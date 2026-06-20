<?php
// app/controllers/MesasController.php

defined('BASEPATH') or die('Acceso denegado');

class MesasController {

    public function index(): void {
        $mesas   = Mesa::todas();
        $salas   = Database::fetchAll("SELECT id, nombre FROM salas WHERE activa = 1 ORDER BY nombre");
        $resumen = Mesa::resumenEstados();

        require_once APP_PATH . '/views/admin/mesas.php';
    }

    public function cambiarEstado(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id     = (int)($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? '';

        if (!$id || !Mesa::cambiarEstado($id, $estado)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
            exit;
        }
        echo json_encode(['ok' => true]);
        exit;
    }

    public function guardar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_POST['id'] ?? 0);

        $data = [
            'sala_id'   => $_POST['sala_id'] ?? null,
            'numero'    => (int)($_POST['numero'] ?? 0),
            'capacidad' => (int)($_POST['capacidad'] ?? 4),
        ];

        if (!$data['numero']) {
            echo json_encode(['ok' => false, 'error' => 'El número de mesa es obligatorio']);
            exit;
        }

        if ($id) {
            Mesa::actualizar($id, $data);
        } else {
            $id = Mesa::crear($data);
        }

        echo json_encode(['ok' => true, 'id' => $id]);
        exit;
    }

    public function estadoAjax(): void {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(Mesa::todas());
        exit;
    }
    public function qrCodes(): void {
    $mesas = Mesa::todas();
    require_once APP_PATH . '/views/admin/mesas_qr.php';
}
}