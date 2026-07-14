<?php

declare(strict_types=1);

namespace App\Utilidades;

/**
 * Valida reglas de entrada del sistema y retorna errores legibles.
 */
final class Validator
{
    private const SEXOS = ['Masculino', 'Femenino', 'Otro'];
    private const EDAD_MINIMA = 16;

    /**
     * @param array<string,mixed> $data
     * @param array<string,array<int,array<string,mixed>>> $catalogos
     * @return array<string>
     */
    public static function colaborador(array $data, array $catalogos, bool $validarPerfil = true): array
    {
        $errors = [];
        self::required($data, ['identidad', 'primer_nombre', 'primer_apellido', 'sexo', 'correo', 'celular'], $errors);

        if ($data['identidad'] !== '' && !preg_match('/^[A-Za-z0-9\-]{4,50}$/', (string) $data['identidad'])) {
            $errors[] = 'La identidad o documento debe tener de 4 a 50 caracteres alfanumericos o guiones.';
        }
        if ($data['primer_nombre'] !== '' && !preg_match('/^[\p{L}\s\'-]{2,80}$/u', (string) $data['primer_nombre'])) {
            $errors[] = 'El primer nombre solo debe contener letras y espacios.';
        }
        if ($data['primer_apellido'] !== '' && !preg_match('/^[\p{L}\s\'-]{2,80}$/u', (string) $data['primer_apellido'])) {
            $errors[] = 'El primer apellido solo debe contener letras y espacios.';
        }
        if ((string) ($data['fecha_nacimiento'] ?? '') !== '') {
            if (!self::validDate((string) $data['fecha_nacimiento'])) {
                $errors[] = 'La fecha de nacimiento no es valida.';
            } else {
                $fechaNac = new \DateTimeImmutable($data['fecha_nacimiento']);
                $hoy = new \DateTimeImmutable('today');
                if ($fechaNac > $hoy) {
                    $errors[] = 'La fecha de nacimiento no puede ser futura.';
                } elseif ($fechaNac->diff($hoy)->y < self::EDAD_MINIMA) {
                    $errors[] = 'El colaborador debe tener al menos ' . self::EDAD_MINIMA . ' años.';
                }
            }
        }
        if (!in_array($data['sexo'], self::SEXOS, true)) {
            $errors[] = 'Seleccione un sexo valido.';
        }
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo debe tener un formato valido.';
        }
        if (!preg_match('/^[\d+\-\s]{7,20}$/', (string) $data['celular'])) {
            $errors[] = 'El celular debe tener entre 7 y 20 digitos, espacios, + o guiones.';
        }
        if ((string) ($data['telefono'] ?? '') !== '' && !preg_match('/^[\d+\-\s]{7,20}$/', (string) $data['telefono'])) {
            $errors[] = 'El telefono debe tener entre 7 y 20 digitos, espacios, + o guiones.';
        }

        if ($validarPerfil) {
            $errors = array_merge($errors, self::perfilLaboral($data, $catalogos));
        }

        return $errors;
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,array<int,array<string,mixed>>> $catalogos
     * @return array<string>
     */
    public static function perfilLaboral(array $data, array $catalogos): array
    {
        $errors = [];
        self::required($data, ['ocupacion_id', 'tipo_empleado_id', 'planilla_id', 'salario', 'fecha_inicio'], $errors);

        if (!self::idExists((int) $data['ocupacion_id'], $catalogos['ocupaciones'] ?? [])) {
            $errors[] = 'Seleccione una ocupacion valida.';
        }
        if (!self::idExists((int) $data['tipo_empleado_id'], $catalogos['tipos_empleado'] ?? [])) {
            $errors[] = 'Seleccione un tipo de empleado valido.';
        }
        if (!self::idExists((int) $data['planilla_id'], $catalogos['tipos_planilla'] ?? [])) {
            $errors[] = 'Seleccione una planilla valida.';
        }
        if ((int) ($data['departamento_id'] ?? 0) > 0 && !self::idExists((int) $data['departamento_id'], $catalogos['departamentos'] ?? [])) {
            $errors[] = 'Seleccione un departamento valido.';
        }
        if ((float) $data['salario'] <= 0) {
            $errors[] = 'El salario debe ser mayor que cero.';
        }
        if (!self::validDate((string) $data['fecha_inicio'])) {
            $errors[] = 'La fecha de inicio debe tener formato valido.';
        } else {
            $fechaInicio = new \DateTimeImmutable($data['fecha_inicio']);
            $hoy = new \DateTimeImmutable('today');
            $maxFuturo = $hoy->modify('+90 days');
            if ($fechaInicio > $maxFuturo) {
                $errors[] = 'La fecha de inicio no puede ser superior a 90 dias en el futuro.';
            }
        }
        if ((string) ($data['fecha_fin'] ?? '') !== '') {
            if (!self::validDate((string) $data['fecha_fin'])) {
                $errors[] = 'La fecha fin debe tener formato valido.';
            } elseif ((string) $data['fecha_inicio'] !== '' && (string) $data['fecha_fin'] < (string) $data['fecha_inicio']) {
                $errors[] = 'La fecha fin no puede ser anterior a la fecha de inicio.';
            }
        }

        return $errors;
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,array<int,array<string,mixed>>> $catalogos
     * @return array<string>
     */
    public static function baja(array $data, array $catalogos): array
    {
        $errors = [];
        self::required($data, ['fecha_fin', 'motivo_terminacion_id'], $errors);
        if (!self::validDate((string) $data['fecha_fin'])) {
            $errors[] = 'La fecha fin debe tener formato valido.';
        } else {
            $fechaFin = new \DateTimeImmutable($data['fecha_fin']);
            $hoy = new \DateTimeImmutable('today');
            if ($fechaFin > $hoy) {
                $errors[] = 'La fecha de baja no puede ser futura.';
            }
        }
        if (!self::idExists((int) $data['motivo_terminacion_id'], $catalogos['motivos_terminacion'] ?? [])) {
            $errors[] = 'Seleccione un motivo de baja valido.';
        }

        return $errors;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string>
     */
    public static function usuario(array $data, bool $requirePassword): array
    {
        $errors = [];
        $required = ['nombre', 'correo', 'usuario'];
        if ($requirePassword) {
            $required[] = 'password';
        }
        self::required($data, $required, $errors);

        if ($data['nombre'] !== '' && !preg_match('/^[\p{L}\s\'-]{2,80}$/u', (string) $data['nombre'])) {
            $errors[] = 'El nombre solo debe contener letras y espacios.';
        }
        if ($data['correo'] !== '' && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo debe tener un formato valido.';
        }
        if ($data['usuario'] !== '' && !preg_match('/^[a-zA-Z0-9_]{4,50}$/', (string) $data['usuario'])) {
            $errors[] = 'El usuario debe tener de 4 a 50 caracteres alfanumericos o guion bajo.';
        }
        if (!empty($data['password']) && !self::validPassword((string) $data['password'])) {
            $errors[] = 'La contraseÃ±a debe tener entre 8 y 12 caracteres.';
        }
        if (empty($data['roles']) || !is_array($data['roles'])) {
            $errors[] = 'Seleccione al menos un rol.';
        }

        return $errors;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string>
     */
    public static function rol(array $data): array
    {
        $errors = [];
        self::required($data, ['nombre'], $errors);
        if ($data['nombre'] !== '' && !preg_match('/^[\p{L}\s\'-]{2,50}$/u', (string) $data['nombre'])) {
            $errors[] = 'El nombre del rol debe tener entre 2 y 50 caracteres.';
        }
        return $errors;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string>
     */
    public static function vacacion(array $data): array
    {
        $errors = [];
        self::required($data, ['colaborador_id', 'fecha_inicio', 'fecha_fin'], $errors);
        if ((int) $data['colaborador_id'] <= 0) {
            $errors[] = 'Seleccione un colaborador valido.';
        }
        if (!self::validDate((string) $data['fecha_inicio'])) {
            $errors[] = 'La fecha de inicio no es valida.';
        }
        if (!self::validDate((string) $data['fecha_fin'])) {
            $errors[] = 'La fecha fin no es valida.';
        }
        if ((string) $data['fecha_inicio'] !== '' && (string) $data['fecha_fin'] !== '' && (string) $data['fecha_fin'] < (string) $data['fecha_inicio']) {
            $errors[] = 'La fecha fin no puede ser anterior a la fecha de inicio.';
        }
        return $errors;
    }

    /**
     * @param array<string,mixed> $data
     * @param array<int,string> $fields
     * @param array<int,string> $errors
     */
    private static function required(array $data, array $fields, array &$errors): void
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '' || (string) $data[$field] === '0') {
                $errors[] = "El campo {$field} es obligatorio.";
            }
        }
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     */
    private static function idExists(int $id, array $rows): bool
    {
        foreach ($rows as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                return true;
            }
        }

        return false;
    }

    private static function validDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        return $parsed instanceof \DateTimeImmutable && $parsed->format('Y-m-d') === $date;
    }

    private static function validPassword(string $password): bool
    {
        $length = mb_strlen($password);
        return $length >= 8 && $length <= 12;
    }
}




