<?php
// app/helpers/Router.php

defined('BASEPATH') or die('Acceso denegado');

class Router {

    // Mapa de rutas: 'url' => ['controller' => X, 'method' => Y, 'auth' => true/false, 'roles' => []]
    private array $routes = [

        // ── PÚBLICAS ──────────────────────────────────────────
        'login'             => ['controller' => 'AuthController',        'method' => 'login',          'auth' => false],
        'login/procesar'    => ['controller' => 'AuthController',        'method' => 'procesar',       'auth' => false],
        'logout'            => ['controller' => 'AuthController',        'method' => 'logout',         'auth' => false],
        'tv'                => ['controller' => 'PublicController',      'method' => 'tv',             'auth' => false],
        'pedir'             => ['controller' => 'PublicController',      'method' => 'pedir',           'auth' => false],


        // ── DASHBOARD ─────────────────────────────────────────
        ''                  => ['controller' => 'DashboardController',   'method' => 'index',          'auth' => true],
        'dashboard'         => ['controller' => 'DashboardController',   'method' => 'index',          'auth' => true],

        // ── CANCIONES ─────────────────────────────────────────
        'canciones'         => ['controller' => 'CancionesController',   'method' => 'index',          'auth' => true],
        'canciones/crear'   => ['controller' => 'CancionesController',   'method' => 'crear',          'auth' => true, 'roles' => ['admin','operador']],
        'canciones/guardar' => ['controller' => 'CancionesController',   'method' => 'guardar',        'auth' => true, 'roles' => ['admin','operador']],
        'canciones/editar'  => ['controller' => 'CancionesController',   'method' => 'editar',         'auth' => true, 'roles' => ['admin','operador']],
        'canciones/eliminar'=> ['controller' => 'CancionesController',   'method' => 'eliminar',       'auth' => true, 'roles' => ['admin']],
        'canciones/buscar'  => ['controller' => 'CancionesController',   'method' => 'buscar',         'auth' => true],

        // ── COLA KARAOKE ───────────────────────────────────────
        'cola'              => ['controller' => 'ColaController',        'method' => 'index',          'auth' => true],
        'cola/agregar'      => ['controller' => 'ColaController',        'method' => 'agregar',        'auth' => false],
        'cola/siguiente'    => ['controller' => 'ColaController',        'method' => 'siguiente',      'auth' => true, 'roles' => ['admin','operador']],
        'cola/saltar'       => ['controller' => 'ColaController',        'method' => 'saltar',         'auth' => true, 'roles' => ['admin','operador']],
        'cola/estado'       => ['controller' => 'ColaController',        'method' => 'estado',         'auth' => true],
        'cola/puntuar'      => ['controller' => 'ColaController',        'method' => 'puntuar',        'auth' => true],
        'cola/buscar'       => ['controller' => 'ColaController',        'method' => 'buscar',         'auth' => false],

        // ── MESAS ─────────────────────────────────────────────
        'mesas'             => ['controller' => 'MesasController',       'method' => 'index',          'auth' => true],
        'mesas/estado'      => ['controller' => 'MesasController',       'method' => 'cambiarEstado',  'auth' => true],
        'mesas/guardar'     => ['controller' => 'MesasController',       'method' => 'guardar',        'auth' => true, 'roles' => ['admin']],

        // ── SALAS ─────────────────────────────────────────────
        'salas'             => ['controller' => 'SalasController',       'method' => 'index',          'auth' => true, 'roles' => ['admin']],
        'salas/guardar'     => ['controller' => 'SalasController',       'method' => 'guardar',        'auth' => true, 'roles' => ['admin']],

        // ── RESERVACIONES ─────────────────────────────────────
        'reservaciones'     => ['controller' => 'ReservacionesController','method' => 'index',         'auth' => true],
        'reservaciones/crear'=> ['controller' => 'ReservacionesController','method' => 'crear',        'auth' => true],
        'reservaciones/guardar'=>['controller'=> 'ReservacionesController','method' => 'guardar',      'auth' => true],
        'reservaciones/estado'=>['controller' => 'ReservacionesController','method' => 'cambiarEstado','auth' => true],

        // ── PEDIDOS ───────────────────────────────────────────
        'pedidos'           => ['controller' => 'PedidosController',     'method' => 'index',          'auth' => true],
        'pedidos/nuevo'     => ['controller' => 'PedidosController',     'method' => 'nuevo',          'auth' => true],
        'pedidos/guardar'   => ['controller' => 'PedidosController',     'method' => 'guardar',        'auth' => true],
        'pedidos/estado'    => ['controller' => 'PedidosController',     'method' => 'cambiarEstado',  'auth' => true],
        'pedidos/detalle'   => ['controller' => 'PedidosController',     'method' => 'detalle',        'auth' => true],

        // ── PRODUCTOS ─────────────────────────────────────────
        'productos'         => ['controller' => 'ProductosController',   'method' => 'index',          'auth' => true, 'roles' => ['admin','cajero']],
        'productos/guardar' => ['controller' => 'ProductosController',   'method' => 'guardar',        'auth' => true, 'roles' => ['admin']],
        'productos/eliminar'=> ['controller' => 'ProductosController',   'method' => 'eliminar',       'auth' => true, 'roles' => ['admin']],

        // ── CAJA ──────────────────────────────────────────────
        'caja'              => ['controller' => 'CajaController',        'method' => 'index',          'auth' => true, 'roles' => ['admin','cajero']],
        'caja/cobrar'       => ['controller' => 'CajaController',        'method' => 'cobrar',         'auth' => true, 'roles' => ['admin','cajero']],
        'caja/boleta'       => ['controller' => 'CajaController',        'method' => 'boleta',         'auth' => true, 'roles' => ['admin','cajero']],

        // ── REPORTES ──────────────────────────────────────────
        'reportes'          => ['controller' => 'ReportesController',    'method' => 'index',          'auth' => true, 'roles' => ['admin']],
        'reportes/ventas'   => ['controller' => 'ReportesController',    'method' => 'ventas',         'auth' => true, 'roles' => ['admin']],
        'reportes/canciones'=> ['controller' => 'ReportesController',    'method' => 'canciones',      'auth' => true, 'roles' => ['admin']],

        // ── USUARIOS ──────────────────────────────────────────
        'usuarios'          => ['controller' => 'UsuariosController',    'method' => 'index',          'auth' => true, 'roles' => ['admin']],
        'usuarios/guardar'  => ['controller' => 'UsuariosController',    'method' => 'guardar',        'auth' => true, 'roles' => ['admin']],
        'usuarios/eliminar' => ['controller' => 'UsuariosController',    'method' => 'eliminar',       'auth' => true, 'roles' => ['admin']],

        // ── CONFIGURACIÓN ────────────────────────────────────
        'configuracion'     => ['controller' => 'ConfigController',      'method' => 'index',          'auth' => true, 'roles' => ['admin']],
        'configuracion/guardar'=>['controller'=> 'ConfigController',     'method' => 'guardar',        'auth' => true, 'roles' => ['admin']],

        // ── API / AJAX ────────────────────────────────────────
        'api/cola'          => ['controller' => 'ColaController',        'method' => 'estadoAjax',     'auth' => false],
        'api/notificaciones'=> ['controller' => 'DashboardController',   'method' => 'notificaciones', 'auth' => true],
        'api/notificaciones/leer'  => ['controller' => 'DashboardController', 'method' => 'marcarLeida',      'auth' => true],
        'api/notificaciones/leertodas' => ['controller' => 'DashboardController', 'method' => 'marcarTodasLeidas', 'auth' => true],
        'api/mesas'         => ['controller' => 'MesasController',       'method' => 'estadoAjax',     'auth' => true],
    ];

    public function dispatch(string $url): void {
        // Separar segmento base de parámetros: ej. "canciones/editar/5" → ruta "canciones/editar", param "5"
        $segments = explode('/', $url);
        $param    = null;

        // Buscar la ruta con hasta 2 segmentos
        $routeKey = '';
        if (isset($this->routes[$url])) {
            $routeKey = $url;
        } elseif (count($segments) >= 2) {
            $try = $segments[0] . '/' . $segments[1];
            if (isset($this->routes[$try])) {
                $routeKey = $try;
                $param = $segments[2] ?? null;
            } elseif (isset($this->routes[$segments[0]])) {
                $routeKey = $segments[0];
                $param = $segments[1] ?? null;
            }
        } elseif (isset($this->routes[$segments[0]])) {
            $routeKey = $segments[0];
        }

        // Ruta no encontrada
        if ($routeKey === '' && !isset($this->routes[$routeKey])) {
            $this->pagina404();
            return;
        }

        $route = $this->routes[$routeKey];

        // Verificar autenticación
        if ($route['auth'] && !Auth::estaAutenticado()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Verificar roles si aplica
        if (!empty($route['roles']) && !Auth::tieneRol($route['roles'])) {
            $this->paginaSinPermiso();
            return;
        }

        // Cargar el controlador
        $controllerFile = APP_PATH . '/controllers/' . $route['controller'] . '.php';
        if (!file_exists($controllerFile)) {
            $this->pagina404();
            return;
        }

        require_once $controllerFile;
        $controller = new $route['controller']();
        $method = $route['method'];

        if (!method_exists($controller, $method)) {
            $this->pagina404();
            return;
        }

        $param !== null ? $controller->$method($param) : $controller->$method();
    }

    private function pagina404(): void {
        http_response_code(404);
        require_once APP_PATH . '/views/partials/404.php';
    }

    private function paginaSinPermiso(): void {
        http_response_code(403);
        require_once APP_PATH . '/views/partials/403.php';
    }
}
