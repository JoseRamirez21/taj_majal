<?php
// app/models/Reservacion.php

defined('BASEPATH') or die('Acceso denegado');

class Reservacion {

    public static function todas(int $limit = 100): array {
        return Database::fetchAll(
            "SELECT r.*, s.nombre AS sala_nombre, m.numero AS mesa_numero
             FROM reservaciones r
             LEFT JOIN salas s ON s.id = r.sala_id
             LEFT JOIN mesas m ON m.id = r.mesa_id
             ORDER BY r.fecha ASC, r.hora_inicio ASC LIMIT ?",
            [$limit]
        );
    }

    public static function porFecha(string $fecha): array {
        return Database::fetchAll(
            "SELECT r.*, s.nombre AS sala_nombre, m.numero AS mesa_numero
             FROM reservaciones r
             LEFT JOIN salas s ON s.id = r.sala_id
             LEFT JOIN mesas m ON m.id = r.mesa_id
             WHERE r.fecha = ? AND r.estado != 'cancelada'
             ORDER BY r.hora_inicio ASC",
            [$fecha]
        );
    }

    public static function proximas(int $limit = 10): array {
        return Database::fetchAll(
            "SELECT r.*, s.nombre AS sala_nombre, m.numero AS mesa_numero
             FROM reservaciones r
             LEFT JOIN salas s ON s.id = r.sala_id
             LEFT JOIN mesas m ON m.id = r.mesa_id
             WHERE r.fecha >= CURDATE() AND r.estado IN ('pendiente','confirmada')
             ORDER BY r.fecha ASC, r.hora_inicio ASC LIMIT ?",
            [$limit]
        );
    }

    public static function porId(int $id): ?array {
        return Database::fetch("SELECT * FROM reservaciones WHERE id = ?", [$id]);
    }

    public static function crear(array $d): int {
        $id = Database::insert(
            "INSERT INTO reservaciones (sala_id, mesa_id, cliente_nombre, cliente_telefono, cliente_email,
                fecha, hora_inicio, hora_fin, n_personas, observaciones, monto_anticipado, creado_por)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            [$d['sala_id'] ?: null, $d['mesa_id'] ?: null, $d['cliente_nombre'], $d['cliente_telefono'] ?? '',
             $d['cliente_email'] ?? '', $d['fecha'], $d['hora_inicio'], $d['hora_fin'], $d['n_personas'] ?? 1,
             $d['observaciones'] ?? '', $d['monto_anticipado'] ?? 0, Auth::id()]
        );
        Notificacion::nuevaReserva($d['cliente_nombre'], $d['fecha'], $d['hora_inicio']);
        return $id;
    }

    public static function cambiarEstado(int $id, string $estado): bool {
        $validos = ['pendiente','confirmada','cancelada','completada'];
        if (!in_array($estado, $validos)) return false;
        Database::execute("UPDATE reservaciones SET estado = ? WHERE id = ?", [$estado, $id]);

        // Si se confirma y tiene mesa asignada, marcarla como reservada
        if ($estado === 'confirmada') {
            $r = self::porId($id);
            if ($r && $r['mesa_id']) Mesa::cambiarEstado((int)$r['mesa_id'], 'reservada');
        }
        return true;
    }

    public static function eliminar(int $id): bool {
        Database::execute("DELETE FROM reservaciones WHERE id = ?", [$id]);
        return true;
    }

    public static function contarHoy(): int {
        return Database::fetch(
            "SELECT COUNT(*) c FROM reservaciones WHERE fecha = CURDATE() AND estado != 'cancelada'"
        )['c'] ?? 0;
    }
}
