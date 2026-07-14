<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Contratos\ErrorHandlerInterface;
use Throwable;

/**
 * Maneja errores web registrandolos y mostrando un mensaje al usuario.
 */
final class WebErrorHandler implements ErrorHandlerInterface
{
    public function handle(Throwable $exception): void
    {
        log_exception($exception);
        flash('error', $exception->getMessage() . ' — revisa almacenamiento/logs/app.log');
    }
}
