<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Colaborador;
use App\Modelos\Vacacion;
use App\Servicios\AuthService;
use App\Utilidades\FormState;
use App\Utilidades\Sanitizer;
use App\Utilidades\Validator;
use Throwable;

/**
 * Controla solicitudes de vacaciones.
 */
final class VacacionController
{
    private Vacacion $vacaciones;
    private Colaborador $colaboradores;

    public function __construct()
    {
        $this->vacaciones = new Vacacion();
        $this->colaboradores = new Colaborador();
    }

    public function index(): void
    {
        AuthService::ensure('vacaciones', 'ver');
        render_view('vacaciones/index', [
            'title' => 'Vacaciones',
            'solicitudes' => $this->vacaciones->all(),
        ]);
    }

    public function create(): void
    {
        AuthService::ensure('vacaciones', 'crear');
        render_view('vacaciones/create', [
            'title' => 'Nueva solicitud de vacaciones',
            'colaboradores' => $this->colaboradores->all(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function store(): void
    {
        AuthService::ensure('vacaciones', 'crear');
        try {
            verify_csrf();
            $data = [
                'colaborador_id' => Sanitizer::int($_POST['colaborador_id'] ?? 0),
                'fecha_inicio' => Sanitizer::text($_POST['fecha_inicio'] ?? ''),
                'fecha_fin' => Sanitizer::text($_POST['fecha_fin'] ?? ''),
                'observaciones' => Sanitizer::text($_POST['observaciones'] ?? ''),
            ];
            $errors = Validator::vacacion($data);
            if ($errors) {
                FormState::flash($data, $errors);
                redirect_to('vacaciones.create');
            }

            $this->vacaciones->create($data);
            flash('success', 'Solicitud registrada correctamente.');
            redirect_to('vacaciones.index');
        } catch (Throwable $exception) {
            FormState::flash($_POST, [$exception->getMessage()]);
            redirect_to('vacaciones.create');
        }
    }

    public function aprobar(): void
    {
        AuthService::ensure('vacaciones', 'crear');
        try {
            verify_csrf();
            $this->vacaciones->aprobar(Sanitizer::int($_GET['id'] ?? 0));
            flash('success', 'Solicitud aprobada.');
        } catch (Throwable $exception) {
            flash('error', $exception->getMessage());
        }
        redirect_to('vacaciones.index');
    }

    public function rechazar(): void
    {
        AuthService::ensure('vacaciones', 'crear');
        try {
            verify_csrf();
            $this->vacaciones->rechazar(Sanitizer::int($_GET['id'] ?? 0));
            flash('success', 'Solicitud rechazada.');
        } catch (Throwable $exception) {
            flash('error', $exception->getMessage());
        }
        redirect_to('vacaciones.index');
    }
}
