<?php
// app/controllers/CajaController.php

defined('BASEPATH') or die('Acceso denegado');

class CajaController {

    public function index(): void {
        $mesasPorCobrar = Boleta::mesasPorCobrar();
        $boletasHoy     = Boleta::todas(20);
        $totalHoy       = Boleta::totalHoy();
        $resumenPagos   = Boleta::resumenMetodosPago();

        require_once APP_PATH . '/views/admin/caja.php';
    }

    public function cobrar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $mesaId   = (int)($_POST['mesa_id'] ?? 0);
        $metodo   = $_POST['metodo_pago'] ?? 'efectivo';
        $descuento= (float)($_POST['descuento'] ?? 0);

        if (!$mesaId) {
            echo json_encode(['ok' => false, 'error' => 'Mesa inválida']);
            exit;
        }

        try {
            $boletaId = Boleta::generar($mesaId, $metodo, $descuento);
            echo json_encode(['ok' => true, 'boleta_id' => $boletaId]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'error' => 'Error al generar boleta']);
        }
        exit;
    }

    public function boleta($id = null): void {
        $id = (int)($id ?? $_GET['id'] ?? 0);
        $boleta = Boleta::porId($id);

        if (!$boleta) {
            http_response_code(404);
            echo "Boleta no encontrada";
            exit;
        }

        $detalle = Database::fetchAll(
            "SELECT pr.nombre, pd.cantidad, pd.precio_unitario, pd.subtotal
             FROM pedido_detalles pd
             JOIN productos pr ON pr.id = pd.producto_id
             JOIN pedidos p ON p.id = pd.pedido_id
             WHERE p.mesa_id = ? AND p.entregado_en IS NOT NULL
             AND p.entregado_en <= ?
             ORDER BY pr.nombre",
            [$boleta['mesa_id'], $boleta['pagado_en']]
        );

        require_once APP_PATH . '/views/admin/boleta_print.php';
    }
}