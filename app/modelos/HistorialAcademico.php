<?php

declare(strict_types=1);

namespace App\Modelos;

use Config\Database;
use PDO;

/**
 * Gestiona historial academico en PDF.
 */
final class HistorialAcademico
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function porColaborador(int $colaboradorId): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM historial_academico WHERE colaborador_id = :id ORDER BY fecha_registro DESC'
        );
        $statement->execute(['id' => $colaboradorId]);
        return $statement->fetchAll();
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO historial_academico (colaborador_id, titulo, institucion, archivo_pdf)
             VALUES (:colaborador_id, :titulo, :institucion, :archivo_pdf)'
        );
        $statement->execute([
            'colaborador_id' => $data['colaborador_id'],
            'titulo' => $data['titulo'],
            'institucion' => $data['institucion'],
            'archivo_pdf' => $data['archivo_pdf'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM historial_academico WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
