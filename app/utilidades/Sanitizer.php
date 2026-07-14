<?php

declare(strict_types=1);

namespace App\Utilidades;

/**
 * Normaliza datos de entrada y escapa datos de salida.
 */
final class Sanitizer
{
    public static function text(mixed $value): string
    {
        $text = strip_tags((string) $value);
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';
        return $text;
    }

    public static function title(mixed $value): string
    {
        return mb_convert_case(self::text($value), MB_CASE_TITLE, 'UTF-8');
    }

    public static function email(mixed $value): string
    {
        return mb_strtolower(self::text($value), 'UTF-8');
    }

    public static function phone(mixed $value): string
    {
        return preg_replace('/[^\d+\-\s]/', '', self::text($value)) ?? '';
    }

    public static function int(mixed $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function money(mixed $value): string
    {
        $normalized = str_replace(',', '.', self::text($value));
        return number_format((float) $normalized, 2, '.', '');
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public static function colaborador(array $data): array
    {
        return array_merge([
            'identidad' => self::text($data['identidad'] ?? ''),
            'primer_nombre' => self::title($data['primer_nombre'] ?? ''),
            'segundo_nombre' => self::title($data['segundo_nombre'] ?? ''),
            'primer_apellido' => self::title($data['primer_apellido'] ?? ''),
            'segundo_apellido' => self::title($data['segundo_apellido'] ?? ''),
            'fecha_nacimiento' => self::text($data['fecha_nacimiento'] ?? ''),
            'sexo' => self::text($data['sexo'] ?? ''),
            'direccion' => self::text($data['direccion'] ?? ''),
            'correo' => self::email($data['correo'] ?? ''),
            'telefono' => self::phone($data['telefono'] ?? ''),
            'celular' => self::phone($data['celular'] ?? ''),
            'estado_colaborador_id' => self::int($data['estado_colaborador_id'] ?? 1),
        ], self::perfilLaboral($data));
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public static function perfilLaboral(array $data): array
    {
        return [
            'ocupacion_id' => self::int($data['ocupacion_id'] ?? 0),
            'tipo_empleado_id' => self::int($data['tipo_empleado_id'] ?? 0),
            'planilla_id' => self::int($data['planilla_id'] ?? 0),
            'departamento_id' => self::int($data['departamento_id'] ?? 0),
            'salario' => self::money($data['salario'] ?? 0),
            'fecha_inicio' => self::text($data['fecha_inicio'] ?? ''),
            'fecha_fin' => self::text($data['fecha_fin'] ?? ''),
        ];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public static function baja(array $data): array
    {
        return [
            'fecha_fin' => self::text($data['fecha_fin'] ?? ''),
            'motivo_terminacion_id' => self::int($data['motivo_terminacion_id'] ?? 0),
        ];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public static function usuario(array $data): array
    {
        $roles = $data['roles'] ?? [];
        if (!is_array($roles)) {
            $roles = [];
        }

        return [
            'nombre' => self::title($data['nombre'] ?? ''),
            'correo' => self::email($data['correo'] ?? ''),
            'usuario' => self::text($data['usuario'] ?? ''),
            'password' => $data['password'] ?? '',
            'activo' => (int) ($data['activo'] ?? 1),
            'roles' => array_values(array_filter(array_map([self::class, 'int'], $roles))),
        ];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public static function rol(array $data): array
    {
        $permisos = $data['permisos'] ?? [];
        if (!is_array($permisos)) {
            $permisos = [];
        }

        return [
            'nombre' => self::title($data['nombre'] ?? ''),
            'descripcion' => self::text($data['descripcion'] ?? ''),
            'activo' => (int) ($data['activo'] ?? 1),
            'permisos' => array_values(array_filter(array_map([self::class, 'int'], $permisos))),
        ];
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
