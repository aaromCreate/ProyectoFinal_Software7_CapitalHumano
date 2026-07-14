<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Usuario;
use App\Servicios\AuthService;
use App\Utilidades\FormState;
use App\Utilidades\Sanitizer;
use Throwable;

/**
 * Controla login, logout y bitácora de accesos.
 */
final class AuthController
{
    private AuthService $auth;
    private Usuario $usuarios;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->usuarios = new Usuario();
    }

    public function loginForm(): void
    {
        if (AuthService::check()) {
            redirect_to('home');
        }

        render_view('auth/login', [
            'title' => 'Iniciar sesion',
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function login(): void
    {
        try {
            verify_csrf();
            $usuario = Sanitizer::text($_POST['usuario'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($usuario === '' || $password === '') {
                FormState::flash($_POST, ['Usuario y contraseña son obligatorios.']);
                redirect_to('login');
            }

            $this->auth->login($usuario, $password);
            flash('success', 'Bienvenido, ' . e(AuthService::user()['nombre'] ?? $usuario) . '.');
            redirect_to('home');
        } catch (Throwable $exception) {
            FormState::flash($_POST, [$exception->getMessage()]);
            redirect_to('login');
        }
    }

    public function logout(): void
    {
        $this->auth->logout();
        flash('success', 'Sesion cerrada correctamente.');
        redirect_to('login');
    }

    public function logs(): void
    {
        AuthService::ensure('usuarios', 'ver');
        render_view('auth/logs', [
            'title' => 'Bitacora de accesos',
            'logs' => $this->usuarios->accessLogs(200),
        ]);
    }
}
