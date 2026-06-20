<?php
// app/models/Notificacion.php

defined('BASEPATH') or die('Acceso denegado');

class Notificacion {

    // Crea una notificación dirigida a uno o varios roles ('admin', 'cajero', etc.) o null = todos
    public static function crear(string $tipo, string $titulo, string $mensaje, ?string $paraRol = null): int {
        return Database::insert(
            "INSERT INTO notificaciones (tipo, titulo, mensaje, para_rol) VALUES (?,?,?,?)",
            [$tipo, $titulo, $mensaje, $paraRol]
        );
    }

    // Trae notificaciones relevantes para el rol actual (propias del rol o globales)
    public static function paraRol(string $rol, int $limit = 15): array {
        return Database::fetchAll(
            "SELECT * FROM notificaciones
             WHERE (para_rol = ? OR para_rol IS NULL)
             ORDER BY creado_en DESC LIMIT ?",
            [$rol, $limit]
        );
    }

    public static function noLeidasCount(string $rol): int {
        return Database::fetch(
            "SELECT COUNT(*) c FROM notificaciones WHERE (para_rol = ? OR para_rol IS NULL) AND leida = 0",
            [$rol]
        )['c'] ?? 0;
    }

    public static function marcarLeida(int $id): bool {
        Database::execute("UPDATE notificaciones SET leida = 1 WHERE id = ?", [$id]);
        return true;
    }

    public static function marcarTodasLeidas(string $rol): bool {
        Database::execute(
            "UPDATE notificaciones SET leida = 1 WHERE (para_rol = ? OR para_rol IS NULL)",
            [$rol]
        );
        return true;
    }

    // Limpieza opcional: borra notificaciones leídas con más de 30 días
    public static function limpiarAntiguas(): void {
        Database::execute(
            "DELETE FROM notificaciones WHERE leida = 1 AND creado_en < DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
    }

    // ─────────────────────────────────────────────
    // Helpers específicos para disparar notifs típicas
    // ─────────────────────────────────────────────

    public static function nuevoPedido(int $pedidoId, ?int $mesaNumero): void {
        $mesaTxt = $mesaNumero ? "Mesa #$mesaNumero" : "Para llevar";
        self::crear('pedido', '🛎️ Nuevo Pedido', "$mesaTxt — Pedido #$pedidoId recibido", null);
    }

    public static function nuevaReserva(string $cliente, string $fecha, string $hora): void {
        self::crear('reserva', '📅 Nueva Reservación', "$cliente reservó para el " . date('d/m', strtotime($fecha)) . " a las " . substr($hora,0,5), 'admin');
    }

    public static function nuevaCancion(string $titulo, string $cantante): void {
        self::crear('cola', '🎤 Cola Actualizada', "$cantante agregó \"$titulo\"", 'operador');
    }

    public static function stockBajo(string $producto, int $stock): void {
        self::crear('alerta', '⚠️ Stock Bajo', "\"$producto\" tiene solo $stock unidades", 'admin');
    }
}