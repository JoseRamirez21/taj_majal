<?php
// app/models/Mesa.php

defined('BASEPATH') or die('Acceso denegado');

class Mesa {

    public static function todas(): array {
        return Database::fetchAll(
            "SELECT m.*, s.nombre AS sala_nombre, s.tipo AS sala_tipo
             FROM mesas m LEFT JOIN salas s ON s.id = m.sala_id
             ORDER BY m.numero ASC"
        );
    }

    public static function porId(int $id): ?array {
        return Database::fetch(
            "SELECT m.*, s.nombre AS sala_nombre FROM mesas m LEFT JOIN salas s ON s.id = m.sala_id WHERE m.id = ?",
            [$id]
        );
    }

    public static function porSala(int $salaId): array {
        return Database::fetchAll("SELECT * FROM mesas WHERE sala_id = ? ORDER BY numero", [$salaId]);
    }

    public static function cambiarEstado(int $id, string $estado): bool {
        $validos = ['libre','ocupada','reservada','mantenimiento'];
        if (!in_array($estado, $validos)) return false;
        Database::execute("UPDATE mesas SET estado = ? WHERE id = ?", [$estado, $id]);
        return true;
    }

    public static function crear(array $d): int {
        return Database::insert(
            "INSERT INTO mesas (sala_id, numero, capacidad, estado) VALUES (?,?,?,?)",
            [$d['sala_id'] ?: null, $d['numero'], $d['capacidad'] ?? 4, $d['estado'] ?? 'libre']
        );
    }

    public static function actualizar(int $id, array $d): bool {
        Database::execute(
            "UPDATE mesas SET sala_id=?, numero=?, capacidad=? WHERE id=?",
            [$d['sala_id'] ?: null, $d['numero'], $d['capacidad'] ?? 4, $id]
        );
        return true;
    }

    public static function eliminar(int $id): bool {
        Database::execute("DELETE FROM mesas WHERE id = ?", [$id]);
        return true;
    }

    public static function resumenEstados(): array {
        $r = Database::fetchAll("SELECT estado, COUNT(*) c FROM mesas GROUP BY estado");
        $out = ['libre'=>0,'ocupada'=>0,'reservada'=>0,'mantenimiento'=>0];
        foreach ($r as $row) { $out[$row['estado']] = (int)$row['c']; }
        return $out;
    }

    // Cuenta cuánto lleva ocupada una mesa (para mostrar tiempo en uso)
    public static function tiempoOcupada(int $id): ?string {
        $ped = Database::fetch(
            "SELECT MIN(creado_en) inicio FROM pedidos WHERE mesa_id = ? AND estado != 'cancelado'
             AND creado_en >= (SELECT COALESCE(MAX(pagado_en), '1970-01-01') FROM boletas WHERE mesa_id = ?)",
            [$id, $id]
        );
        return $ped['inicio'] ?? null;
    }
}