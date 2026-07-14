<?php $appConfig = require ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php'; ?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? $appConfig['name']) ?> | Sistema de Capital Humano</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <header class="topbar">
        <a class="brand" href="<?= e(url('home')) ?>">Capital Humano</a>
        <nav class="nav">
            <a href="<?= e(url('home')) ?>"
                class="<?= ($_GET['route'] ?? 'home') === 'home' ? 'active' : '' ?>">Inicio</a>
            <?php if (\App\Servicios\AuthService::can('colaboradores', 'ver')): ?>
                <a href="<?= e(url('colaboradores.index')) ?>"
                    class="<?= str_starts_with($_GET['route'] ?? '', 'colaboradores') ? 'active' : '' ?>">Colaboradores</a>
            <?php endif; ?>
            <?php if (\App\Servicios\AuthService::can('reportes', 'ver')): ?>
                <a href="<?= e(url('reportes.index')) ?>"
                    class="<?= str_starts_with($_GET['route'] ?? '', 'reportes') ? 'active' : '' ?>">Reportes</a>
            <?php endif; ?>
            <?php if (\App\Servicios\AuthService::can('vacaciones', 'ver')): ?>
                <a href="<?= e(url('vacaciones.index')) ?>"
                    class="<?= str_starts_with($_GET['route'] ?? '', 'vacaciones') ? 'active' : '' ?>">Vacaciones</a>
            <?php endif; ?>
            <?php if (\App\Servicios\AuthService::can('usuarios', 'ver')): ?>
                <a href="<?= e(url('usuarios.index')) ?>"
                    class="<?= str_starts_with($_GET['route'] ?? '', 'usuarios') ? 'active' : '' ?>">Usuarios</a>
            <?php endif; ?>
            <?php if (\App\Servicios\AuthService::can('roles', 'ver')): ?>
                <a href="<?= e(url('roles.index')) ?>"
                    class="<?= str_starts_with($_GET['route'] ?? '', 'roles') ? 'active' : '' ?>">Roles</a>
            <?php endif; ?>
            <?php if (\App\Servicios\AuthService::can('usuarios', 'ver')): ?>
                <a href="<?= e(url('bitacora')) ?>"
                    class="<?= ($_GET['route'] ?? '') === 'bitacora' ? 'active' : '' ?>">Bitácora</a>
            <?php endif; ?>
        </nav>
        <div class="user-menu">
            <?php if (\App\Servicios\AuthService::check()): ?>
                <span class="user-name"><?= e((\App\Servicios\AuthService::user()['nombre'] ?? '')) ?></span>
                <form method="post" action="<?= e(url('logout')) ?>" class="inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn small ghost">Cerrar sesión</button>
                </form>
            <?php else: ?>
                <a href="<?= e(url('login')) ?>" class="btn small primary">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </header>
    <main class="page">
        <?php if ($message = flash('success')): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($message = flash('error')): ?>
            <div class="alert alert-danger"><?= e($message) ?></div>
        <?php endif; ?>