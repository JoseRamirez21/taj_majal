<?php
// public/index.php — Punto de entrada único del sistema Taj Mahal

define('BASEPATH', true); // Seguridad: evita acceso directo a archivos internos

require_once dirname(__DIR__) . '/config/config.php';
require_once APP_PATH . '/helpers/Database.php';
require_once APP_PATH . '/helpers/Auth.php';
require_once APP_PATH . '/helpers/Router.php';

// Iniciar sesión con nombre personalizado
session_name(SESSION_NAME);
session_start();

// Registrar último acceso si hay sesión activa
if (isset($_SESSION['usuario_id'])) {
    // Solo actualiza cada 5 minutos para no sobrecargar la BD
    if (!isset($_SESSION['last_activity_update']) || 
        time() - $_SESSION['last_activity_update'] > 300) {
        Database::execute(
            "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?",
            [$_SESSION['usuario_id']]
        );
        $_SESSION['last_activity_update'] = time();
    }
}

// Obtener la URL limpia
$url = $_GET['url'] ?? 'dashboard';
$url = trim(rtrim($url, '/'));

// Lanzar el router
$router = new Router();
$router->dispatch($url);
