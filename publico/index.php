<?php

declare(strict_types=1);

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

$routes = require ROOT_PATH . DIRECTORY_SEPARATOR . 'rutas' . DIRECTORY_SEPARATOR . 'web.php';
$route = $_GET['route'] ?? 'home';

if (!is_string($route) || !isset($routes[$route])) {
    http_response_code(404);
    render_view('colaboradores/index', [
        'title' => 'Ruta no encontrada',
        'colaboradores' => [],
        'q' => '',
    ]);
    exit;
}

[$class, $method] = $routes[$route];

try {
    (new $class())->$method();
} catch (Throwable $exception) {
    http_response_code(500);
    (new \App\Servicios\WebErrorHandler())->handle($exception);
    render_view('colaboradores/index', [
        'title' => 'Error',
        'colaboradores' => [],
        'q' => '',
    ]);
}
