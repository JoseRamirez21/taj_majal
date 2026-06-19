<?php
// app/controllers/ColaController.php

defined('BASEPATH') or die('Acceso denegado');

class ColaController {

    public function index(): void {
        $cantandoAhora = ColaKaraoke::cantandoAhora();
        $enEspera      = ColaKaraoke::enEspera();
        $historial     = ColaKaraoke::historial(8);
        $canciones     = Cancion::todas(60);
        $mesas         = Database::fetchAll("SELECT id, numero FROM mesas ORDER BY numero");
        $generos       = Cancion::generos();

        require_once APP_PATH . '/views/admin/cola.php';
    }

    public function agregar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->json(['error' => 'Método inválido']); return; }

        $cancionId = (int)($_POST['cancion_id'] ?? 0);
        if (!$cancionId) { $this->json(['error' => 'Selecciona una canción']); return; }

        $id = ColaKaraoke::agregar([
            'cancion_id'      => $cancionId,
            'cantante_nombre' => trim($_POST['cantante_nombre'] ?? '') ?: 'Anónimo',
            'mesa_id'         => $_POST['mesa_id'] ?? null,
            'sala_id'         => $_POST['sala_id'] ?? null,
            'nota_publica'    => trim($_POST['nota_publica'] ?? ''),
        ]);

        $this->json(['ok' => true, 'id' => $id, 'mensaje' => '¡Agregado a la cola!']);
    }

    public function siguiente(): void {
        $proxima = ColaKaraoke::siguiente();
        $this->json(['ok' => true, 'proxima' => $proxima]);
    }

    public function saltar(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) ColaKaraoke::saltar($id);
        $this->json(['ok' => true]);
    }

    public function puntuar(): void {
        $colaId  = (int)($_POST['cola_id'] ?? 0);
        $puntos  = (int)($_POST['puntos'] ?? 5);
        $aplauso = !empty($_POST['aplauso']);
        if ($colaId) ColaKaraoke::agregarPuntuacion($colaId, max(1, min(10, $puntos)), $aplauso);
        $this->json(['ok' => true]);
    }

    public function estado(): void {
        $this->estadoAjax();
    }

    public function estadoAjax(): void {
        $this->json([
            'cantando_ahora' => ColaKaraoke::cantandoAhora(),
            'en_espera'      => ColaKaraoke::enEspera(),
            'total_espera'   => ColaKaraoke::contarEnEspera(),
        ]);
    }

    public function buscar(): void {
        // proxy reusado en CancionesController; aquí permite buscar canciones desde la cola
        $q = trim($_GET['q'] ?? '');
        $resultados = $q !== '' ? Cancion::buscar($q) : Cancion::todas(30);
        $this->json($resultados);
    }

    private function json($data): void {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}