<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Rol;
use App\Modelos\Usuario;
use App\Servicios\AuthService;
use App\Utilidades\FormState;
use App\Utilidades\Sanitizer;
use App\Utilidades\Validator;
use Throwable;

/**
 * Controla el CRUD de usuarios administrativos.
 */
final class UsuarioController
{
    private Usuario $usuarios;
    private Rol $roles;

    public function __construct()
    {
        $this->usuarios = new Usuario();
        $this->roles = new Rol();
    }

    public function index(): void
    {
        AuthService::ensure('usuarios', 'ver');
        render_view('usuarios/index', [
            'title' => 'Usuarios',
            'usuarios' => $this->usuarios->all(),
        ]);
    }

    public function create(): void
    {
        AuthService::ensure('usuarios', 'crear');
        render_view('usuarios/create', [
            'title' => 'Nuevo usuario',
            'roles' => $this->roles->all(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function store(): void
    {
        AuthService::ensure('usuarios', 'crear');
        try {
            verify_csrf();
            $data = Sanitizer::usuario($_POST);
            $errors = Validator::usuario($data, true);
            if ($errors) {
                FormState::flash($data, $errors);
                redirect_to('usuarios.create');
            }

            $this->usuarios->create($data);
            flash('success', 'Usuario creado correctamente.');
            redirect_to('usuarios.index');
        } catch (Throwable $exception) {
            FormState::flash($_POST, [$exception->getMessage()]);
            redirect_to('usuarios.create');
        }
    }

    public function edit(): void
    {
        AuthService::ensure('usuarios', 'editar');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        $usuario = $this->usuarios->find($id);
        if (!$usuario) {
            flash('error', 'El usuario no existe.');
            redirect_to('usuarios.index');
        }

        render_view('usuarios/edit', [
            'title' => 'Editar usuario',
            'usuario' => $usuario,
            'roles' => $this->roles->all(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function update(): void
    {
        AuthService::ensure('usuarios', 'editar');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        try {
            verify_csrf();
            $data = Sanitizer::usuario($_POST);
            $errors = Validator::usuario($data, false);
            if ($errors) {
                FormState::flash($data, $errors);
                redirect_to('usuarios.edit', ['id' => $id]);
            }

            $this->usuarios->update($id, $data);
            flash('success', 'Usuario actualizado correctamente.');
            redirect_to('usuarios.index');
        } catch (Throwable $exception) {
            FormState::flash($_POST, [$exception->getMessage()]);
            redirect_to('usuarios.edit', ['id' => $id]);
        }
    }

    public function toggle(): void
    {
        AuthService::ensure('usuarios', 'editar');
        try {
            verify_csrf();
            $id = Sanitizer::int($_GET['id'] ?? 0);
            $this->usuarios->toggleActive($id);
            flash('success', 'Estado del usuario actualizado.');
        } catch (Throwable $exception) {
            flash('error', $exception->getMessage());
        }
        redirect_to('usuarios.index');
    }
}
