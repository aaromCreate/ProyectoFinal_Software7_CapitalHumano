<?php

declare(strict_types=1);

use Api\Controladores\ApiController;
use App\Controladores\AuthController;
use App\Controladores\ColaboradorController;
use App\Controladores\ReporteController;
use App\Controladores\RolController;
use App\Controladores\UsuarioController;
use App\Controladores\VacacionController;

return [
    // Autenticación
    'login' => [AuthController::class, 'loginForm'],
    'login.process' => [AuthController::class, 'login'],
    'logout' => [AuthController::class, 'logout'],
    'bitacora' => [AuthController::class, 'logs'],

    // Usuarios
    'usuarios.index' => [UsuarioController::class, 'index'],
    'usuarios.create' => [UsuarioController::class, 'create'],
    'usuarios.store' => [UsuarioController::class, 'store'],
    'usuarios.edit' => [UsuarioController::class, 'edit'],
    'usuarios.update' => [UsuarioController::class, 'update'],
    'usuarios.toggle' => [UsuarioController::class, 'toggle'],

    // Roles
    'roles.index' => [RolController::class, 'index'],
    'roles.create' => [RolController::class, 'create'],
    'roles.store' => [RolController::class, 'store'],
    'roles.edit' => [RolController::class, 'edit'],
    'roles.update' => [RolController::class, 'update'],
    'roles.toggle' => [RolController::class, 'toggle'],

    // Colaboradores
    'home' => [ColaboradorController::class, 'home'],
    'colaboradores.index' => [ColaboradorController::class, 'index'],
    'colaboradores.create' => [ColaboradorController::class, 'create'],
    'colaboradores.store' => [ColaboradorController::class, 'store'],
    'colaboradores.edit' => [ColaboradorController::class, 'edit'],
    'colaboradores.update' => [ColaboradorController::class, 'update'],
    'colaboradores.show' => [ColaboradorController::class, 'show'],
    'colaboradores.promoteForm' => [ColaboradorController::class, 'promoteForm'],
    'colaboradores.promote' => [ColaboradorController::class, 'promote'],
    'colaboradores.bajaForm' => [ColaboradorController::class, 'bajaForm'],
    'colaboradores.baja' => [ColaboradorController::class, 'baja'],
    'colaboradores.reintegrar' => [ColaboradorController::class, 'reintegrar'],
    'colaboradores.destroy' => [ColaboradorController::class, 'destroy'],
    'colaboradores.historial.store' => [ColaboradorController::class, 'addHistorial'],
    'colaboradores.historial.destroy' => [ColaboradorController::class, 'removeHistorial'],

    // Reportes
    'reportes.index' => [ReporteController::class, 'index'],
    'reportes.export' => [ReporteController::class, 'export'],

    // Vacaciones
    'vacaciones.index' => [VacacionController::class, 'index'],
    'vacaciones.create' => [VacacionController::class, 'create'],
    'vacaciones.store' => [VacacionController::class, 'store'],
    'vacaciones.aprobar' => [VacacionController::class, 'aprobar'],
    'vacaciones.rechazar' => [VacacionController::class, 'rechazar'],

    // API REST
    'api.colaboradores.sexo' => [ApiController::class, 'porSexo'],
];
