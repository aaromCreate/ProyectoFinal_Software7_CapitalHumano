<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Modelos\Usuario;
use DateTimeImmutable;
use RuntimeException;

/**
 * Gestiona sesión, login, permisos y bloqueos.
 */
final class AuthService
{
    private Usuario $usuarios;
    private const MAX_ATTEMPTS = 3;

    public function __construct(?Usuario $usuarios = null)
    {
        $this->usuarios = $usuarios ?? new Usuario();
    }

    public function login(string $usuario, string $password): void
    {
        $row = $this->usuarios->findByUsername($usuario);

        if (!$row) {
            $this->usuarios->logAccess(null, $usuario, 'fallido', true, 'Usuario no existe');
            throw new RuntimeException('Credenciales incorrectas.');
        }

        $id = (int) $row['id'];

        if ((int) $row['activo'] !== 1) {
            $this->usuarios->logAccess($id, $usuario, 'fallido', true, 'Usuario inactivo');
            throw new RuntimeException('Usuario desactivado. Contacte al administrador.');
        }

        if (!empty($row['bloqueado_hasta'])) {
            $blockedUntil = new DateTimeImmutable((string) $row['bloqueado_hasta']);
            if ($blockedUntil > new DateTimeImmutable()) {
                $this->usuarios->logAccess($id, $usuario, 'fallido', true, 'Cuenta bloqueada');
                throw new RuntimeException('Cuenta bloqueada hasta ' . $blockedUntil->format('Y-m-d H:i:s'));
            }
        }

        if (!password_verify($password, (string) $row['password_hash'])) {
            $this->usuarios->incrementFailedAttempts($id);
            $attempts = (int) $row['intentos_fallidos'] + 1;
            $anomalia = $attempts >= self::MAX_ATTEMPTS;
            if ($anomalia) {
                $this->usuarios->block($id);
            }
            $this->usuarios->logAccess($id, $usuario, 'fallido', $anomalia, "Intento {$attempts}");
            throw new RuntimeException('Credenciales incorrectas.');
        }

        $this->usuarios->resetAttempts($id);
        $this->usuarios->logAccess($id, $usuario, 'exitoso', false);

        $_SESSION['usuario_id'] = $id;
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['usuario_nombre'] = $row['nombre'];
        $_SESSION['permisos'] = $this->usuarios->permissionIds($id);
    }

    public function logout(): void
    {
        unset($_SESSION['usuario_id'], $_SESSION['usuario'], $_SESSION['usuario_nombre'], $_SESSION['permisos']);
    }

    public static function check(): bool
    {
        return !empty($_SESSION['usuario_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id' => $_SESSION['usuario_id'],
            'usuario' => $_SESSION['usuario'],
            'nombre' => $_SESSION['usuario_nombre'],
        ];
    }

    public static function can(string $modulo, string $accion): bool
    {
        $permisos = $_SESSION['permisos'] ?? [];
        if (empty($permisos)) {
            return false;
        }

        // Buscar permiso por modulo+accion. Reutilizamos el modelo Usuario internamente.
        $permisoId = self::permissionId($modulo, $accion);
        return $permisoId !== null && in_array($permisoId, $permisos, true);
    }

    private static function permissionId(string $modulo, string $accion): ?int
    {
        $db = \Config\Database::getConnection();
        $statement = $db->prepare('SELECT id FROM permisos WHERE modulo = :modulo AND accion = :accion');
        $statement->execute(['modulo' => $modulo, 'accion' => $accion]);
        $id = $statement->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    public static function ensure(string $modulo, string $accion): void
    {
        if (!self::check()) {
            redirect_to('login');
        }
        if (!self::can($modulo, $accion)) {
            http_response_code(403);
            throw new RuntimeException('No tiene permiso para realizar esta acción.');
        }
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            redirect_to('login');
        }
    }
}
