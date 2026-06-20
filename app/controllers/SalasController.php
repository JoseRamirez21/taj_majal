<?php
// app/controllers/SalasController.php

defined('BASEPATH') or die('Acceso denegado');

class SalasController {

    public function index(): void {
        $salas = Sala::todas();
        require_once APP_PATH . '/views/admin/salas.php';
    }

    public function guardar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'nombre'      => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'capacidad'   => (int)($_POST['capacidad'] ?? 10),
            'tipo'        => $_POST['tipo'] ?? 'publica',
            'precio_hora' => (float)($_POST['precio_hora'] ?? 0),
        ];

        if (empty($data['nombre'])) {
            echo json_encode(['ok' => false, 'error' => 'El nombre es obligatorio']);
            exit;
        }

        $id ? Sala::actualizar($id, $data) : Sala::crear($data);
        echo json_encode(['ok' => true]);
        exit;
    }
}
