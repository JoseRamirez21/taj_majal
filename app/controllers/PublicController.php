<?php
// app/controllers/PublicController.php

defined('BASEPATH') or die('Acceso denegado');

class PublicController {

    // Pantalla TV pública - sin login requerido
    public function tv(): void {
        require_once APP_PATH . '/views/public/tv.php';
    }

    // Pantalla pública para pedir canciones vía QR de mesa
    // URL: /pedir/{mesa_id}  ó  /pedir  (sin mesa específica)
    public function pedir($mesaId = null): void {
        $mesaId = $mesaId !== null ? (int)$mesaId : null;
        $mesaNumero = null;

        if ($mesaId) {
            $mesa = Database::fetch("SELECT numero FROM mesas WHERE id = ?", [$mesaId]);
            $mesaNumero = $mesa['numero'] ?? null;
        }

        require_once APP_PATH . '/views/public/pedir.php';
    }
}