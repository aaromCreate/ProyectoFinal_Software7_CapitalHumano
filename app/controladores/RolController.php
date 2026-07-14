<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Rol;
use App\Servicios\AuthService;
use App\Utilidades\FormState;
use App\Utilidades\Sanitizer;
use App\Utilidades\Validator;
use Throwable;

/**
 * Controla roles y permisos.
 */
final class RolController
{
    private Rol $roles;

    public function __construct()
    {
        $this->roles = new Rol();
    }

    public function index(): void
    {
        AuthService::ensure('roles', 'ver');
        render_view('roles/index', [
            'title' => 'Roles',
            'roles' => $this->roles->all(),
        ]);
    }

    public function create(): void
    {
        AuthService::ensure('roles', 'crear');
        render_view('roles/create', [
            'title' => 'Nuevo rol',
            'modulos' => $this->roles->permissionsByModule(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function store(): void
    {
        AuthService::ensure('roles', 'crear');
        try {
            verify_csrf();
            $data = Sanitizer::rol($_POST);
            $errors = Validator::rol($data);
            if ($errors) {
                FormState::flash($data, $errors);
                redirect_to('roles.create');
            }

            $this->roles->create($data);
            flash('success', 'Rol creado correctamente.');
            redirect_to('roles.index');
        } catch (Throwable $exception) {
            FormState::flash($_POST, [$exception->getMessage()]);
            redirect_to('roles.create');
        }
    }

    public function edit(): void
    {
        AuthService::ensure('roles', 'editar');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        $rol = $this->roles->find($id);
        if (!$rol) {
            flash('error', 'El rol no existe.');
            redirect_to('roles.index');
        }

        render_view('roles/edit', [
            'title' => 'Editar rol',
            'rol' => $rol,
            'modulos' => $this->roles->permissionsByModule(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function update(): void
    {
        AuthService::ensure('roles', 'editar');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        try {
            verify_csrf();
            $data = Sanitizer::rol($_POST);
            $errors = Validator::rol($data);
            if ($errors) {
                FormState::flash($data, $errors);
                redirect_to('roles.edit', ['id' => $id]);
            }

            $this->roles->update($id, $data);
            flash('success', 'Rol actualizado correctamente.');
            redirect_to('roles.index');
        } catch (Throwable $exception) {
            FormState::flash($_POST, [$exception->getMessage()]);
            redirect_to('roles.edit', ['id' => $id]);
        }
    }

    public function toggle(): void
    {
        AuthService::ensure('roles', 'editar');
        try {
            verify_csrf();
            $id = Sanitizer::int($_GET['id'] ?? 0);
            $this->roles->toggleActive($id);
            flash('success', 'Estado del rol actualizado.');
        } catch (Throwable $exception) {
            flash('error', $exception->getMessage());
        }
        redirect_to('roles.index');
    }
}
