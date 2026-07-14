<?php

declare(strict_types=1);

namespace App\Modelos;

use Config\Database;
use PDO;

/**
 * Gestiona usuarios administrativos, roles y bitácora de accesos.
 */
final class Usuario
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query(
            'SELECT u.*, GROUP_CONCAT(r.nombre SEPARATOR ", ") AS roles
             FROM usuarios u
             LEFT JOIN usuario_rol ur ON ur.usuario_id = u.id
             LEFT JOIN roles r ON r.id = ur.rol_id
             GROUP BY u.id
             ORDER BY u.fecha_registro DESC'
        );
        return $statement->fetchAll();
    }

    /**
     * @return array<string,mixed>|null
     */
    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM usuarios WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        if (!$row) {
            return null;
        }
        $row['roles_ids'] = $this->roleIds($id);
        return $row;
    }

    /**
     * @return array<string,mixed>|null
     */
    public function findByUsername(string $usuario): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM usuarios WHERE usuario = :usuario');
        $statement->execute(['usuario' => $usuario]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $statement = $this->db->prepare(
                'INSERT INTO usuarios (nombre, correo, usuario, password_hash, activo)
                 VALUES (:nombre, :correo, :usuario, :password_hash, :activo)'
            );
            $statement->execute([
                'nombre' => $data['nombre'],
                'correo' => $data['correo'],
                'usuario' => $data['usuario'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'activo' => $data['activo'] ? 1 : 0,
            ]);
            $id = (int) $this->db->lastInsertId();
            $this->syncRoles($id, $data['roles'] ?? []);
            $this->db->commit();
            return $id;
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $this->db->beginTransaction();
        try {
            $sql = 'UPDATE usuarios
                    SET nombre = :nombre, correo = :correo, usuario = :usuario, activo = :activo';
            $params = [
                'id' => $id,
                'nombre' => $data['nombre'],
                'correo' => $data['correo'],
                'usuario' => $data['usuario'],
                'activo' => $data['activo'] ? 1 : 0,
            ];
            if (!empty($data['password'])) {
                $sql .= ', password_hash = :password_hash';
                $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            $sql .= ' WHERE id = :id';

            $statement = $this->db->prepare($sql);
            $statement->execute($params);
            $this->syncRoles($id, $data['roles'] ?? []);
            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function toggleActive(int $id): void
    {
        $statement = $this->db->prepare(
            'UPDATE usuarios SET activo = NOT activo, intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
    }

    /**
     * @param array<int,int|string> $roles
     */
    private function syncRoles(int $usuarioId, array $roles): void
    {
        $delete = $this->db->prepare('DELETE FROM usuario_rol WHERE usuario_id = :id');
        $delete->execute(['id' => $usuarioId]);

        $insert = $this->db->prepare('INSERT INTO usuario_rol (usuario_id, rol_id) VALUES (:usuario_id, :rol_id)');
        foreach (array_unique(array_map('intval', $roles)) as $rolId) {
            if ($rolId > 0) {
                $insert->execute(['usuario_id' => $usuarioId, 'rol_id' => $rolId]);
            }
        }
    }

    /**
     * @return array<int,int>
     */
    private function roleIds(int $usuarioId): array
    {
        $statement = $this->db->prepare('SELECT rol_id FROM usuario_rol WHERE usuario_id = :id');
        $statement->execute(['id' => $usuarioId]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    public function incrementFailedAttempts(int $id): void
    {
        $statement = $this->db->prepare(
            'UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
    }

    public function block(int $id, int $minutes = 30): void
    {
        $statement = $this->db->prepare(
            'UPDATE usuarios SET bloqueado_hasta = DATE_ADD(NOW(), INTERVAL :minutes MINUTE), intentos_fallidos = 0 WHERE id = :id'
        );
        $statement->execute(['id' => $id, 'minutes' => $minutes]);
    }

    public function resetAttempts(int $id): void
    {
        $statement = $this->db->prepare(
            'UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL, ultimo_acceso = NOW() WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
    }

    public function logAccess(
        ?int $usuarioId,
        ?string $usuario,
        string $intento,
        bool $anomalia = false,
        ?string $detalle = null
    ): void {
        $statement = $this->db->prepare(
            'INSERT INTO bitacora_accesos (usuario_id, usuario, ip, intento, anomalia, detalle)
             VALUES (:usuario_id, :usuario, :ip, :intento, :anomalia, :detalle)'
        );
        $statement->execute([
            'usuario_id' => $usuarioId,
            'usuario' => $usuario,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'intento' => $intento,
            'anomalia' => $anomalia ? 1 : 0,
            'detalle' => $detalle,
        ]);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function accessLogs(int $limit = 100): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM bitacora_accesos ORDER BY fecha DESC LIMIT :limit'
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function roles(): array
    {
        return $this->db->query('SELECT * FROM roles ORDER BY nombre')->fetchAll();
    }

    /**
     * @return array<int,int>
     */
    public function permissionIds(int $usuarioId): array
    {
        $statement = $this->db->prepare(
            'SELECT DISTINCT rp.permiso_id
             FROM usuario_rol ur
             INNER JOIN rol_permiso rp ON rp.rol_id = ur.rol_id
             INNER JOIN roles r ON r.id = ur.rol_id
             WHERE ur.usuario_id = :id AND r.activo = 1'
        );
        $statement->execute(['id' => $usuarioId]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }
}
