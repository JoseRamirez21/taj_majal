<?php
// app/controllers/CancionesController.php

defined('BASEPATH') or die('Acceso denegado');

class CancionesController {

    public function index(): void {
        $q = trim($_GET['q'] ?? '');
        $canciones = $q !== '' ? Cancion::buscar($q) : Cancion::todas(200);
        $generos   = Cancion::generos();
        $total     = Cancion::contarTotal();

        require_once APP_PATH . '/views/admin/canciones.php';
    }

    public function crear(): void {
        // Se maneja vía modal en la misma vista index, este método queda por compatibilidad de ruta
        header('Location: ' . BASE_URL . '/canciones');
        exit;
    }

    public function guardar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'titulo'       => trim($_POST['titulo'] ?? ''),
            'artista'      => trim($_POST['artista'] ?? ''),
            'album'        => trim($_POST['album'] ?? ''),
            'anio'         => $_POST['anio'] ?? null,
            'genero'       => trim($_POST['genero'] ?? ''),
            'idioma'       => trim($_POST['idioma'] ?? 'Español'),
            'duracion_seg' => (int)($_POST['duracion_seg'] ?? 0),
            'codigo'       => trim($_POST['codigo'] ?? '') ?: null,
            'youtube_id'   => trim($_POST['youtube_id'] ?? '') ?: null,
        ];

        if (empty($data['titulo']) || empty($data['artista'])) {
            echo json_encode(['ok' => false, 'error' => 'Título y artista son obligatorios']);
            exit;
        }

        try {
            if ($id) {
                Cancion::actualizar($id, $data);
            } else {
                $id = Cancion::crear($data);
            }
            echo json_encode(['ok' => true, 'id' => $id]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'error' => 'El código ya existe o hubo un error']);
        }
        exit;
    }

    public function editar($id = null): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($id ?? $_GET['id'] ?? 0);
        $cancion = Cancion::porId($id);
        echo json_encode($cancion ?: ['error' => 'No encontrada']);
        exit;
    }

    public function eliminar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) Cancion::eliminar($id);
        echo json_encode(['ok' => true]);
        exit;
    }

    public function buscar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $q = trim($_GET['q'] ?? '');
        $resultados = $q !== '' ? Cancion::buscar($q) : Cancion::todas(30);
        echo json_encode($resultados);
        exit;
    }
}
