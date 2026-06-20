<?php
// app/controllers/ConfigController.php

defined('BASEPATH') or die('Acceso denegado');

class ConfigController {

    public function index(): void {
        $configRows = Database::fetchAll("SELECT * FROM configuracion ORDER BY clave");
        $config = [];
        foreach ($configRows as $row) { $config[$row['clave']] = $row['valor']; }

        require_once APP_PATH . '/views/admin/configuracion.php';
    }

    public function guardar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $campos = [
            'nombre_bar','direccion','telefono','email','igv_porcentaje','moneda','simbolo_moneda',
            'hora_apertura','hora_cierre','max_canciones_cola','tiempo_rotacion_min',
            'color_primario','facebook','instagram'
        ];

        foreach ($campos as $campo) {
            if (isset($_POST[$campo])) {
                Database::execute(
                    "INSERT INTO configuracion (clave, valor) VALUES (?,?)
                     ON DUPLICATE KEY UPDATE valor = VALUES(valor)",
                    [$campo, trim($_POST[$campo])]
                );
            }
        }

        echo json_encode(['ok' => true]);
        exit;
    }
}