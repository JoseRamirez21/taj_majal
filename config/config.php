<?php
// ============================================================
//  config/config.php - Configuración Global del Sistema
// ============================================================

define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('DB_NAME',   'tajmahal_karaoke');
define('DB_CHARSET','utf8mb4');

define('BASE_URL', 'http://localhost/taj_majal/public');
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

define('APP_NAME', 'Taj Mahal Karaoke');
define('APP_VERSION', '1.0.0');
define('SESSION_NAME', 'TAJMAHAL_SESSION');
define('TIMEZONE', 'America/Lima');

date_default_timezone_set(TIMEZONE);

// Autoload básico
spl_autoload_register(function($class) {
    $paths = [
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/'      . $class . '.php',
        APP_PATH . '/helpers/'     . $class . '.php',
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) { require_once $p; return; }
    }
});
