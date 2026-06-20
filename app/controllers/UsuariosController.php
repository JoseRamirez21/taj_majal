<?php
// app/controllers/UsuariosController.php

defined('BASEPATH') or die('Acceso denegado');

class UsuariosController {

    public function index(): void {
        $usuarios = Usuario::todos();
        require_once APP_PATH . '/views/admin/usuarios.php';
    }

    public function guardar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'usuario'  => trim($_POST['usuario'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'rol'      => $_POST['rol'] ?? 'mesero',
            'password' => $_POST['password'] ?? '',
        ];

        if (empty($data['nombre']) || empty($data['usuario'])) {
            echo json_encode(['ok' => false, 'error' => 'Nombre y usuario son obligatorios']);
            exit;
        }

        if (Usuario::existeUsuario($data['usuario'], $id)) {
            echo json_encode(['ok' => false, 'error' => 'Ese nombre de usuario ya existe']);
            exit;
        }

        if (!$id && empty($data['password'])) {
            echo json_encode(['ok' => false, 'error' => 'La contraseña es obligatoria para usuarios nuevos']);
            exit;
        }

        if ($id) {
            Usuario::actualizar($id, $data);
        } else {
            Usuario::crear($data);
        }

        echo json_encode(['ok' => true]);
        exit;
    }

    public function eliminar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id === Auth::id()) {
            echo json_encode(['ok' => false, 'error' => 'No puedes desactivar tu propia cuenta']);
            exit;
        }

        if ($id) Usuario::eliminar($id);
        echo json_encode(['ok' => true]);
        exit;
    }
}
