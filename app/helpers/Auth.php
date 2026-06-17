<?php
// app/helpers/Auth.php

defined('BASEPATH') or die('Acceso denegado');

class Auth {

    public static function login(array $usuario): void {
        session_regenerate_id(true);
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_usuario']= $usuario['usuario'];
        $_SESSION['usuario_rol']    = $usuario['rol'];
        $_SESSION['usuario_avatar'] = $usuario['avatar'] ?? '';
        $_SESSION['login_time']     = time();
    }

    public static function logout(): void {
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public static function estaAutenticado(): bool {
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    }

    public static function tieneRol(array $roles): bool {
        if (!self::estaAutenticado()) return false;
        return in_array($_SESSION['usuario_rol'], $roles);
    }

    public static function esAdmin(): bool {
        return self::tieneRol(['admin']);
    }

    public static function usuario(): array {
        return [
            'id'      => $_SESSION['usuario_id']      ?? 0,
            'nombre'  => $_SESSION['usuario_nombre']  ?? '',
            'usuario' => $_SESSION['usuario_usuario'] ?? '',
            'rol'     => $_SESSION['usuario_rol']     ?? '',
            'avatar'  => $_SESSION['usuario_avatar']  ?? '',
        ];
    }

    public static function id(): int {
        return (int)($_SESSION['usuario_id'] ?? 0);
    }

    public static function rol(): string {
        return $_SESSION['usuario_rol'] ?? '';
    }

    // Genera token CSRF y lo guarda en sesión
    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Valida token CSRF enviado por POST
    public static function validarCsrf(): bool {
        $token = $_POST['csrf_token'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    // Redirige si no está autenticado
    public static function requireLogin(): void {
        if (!self::estaAutenticado()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    // Redirige si no tiene el rol requerido
    public static function requireRol(array $roles): void {
        self::requireLogin();
        if (!self::tieneRol($roles)) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    // Alias de nombre de rol en español para mostrar en UI
    public static function rolLabel(): string {
        $labels = [
            'admin'    => 'Administrador',
            'operador' => 'Operador',
            'cajero'   => 'Cajero',
            'mesero'   => 'Mesero',
        ];
        return $labels[self::rol()] ?? ucfirst(self::rol());
    }

    // Ícono según rol
    public static function rolIcono(): string {
        $iconos = [
            'admin'    => '👑',
            'operador' => '🎤',
            'cajero'   => '💰',
            'mesero'   => '🍽️',
        ];
        return $iconos[self::rol()] ?? '👤';
    }
}
