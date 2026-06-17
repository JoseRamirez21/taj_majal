<?php
// app/controllers/AuthController.php

defined('BASEPATH') or die('Acceso denegado');

class AuthController {

    public function login(): void {
        // Si ya está logueado, redirigir al dashboard
        if (Auth::estaAutenticado()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        require_once APP_PATH . '/views/auth/login.php';
    }

    public function procesar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $usuario  = trim($_POST['usuario']  ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($usuario) || empty($password)) {
            $_SESSION['login_error'] = 'Completa todos los campos.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $user = Database::fetch(
            "SELECT * FROM usuarios WHERE usuario = ? AND activo = 1 LIMIT 1",
            [$usuario]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        Auth::login($user);
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    public function logout(): void {
        Auth::logout();
    }
}
