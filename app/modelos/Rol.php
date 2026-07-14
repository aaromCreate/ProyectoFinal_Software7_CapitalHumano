<?php

declare(strict_types=1);

namespace App\Modelos;

use Config\Database;
use PDO;

/**
 * Gestiona roles y sus permisos asociados.
 */
final class Rol
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
        return $this->db->query('SELECT * FROM roles ORDER BY nombre')->fetchAll();
    }

    /**
     * @return array<string,mixed>|null
     */
    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM roles WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        if (!$row) {
            return null;
        }
        $row['permisos_ids'] = $this->permissionIds($id);
        return $row;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $statement = $this->db->prepare(
                'INSERT INTO roles (nombre, descripcion, activo) VALUES (:nombre, :descripcion, :activo)'
            );
            $statement->execute([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'activo' => $data['activo'] ? 1 : 0,
            ]);
            $id = (int) $this->db->lastInsertId();
            $this->syncPermissions($id, $data['permisos'] ?? []);
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
            $statement = $this->db->prepare(
                'UPDATE roles SET nombre = :nombre, descripcion = :descripcion, activo = :activo WHERE id = :id'
            );
            $statement->execute([
                'id' => $id,
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'activo' => $data['activo'] ? 1 : 0,
            ]);
            $this->syncPermissions($id, $data['permisos'] ?? []);
            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function toggleActive(int $id): void
    {
        $statement = $this->db->prepare('UPDATE roles SET activo = NOT activo WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /**
     * @param array<int,int|string> $permisos
     */
    private function syncPermissions(int $rolId, array $permisos): void
    {
        $delete = $this->db->prepare('DELETE FROM rol_permiso WHERE rol_id = :id');
        $delete->execute(['id' => $rolId]);

        $insert = $this->db->prepare('INSERT INTO rol_permiso (rol_id, permiso_id) VALUES (:rol_id, :permiso_id)');
        foreach (array_unique(array_map('intval', $permisos)) as $permisoId) {
            if ($permisoId > 0) {
                $insert->execute(['rol_id' => $rolId, 'permiso_id' => $permisoId]);
            }
        }
    }

    /**
     * @return array<int,int>
     */
    private function permissionIds(int $rolId): array
    {
        $statement = $this->db->prepare('SELECT permiso_id FROM rol_permiso WHERE rol_id = :id');
        $statement->execute(['id' => $rolId]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function permissions(): array
    {
        return $this->db->query('SELECT * FROM permisos ORDER BY modulo, accion')->fetchAll();
    }

    /**
     * @return array<string,array<int,array<string,mixed>>>
     */
    public function permissionsByModule(): array
    {
        $rows = $this->permissions();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['modulo']][] = $row;
        }
        return $grouped;
    }
}
