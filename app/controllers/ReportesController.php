<?php
// app/controllers/ReportesController.php

defined('BASEPATH') or die('Acceso denegado');

class ReportesController {

    public function index(): void {
        // Ventas últimos 30 días
        $ventas30 = Database::fetchAll(
            "SELECT DATE(pagado_en) fecha, COALESCE(SUM(total),0) total, COUNT(*) cantidad
             FROM boletas WHERE estado='pagada' AND pagado_en >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
             GROUP BY DATE(pagado_en) ORDER BY fecha ASC"
        );
        $ventasPorDia = [];
        for ($i = 29; $i >= 0; $i--) {
            $f = date('Y-m-d', strtotime("-$i days"));
            $ventasPorDia[$f] = ['total' => 0, 'cantidad' => 0];
        }
        foreach ($ventas30 as $v) {
            $ventasPorDia[$v['fecha']] = ['total' => (float)$v['total'], 'cantidad' => (int)$v['cantidad']];
        }

        // Totales generales
        $resumen = [
            'ventas_mes'    => Database::fetch("SELECT COALESCE(SUM(total),0) t FROM boletas WHERE estado='pagada' AND MONTH(pagado_en)=MONTH(CURDATE()) AND YEAR(pagado_en)=YEAR(CURDATE())")['t'] ?? 0,
            'ventas_totales'=> Database::fetch("SELECT COALESCE(SUM(total),0) t FROM boletas WHERE estado='pagada'")['t'] ?? 0,
            'ticket_promedio'=> Database::fetch("SELECT COALESCE(AVG(total),0) t FROM boletas WHERE estado='pagada'")['t'] ?? 0,
            'total_boletas' => Database::fetch("SELECT COUNT(*) c FROM boletas WHERE estado='pagada'")['c'] ?? 0,
            'total_canciones_cantadas' => Database::fetch("SELECT COUNT(*) c FROM cola_karaoke WHERE estado='completada'")['c'] ?? 0,
            'total_reservas' => Database::fetch("SELECT COUNT(*) c FROM reservaciones WHERE estado != 'cancelada'")['c'] ?? 0,
        ];

        // Productos más vendidos
        $topProductos = Database::fetchAll(
            "SELECT pr.nombre, pr.categoria_id, c.icono, SUM(pd.cantidad) cantidad, SUM(pd.subtotal) total
             FROM pedido_detalles pd
             JOIN productos pr ON pr.id = pd.producto_id
             LEFT JOIN categorias_productos c ON c.id = pr.categoria_id
             JOIN pedidos p ON p.id = pd.pedido_id
             WHERE p.estado != 'cancelado'
             GROUP BY pd.producto_id ORDER BY cantidad DESC LIMIT 8"
        );

        // Ventas por método de pago (histórico)
        $metodosTotales = Database::fetchAll(
            "SELECT metodo_pago, COUNT(*) cantidad, COALESCE(SUM(total),0) total
             FROM boletas WHERE estado='pagada' GROUP BY metodo_pago"
        );

        // Top cantantes (por nombre)
        $topCantantes = Database::fetchAll(
            "SELECT cantante_nombre, COUNT(*) veces
             FROM cola_karaoke WHERE estado='completada' AND cantante_nombre != 'Anónimo'
             GROUP BY cantante_nombre ORDER BY veces DESC LIMIT 6"
        );

        require_once APP_PATH . '/views/admin/reportes.php';
    }

    public function ventas(): void {
        header('Content-Type: application/json; charset=utf-8');
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');

        $data = Database::fetchAll(
            "SELECT DATE(pagado_en) fecha, COALESCE(SUM(total),0) total
             FROM boletas WHERE estado='pagada' AND DATE(pagado_en) BETWEEN ? AND ?
             GROUP BY DATE(pagado_en) ORDER BY fecha",
            [$desde, $hasta]
        );
        echo json_encode($data);
        exit;
    }

    public function canciones(): void {
        header('Content-Type: application/json; charset=utf-8');
        $data = Database::fetchAll(
            "SELECT c.titulo, c.artista, COUNT(ck.id) veces
             FROM cola_karaoke ck JOIN canciones c ON c.id = ck.cancion_id
             GROUP BY ck.cancion_id ORDER BY veces DESC LIMIT 20"
        );
        echo json_encode($data);
        exit;
    }
}