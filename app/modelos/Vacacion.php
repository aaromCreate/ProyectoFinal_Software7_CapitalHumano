<?php

declare(strict_types=1);

namespace App\Modelos;

use Config\Database;
use DateTimeImmutable;
use PDO;

/**
 * Gestiona solicitudes de vacaciones y calculo de dias generados.
 */
final class Vacacion
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
        return $this->db->query(
            'SELECT v.*,
                    CONCAT(c.primer_nombre, " ", c.primer_apellido) AS colaborador
             FROM solicitudes_vacaciones v
             INNER JOIN colaboradores c ON c.id = v.colaborador_id
             ORDER BY v.fecha_registro DESC'
        )->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function porColaborador(int $colaboradorId): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM solicitudes_vacaciones WHERE colaborador_id = :id ORDER BY fecha_registro DESC'
        );
        $statement->execute(['id' => $colaboradorId]);
        return $statement->fetchAll();
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): int
    {
        $diasSolicitados = $this->diasEntre((string) $data['fecha_inicio'], (string) $data['fecha_fin']);
        $diasGenerados = $this->diasGenerados((int) $data['colaborador_id']);

        $statement = $this->db->prepare(
            'INSERT INTO solicitudes_vacaciones
             (colaborador_id, fecha_inicio, fecha_fin, dias_solicitados, dias_generados, observaciones)
             VALUES
             (:colaborador_id, :fecha_inicio, :fecha_fin, :dias_solicitados, :dias_generados, :observaciones)'
        );
        $statement->execute([
            'colaborador_id' => $data['colaborador_id'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'dias_solicitados' => $diasSolicitados,
            'dias_generados' => $diasGenerados,
            'observaciones' => $data['observaciones'] !== '' ? $data['observaciones'] : null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function aprobar(int $id): void
    {
        $statement = $this->db->prepare(
            "UPDATE solicitudes_vacaciones SET estado = 'Aprobada' WHERE id = :id"
        );
        $statement->execute(['id' => $id]);
    }

    public function rechazar(int $id): void
    {
        $statement = $this->db->prepare(
            "UPDATE solicitudes_vacaciones SET estado = 'Rechazada' WHERE id = :id"
        );
        $statement->execute(['id' => $id]);
    }

    public function diasGenerados(int $colaboradorId): float
    {
        $statement = $this->db->prepare(
            'SELECT fecha_inicio FROM perfiles_laborales
             WHERE colaborador_id = :id AND cargo_activo = 1
             ORDER BY fecha_inicio ASC LIMIT 1'
        );
        $statement->execute(['id' => $colaboradorId]);
        $fechaInicio = $statement->fetchColumn();
        if ($fechaInicio === false) {
            return 0;
        }

        $inicio = new DateTimeImmutable((string) $fechaInicio);
        $hoy = new DateTimeImmutable();
        $meses = (int) $inicio->diff($hoy)->y * 12 + (int) $inicio->diff($hoy)->m;
        $dias = $meses * (30 / 11);

        return round($dias, 2);
    }

    private function diasEntre(string $inicio, string $fin): int
    {
        $start = new DateTimeImmutable($inicio);
        $end = new DateTimeImmutable($fin);
        return (int) $start->diff($end)->days + 1;
    }
}
