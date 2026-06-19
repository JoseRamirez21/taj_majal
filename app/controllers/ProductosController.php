<?php
// app/controllers/ProductosController.php

defined('BASEPATH') or die('Acceso denegado');

class ProductosController {

    public function index(): void {
        $productos  = Producto::todos();
        $categorias = Producto::categorias();
        $stockBajo  = Producto::stockBajo();
        require_once APP_PATH . '/views/admin/productos.php';
    }

    public function guardar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_POST['id'] ?? 0);

        $data = [
            'categoria_id' => $_POST['categoria_id'] ?? null,
            'nombre'       => trim($_POST['nombre'] ?? ''),
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
            'precio'       => (float)($_POST['precio'] ?? 0),
            'stock'        => (int)($_POST['stock'] ?? 0),
            'destacado'    => isset($_POST['destacado']) ? 1 : 0,
        ];

        if (empty($data['nombre']) || $data['precio'] <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Nombre y precio válido son obligatorios']);
            exit;
        }

        $id ? Producto::actualizar($id, $data) : Producto::crear($data);
        echo json_encode(['ok' => true]);
        exit;
    }

    public function eliminar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) Producto::eliminar($id);
        echo json_encode(['ok' => true]);
        exit;
    }
}