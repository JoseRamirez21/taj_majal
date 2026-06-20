<?php
// app/controllers/ReservacionesController.php

defined('BASEPATH') or die('Acceso denegado');

class ReservacionesController {

    public function index(): void {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $reservasDelDia = Reservacion::porFecha($fecha);
        $proximas       = Reservacion::proximas(15);
        $salas          = Database::fetchAll("SELECT id, nombre FROM salas WHERE activa = 1 ORDER BY nombre");
        $mesas          = Database::fetchAll("SELECT id, numero FROM mesas ORDER BY numero");

        require_once APP_PATH . '/views/admin/reservaciones.php';
    }

    public function crear(): void {
        header('Location: ' . BASE_URL . '/reservaciones');
        exit;
    }

    public function guardar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $data = [
            'sala_id'          => $_POST['sala_id'] ?? null,
            'mesa_id'          => $_POST['mesa_id'] ?? null,
            'cliente_nombre'   => trim($_POST['cliente_nombre'] ?? ''),
            'cliente_telefono' => trim($_POST['cliente_telefono'] ?? ''),
            'cliente_email'    => trim($_POST['cliente_email'] ?? ''),
            'fecha'            => $_POST['fecha'] ?? '',
            'hora_inicio'      => $_POST['hora_inicio'] ?? '',
            'hora_fin'         => $_POST['hora_fin'] ?? '',
            'n_personas'       => (int)($_POST['n_personas'] ?? 1),
            'observaciones'    => trim($_POST['observaciones'] ?? ''),
            'monto_anticipado' => (float)($_POST['monto_anticipado'] ?? 0),
        ];

        if (empty($data['cliente_nombre']) || empty($data['fecha']) || empty($data['hora_inicio'])) {
            echo json_encode(['ok' => false, 'error' => 'Completa los campos obligatorios']);
            exit;
        }

        $id = Reservacion::crear($data);
        echo json_encode(['ok' => true, 'id' => $id]);
        exit;
    }

    public function cambiarEstado(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id     = (int)($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        if (!$id || !Reservacion::cambiarEstado($id, $estado)) {
            echo json_encode(['ok' => false]);
            exit;
        }
        echo json_encode(['ok' => true]);
        exit;
    }
}
