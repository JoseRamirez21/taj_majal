<?php
// app/models/Pedido.php

defined('BASEPATH') or die('Acceso denegado');

class Pedido {

    public static function todos(int $limit = 50): array {
        return Database::fetchAll(
            "SELECT p.*, m.numero AS mesa_numero, u.nombre AS mesero_nombre
             FROM pedidos p
             LEFT JOIN mesas m ON m.id = p.mesa_id
             LEFT JOIN usuarios u ON u.id = p.mesero_id
             ORDER BY p.creado_en DESC LIMIT ?",
            [$limit]
        );
    }

    public static function activos(): array {
        return Database::fetchAll(
            "SELECT p.*, m.numero AS mesa_numero, u.nombre AS mesero_nombre
             FROM pedidos p
             LEFT JOIN mesas m ON m.id = p.mesa_id
             LEFT JOIN usuarios u ON u.id = p.mesero_id
             WHERE p.estado IN ('pendiente','en_preparacion','listo')
             ORDER BY p.creado_en ASC"
        );
    }

    public static function porId(int $id): ?array {
        return Database::fetch(
            "SELECT p.*, m.numero AS mesa_numero FROM pedidos p LEFT JOIN mesas m ON m.id = p.mesa_id WHERE p.id = ?",
            [$id]
        );
    }

    public static function detalles(int $pedidoId): array {
        return Database::fetchAll(
            "SELECT pd.*, pr.nombre AS producto_nombre
             FROM pedido_detalles pd JOIN productos pr ON pr.id = pd.producto_id
             WHERE pd.pedido_id = ?",
            [$pedidoId]
        );
    }

    public static function crear(array $cabecera, array $items): int {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $pedidoId = Database::insert(
                "INSERT INTO pedidos (mesa_id, cliente_nombre, mesero_id, observaciones, total)
                 VALUES (?,?,?,?,0)",
                [$cabecera['mesa_id'] ?: null, $cabecera['cliente_nombre'] ?? '', $cabecera['mesero_id'], $cabecera['observaciones'] ?? '']
            );

            $total = 0;
            foreach ($items as $it) {
                $subtotal = $it['precio'] * $it['cantidad'];
                $total += $subtotal;
                Database::insert(
                    "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario, subtotal, nota)
                     VALUES (?,?,?,?,?,?)",
                    [$pedidoId, $it['producto_id'], $it['cantidad'], $it['precio'], $subtotal, $it['nota'] ?? '']
                );
                Producto::descontarStock($it['producto_id'], $it['cantidad']);
            }

            Database::execute("UPDATE pedidos SET total = ? WHERE id = ?", [$total, $pedidoId]);

            if (!empty($cabecera['mesa_id'])) {
                Mesa::cambiarEstado((int)$cabecera['mesa_id'], 'ocupada');
            }

            $db->commit();

            $mesaNum = !empty($cabecera['mesa_id']) ? Database::fetch("SELECT numero FROM mesas WHERE id=?", [$cabecera['mesa_id']])['numero'] ?? null : null;
            Notificacion::nuevoPedido($pedidoId, $mesaNum);

            return $pedidoId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function cambiarEstado(int $id, string $estado): bool {
        $validos = ['pendiente','en_preparacion','listo','entregado','cancelado'];
        if (!in_array($estado, $validos)) return false;
        $extra = $estado === 'entregado' ? ", entregado_en = NOW()" : "";
        Database::execute("UPDATE pedidos SET estado = ? $extra WHERE id = ?", [$estado, $id]);
        return true;
    }

    public static function totalPorMesa(int $mesaId): float {
        return (float) (Database::fetch(
            "SELECT COALESCE(SUM(total),0) t FROM pedidos WHERE mesa_id = ? AND estado != 'cancelado'
             AND creado_en >= (SELECT COALESCE(MAX(pagado_en),'1970-01-01') FROM boletas WHERE mesa_id = ?)",
            [$mesaId, $mesaId]
        )['t'] ?? 0);
    }
}