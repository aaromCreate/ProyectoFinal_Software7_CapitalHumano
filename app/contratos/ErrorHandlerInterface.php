<?php

declare(strict_types=1);

namespace App\Contratos;

use Throwable;

/**
 * Contrato para manejar errores de forma centralizada.
 */
interface ErrorHandlerInterface
{
    public function handle(Throwable $exception): void;
}
