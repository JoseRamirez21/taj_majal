<?php
// app/models/ColaKaraoke.php

defined('BASEPATH') or die('Acceso denegado');

class ColaKaraoke {

    public static function enEspera(): array {
        return Database::fetchAll(
            "SELECT ck.*, c.titulo, c.artista, c.duracion_seg, c.youtube_id, c.portada, m.numero AS mesa_numero
             FROM cola_karaoke ck
             JOIN canciones c ON c.id = ck.cancion_id
             LEFT JOIN mesas m ON m.id = ck.mesa_id
             WHERE ck.estado = 'en_espera'
             ORDER BY ck.solicitado_en ASC"
        );
    }

    public static function cantandoAhora(): ?array {
        return Database::fetch(
            "SELECT ck.*, c.titulo, c.artista, c.duracion_seg, c.youtube_id, c.portada, m.numero AS mesa_numero
             FROM cola_karaoke ck
             JOIN canciones c ON c.id = ck.cancion_id
             LEFT JOIN mesas m ON m.id = ck.mesa_id
             WHERE ck.estado = 'cantando' LIMIT 1"
        );
    }

    public static function historial(int $limit = 20): array {
        return Database::fetchAll(
            "SELECT ck.*, c.titulo, c.artista,
                    (SELECT COALESCE(AVG(puntos),0) FROM puntuaciones WHERE cola_id = ck.id) AS promedio_puntos,
                    (SELECT COUNT(*) FROM puntuaciones WHERE cola_id = ck.id AND aplauso=1) AS aplausos
             FROM cola_karaoke ck
             JOIN canciones c ON c.id = ck.cancion_id
             WHERE ck.estado = 'completada'
             ORDER BY ck.finalizado_en DESC LIMIT ?",
            [$limit]
        );
    }

    public static function agregar(array $d): int {
        $maxPos = Database::fetch("SELECT COALESCE(MAX(posicion),0) m FROM cola_karaoke WHERE estado='en_espera'")['m'] ?? 0;
        return Database::insert(
            "INSERT INTO cola_karaoke (sala_id, mesa_id, cancion_id, cantante_nombre, posicion, nota_publica)
             VALUES (?,?,?,?,?,?)",
            [$d['sala_id'] ?? null, $d['mesa_id'] ?? null, $d['cancion_id'], $d['cantante_nombre'] ?: 'Anónimo', $maxPos + 1, $d['nota_publica'] ?? null]
        );
    }

    public static function marcarCantando(int $id): bool {
        // Finalizar cualquier otra que esté "cantando"
        Database::execute("UPDATE cola_karaoke SET estado='completada', finalizado_en=NOW() WHERE estado='cantando'");
        Database::execute("UPDATE cola_karaoke SET estado='cantando', iniciado_en=NOW() WHERE id=?", [$id]);
        return true;
    }

    public static function siguiente(): ?array {
        // Finaliza la actual y activa la próxima en espera
        Database::execute("UPDATE cola_karaoke SET estado='completada', finalizado_en=NOW() WHERE estado='cantando'");
        $proxima = Database::fetch(
            "SELECT * FROM cola_karaoke WHERE estado='en_espera' ORDER BY posicion ASC, solicitado_en ASC LIMIT 1"
        );
        if ($proxima) {
            Database::execute("UPDATE cola_karaoke SET estado='cantando', iniciado_en=NOW() WHERE id=?", [$proxima['id']]);
        }
        return $proxima;
    }

    public static function saltar(int $id): bool {
        Database::execute("UPDATE cola_karaoke SET estado='saltada', finalizado_en=NOW() WHERE id=?", [$id]);
        return true;
    }

    public static function eliminarDeCola(int $id): bool {
        Database::execute("DELETE FROM cola_karaoke WHERE id=? AND estado='en_espera'", [$id]);
        return true;
    }

    public static function reordenar(array $idsOrdenados): bool {
        foreach ($idsOrdenados as $pos => $id) {
            Database::execute("UPDATE cola_karaoke SET posicion=? WHERE id=?", [$pos + 1, (int)$id]);
        }
        return true;
    }

    public static function contarEnEspera(): int {
        return Database::fetch("SELECT COUNT(*) c FROM cola_karaoke WHERE estado='en_espera'")['c'] ?? 0;
    }

    public static function agregarPuntuacion(int $colaId, int $puntos, bool $aplauso = false): int {
        return Database::insert(
            "INSERT INTO puntuaciones (cola_id, puntos, aplauso) VALUES (?,?,?)",
            [$colaId, $puntos, $aplauso ? 1 : 0]
        );
    }
}