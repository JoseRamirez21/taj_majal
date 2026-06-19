<?php
// app/models/Producto.php

defined('BASEPATH') or die('Acceso denegado');

class Producto {

    public static function todos(): array {
        return Database::fetchAll(
            "SELECT p.*, c.nombre AS categoria_nombre, c.icono AS categoria_icono, c.color AS categoria_color
             FROM productos p LEFT JOIN categorias_productos c ON c.id = p.categoria_id
             WHERE p.activo = 1
             ORDER BY c.orden ASC, p.nombre ASC"
        );
    }

    public static function porCategoria(int $catId): array {
        return Database::fetchAll(
            "SELECT * FROM productos WHERE categoria_id = ? AND activo = 1 ORDER BY nombre", [$catId]
        );
    }

    public static function porId(int $id): ?array {
        return Database::fetch("SELECT * FROM productos WHERE id = ?", [$id]);
    }

    public static function categorias(): array {
        return Database::fetchAll("SELECT * FROM categorias_productos WHERE activa = 1 ORDER BY orden");
    }

    public static function crear(array $d): int {
        return Database::insert(
            "INSERT INTO productos (categoria_id, nombre, descripcion, precio, precio_costo, stock, stock_minimo, destacado)
             VALUES (?,?,?,?,?,?,?,?)",
            [$d['categoria_id'] ?: null, $d['nombre'], $d['descripcion'] ?? '', $d['precio'], $d['precio_costo'] ?? 0,
             $d['stock'] ?? 0, $d['stock_minimo'] ?? 5, $d['destacado'] ?? 0]
        );
    }

    public static function actualizar(int $id, array $d): bool {
        Database::execute(
            "UPDATE productos SET categoria_id=?, nombre=?, descripcion=?, precio=?, stock=?, destacado=? WHERE id=?",
            [$d['categoria_id'] ?: null, $d['nombre'], $d['descripcion'] ?? '', $d['precio'], $d['stock'] ?? 0, $d['destacado'] ?? 0, $id]
        );
        return true;
    }

    public static function eliminar(int $id): bool {
        Database::execute("UPDATE productos SET activo = 0 WHERE id = ?", [$id]);
        return true;
    }

    public static function descontarStock(int $id, int $cantidad): bool {
        Database::execute("UPDATE productos SET stock = GREATEST(0, stock - ?) WHERE id = ?", [$cantidad, $id]);
        return true;
    }

    public static function stockBajo(): array {
        return Database::fetchAll("SELECT * FROM productos WHERE activo=1 AND stock <= stock_minimo ORDER BY stock ASC");
    }
}