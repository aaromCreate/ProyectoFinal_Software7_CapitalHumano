<?php

declare(strict_types=1);

namespace App\Utilidades;

/**
 * Centraliza datos de formularios fallidos para re-renderizar vistas.
 */
final class FormState
{
    /**
     * @param array<string,mixed> $old
     * @param array<int,string> $errors
     */
    public static function flash(array $old, array $errors): void
    {
        $_SESSION['form_old'] = $old;
        $_SESSION['form_errors'] = $errors;
    }

    /**
     * @return array<string,mixed>
     */
    public static function old(): array
    {
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_old']);
        return is_array($old) ? $old : [];
    }

    /**
     * @return array<int,string>
     */
    public static function errors(): array
    {
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);
        return is_array($errors) ? $errors : [];
    }
}
