<?php
// app/models/Sala.php

defined('BASEPATH') or die('Acceso denegado');

class Sala {

    public static function todas(): array {
        return Database::fetchAll(
            "SELECT s.*,
                (SELECT COUNT(*) FROM mesas WHERE sala_id = s.id) AS total_mesas,
                (SELECT COUNT(*) FROM mesas WHERE sala_id = s.id AND estado='ocupada') AS mesas_ocupadas
             FROM salas s ORDER BY s.nombre"
        );
    }

    public static function activas(): array {
        return Database::fetchAll("SELECT * FROM salas WHERE activa = 1 ORDER BY nombre");
    }

    public static function porId(int $id): ?array {
        return Database::fetch("SELECT * FROM salas WHERE id = ?", [$id]);
    }

    public static function crear(array $d): int {
        return Database::insert(
            "INSERT INTO salas (nombre, descripcion, capacidad, tipo, precio_hora) VALUES (?,?,?,?,?)",
            [$d['nombre'], $d['descripcion'] ?? '', $d['capacidad'] ?? 10, $d['tipo'] ?? 'publica', $d['precio_hora'] ?? 0]
        );
    }

    public static function actualizar(int $id, array $d): bool {
        Database::execute(
            "UPDATE salas SET nombre=?, descripcion=?, capacidad=?, tipo=?, precio_hora=? WHERE id=?",
            [$d['nombre'], $d['descripcion'] ?? '', $d['capacidad'] ?? 10, $d['tipo'] ?? 'publica', $d['precio_hora'] ?? 0, $id]
        );
        return true;
    }

    public static function eliminar(int $id): bool {
        Database::execute("UPDATE salas SET activa = 0 WHERE id = ?", [$id]);
        return true;
    }
}
