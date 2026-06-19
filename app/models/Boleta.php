<?php
// app/models/Boleta.php

defined('BASEPATH') or die('Acceso denegado');

class Boleta {

    public static function todas(int $limit = 50): array {
        return Database::fetchAll(
            "SELECT b.*, m.numero AS mesa_numero, u.nombre AS cajero_nombre
             FROM boletas b
             LEFT JOIN mesas m ON m.id = b.mesa_id
             LEFT JOIN usuarios u ON u.id = b.cajero_id
             ORDER BY b.creado_en DESC LIMIT ?",
            [$limit]
        );
    }

    public static function porId(int $id): ?array {
        return Database::fetch(
            "SELECT b.*, m.numero AS mesa_numero, u.nombre AS cajero_nombre
             FROM boletas b LEFT JOIN mesas m ON m.id = b.mesa_id LEFT JOIN usuarios u ON u.id = b.cajero_id
             WHERE b.id = ?", [$id]
        );
    }

    // Pedidos pendientes de cobro agrupados por mesa
    public static function mesasPorCobrar(): array {
        return Database::fetchAll(
            "SELECT m.id AS mesa_id, m.numero, COUNT(p.id) AS num_pedidos, COALESCE(SUM(p.total),0) AS total
             FROM mesas m
             JOIN pedidos p ON p.mesa_id = m.id AND p.estado != 'cancelado'
             WHERE p.creado_en >= (
                 SELECT COALESCE(MAX(pagado_en), '1970-01-01') FROM boletas WHERE mesa_id = m.id
             )
             GROUP BY m.id, m.numero
             HAVING total > 0
             ORDER BY m.numero"
        );
    }

    public static function pedidosDeMesa(int $mesaId): array {
        return Database::fetchAll(
            "SELECT p.* FROM pedidos p
             WHERE p.mesa_id = ? AND p.estado != 'cancelado'
             AND p.creado_en >= (SELECT COALESCE(MAX(pagado_en),'1970-01-01') FROM boletas WHERE mesa_id = ?)
             ORDER BY p.creado_en",
            [$mesaId, $mesaId]
        );
    }

    public static function detalleConsolidado(int $mesaId): array {
        return Database::fetchAll(
            "SELECT pr.nombre, SUM(pd.cantidad) AS cantidad, pd.precio_unitario, SUM(pd.subtotal) AS subtotal
             FROM pedido_detalles pd
             JOIN pedidos p ON p.id = pd.pedido_id
             JOIN productos pr ON pr.id = pd.producto_id
             WHERE p.mesa_id = ? AND p.estado != 'cancelado'
             AND p.creado_en >= (SELECT COALESCE(MAX(pagado_en),'1970-01-01') FROM boletas WHERE mesa_id = ?)
             GROUP BY pd.producto_id, pr.nombre, pd.precio_unitario
             ORDER BY pr.nombre",
            [$mesaId, $mesaId]
        );
    }

    public static function generar(int $mesaId, string $metodoPago, float $descuento = 0): int {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $subtotal = 0;
            foreach (self::pedidosDeMesa($mesaId) as $p) { $subtotal += (float)$p['total']; }

            $igvPorcentaje = (float) (Database::fetch("SELECT valor FROM configuracion WHERE clave='igv_porcentaje'")['valor'] ?? 18);
            $baseImponible = $subtotal - $descuento;
            $igv   = round($baseImponible * ($igvPorcentaje / 100) / (1 + $igvPorcentaje/100), 2);
            $total = $baseImponible;

            $numero = 'B-' . date('Ymd') . '-' . str_pad((string) Database::fetch("SELECT COUNT(*) c FROM boletas WHERE DATE(creado_en)=CURDATE()")['c'] + 1, 4, '0', STR_PAD_LEFT);

            $boletaId = Database::insert(
                "INSERT INTO boletas (numero_boleta, mesa_id, cajero_id, subtotal, descuento, igv, total, metodo_pago, estado, pagado_en)
                 VALUES (?,?,?,?,?,?,?,?,?,NOW())",
                [$numero, $mesaId, Auth::id(), $subtotal, $descuento, $igv, $total, $metodoPago, 'pagada']
            );

            // Marcar pedidos como entregados y liberar mesa
            Database::execute(
                "UPDATE pedidos SET estado='entregado', entregado_en=NOW()
                 WHERE mesa_id=? AND estado IN ('pendiente','en_preparacion','listo')",
                [$mesaId]
            );
            Mesa::cambiarEstado($mesaId, 'libre');

            $db->commit();
            return $boletaId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function anular(int $id): bool {
        Database::execute("UPDATE boletas SET estado='anulada' WHERE id=?", [$id]);
        return true;
    }

    public static function totalHoy(): float {
        return (float) (Database::fetch(
            "SELECT COALESCE(SUM(total),0) t FROM boletas WHERE estado='pagada' AND DATE(pagado_en)=CURDATE()"
        )['t'] ?? 0);
    }

    public static function resumenMetodosPago(): array {
        return Database::fetchAll(
            "SELECT metodo_pago, COUNT(*) cantidad, COALESCE(SUM(total),0) total
             FROM boletas WHERE estado='pagada' AND DATE(pagado_en)=CURDATE()
             GROUP BY metodo_pago"
        );
    }
}