<?php
// app/models/Usuario.php

defined('BASEPATH') or die('Acceso denegado');

class Usuario {

    public static function todos(): array {
        return Database::fetchAll("SELECT * FROM usuarios ORDER BY nombre");
    }

    public static function porId(int $id): ?array {
        return Database::fetch("SELECT * FROM usuarios WHERE id = ?", [$id]);
    }

    public static function porUsuario(string $usuario): ?array {
        return Database::fetch("SELECT * FROM usuarios WHERE usuario = ?", [$usuario]);
    }

    public static function crear(array $d): int {
        return Database::insert(
            "INSERT INTO usuarios (nombre, usuario, password, email, telefono, rol) VALUES (?,?,?,?,?,?)",
            [$d['nombre'], $d['usuario'], password_hash($d['password'], PASSWORD_DEFAULT),
             $d['email'] ?? '', $d['telefono'] ?? '', $d['rol'] ?? 'mesero']
        );
    }

    public static function actualizar(int $id, array $d): bool {
        if (!empty($d['password'])) {
            Database::execute(
                "UPDATE usuarios SET nombre=?, email=?, telefono=?, rol=?, password=? WHERE id=?",
                [$d['nombre'], $d['email'] ?? '', $d['telefono'] ?? '', $d['rol'] ?? 'mesero',
                 password_hash($d['password'], PASSWORD_DEFAULT), $id]
            );
        } else {
            Database::execute(
                "UPDATE usuarios SET nombre=?, email=?, telefono=?, rol=? WHERE id=?",
                [$d['nombre'], $d['email'] ?? '', $d['telefono'] ?? '', $d['rol'] ?? 'mesero', $id]
            );
        }
        return true;
    }

    public static function cambiarActivo(int $id, bool $activo): bool {
        Database::execute("UPDATE usuarios SET activo = ? WHERE id = ?", [$activo ? 1 : 0, $id]);
        return true;
    }

    public static function eliminar(int $id): bool {
        Database::execute("UPDATE usuarios SET activo = 0 WHERE id = ?", [$id]);
        return true;
    }

    public static function existeUsuario(string $usuario, int $excluirId = 0): bool {
        $row = Database::fetch("SELECT id FROM usuarios WHERE usuario = ? AND id != ?", [$usuario, $excluirId]);
        return $row !== null;
    }
}
