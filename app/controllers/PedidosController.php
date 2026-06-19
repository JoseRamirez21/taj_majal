<?php
// app/controllers/PedidosController.php

defined('BASEPATH') or die('Acceso denegado');

class PedidosController {

    public function index(): void {
        $pedidos = Pedido::todos(60);
        $activos = Pedido::activos();
        require_once APP_PATH . '/views/admin/pedidos.php';
    }

    public function nuevo(): void {
        $productos  = Producto::todos();
        $categorias = Producto::categorias();
        $mesas      = Database::fetchAll("SELECT id, numero, estado FROM mesas ORDER BY numero");
        require_once APP_PATH . '/views/admin/pedido_nuevo.php';
    }

    public function guardar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $itemsJson = $_POST['items'] ?? '[]';
        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            echo json_encode(['ok' => false, 'error' => 'El pedido no tiene productos']);
            exit;
        }

        $cabecera = [
            'mesa_id'        => $_POST['mesa_id'] ?? null,
            'cliente_nombre' => trim($_POST['cliente_nombre'] ?? ''),
            'mesero_id'      => Auth::id(),
            'observaciones'  => trim($_POST['observaciones'] ?? ''),
        ];

        try {
            $id = Pedido::crear($cabecera, $items);
            echo json_encode(['ok' => true, 'id' => $id]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'error' => 'Error al guardar el pedido']);
        }
        exit;
    }

    public function cambiarEstado(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id     = (int)($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        if (!$id || !Pedido::cambiarEstado($id, $estado)) {
            echo json_encode(['ok' => false]);
            exit;
        }
        echo json_encode(['ok' => true]);
        exit;
    }

    public function detalle($id): void {
        header('Content-Type: application/json; charset=utf-8');
        $pedido = Pedido::porId((int)$id);
        $detalles = Pedido::detalles((int)$id);
        echo json_encode(['pedido' => $pedido, 'detalles' => $detalles]);
        exit;
    }
}