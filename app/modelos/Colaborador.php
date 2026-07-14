<?php

declare(strict_types=1);

namespace App\Modelos;

use App\Servicios\IntegrityService;
use Config\Database;
use DateTimeImmutable;
use PDO;

/**
 * Gestiona colaboradores y perfiles laborales.
 */
final class Colaborador
{
    private PDO $db;
    private IntegrityService $integrity;

    public function __construct(?PDO $db = null, ?IntegrityService $integrity = null)
    {
        $this->db = $db ?? Database::getConnection();
        $this->integrity = $integrity ?? new IntegrityService();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function all(?string $search = null): array
    {
        [$where, $params] = $this->buildFilterSql($search, null, null);
        $statement = $this->db->prepare($this->baseSelect() . ' ' . $where . ' ORDER BY c.fecha_registro DESC, c.id DESC');
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * @return array<string,mixed>
     */
    public function paginated(?string $search = null, int $page = 1, int $perPage = 15, ?string $sexo = null, ?string $rangoEdad = null): array
    {
        [$where, $params] = $this->buildFilterSql($search, $sexo, $rangoEdad);
        $offset = ($page - 1) * $perPage;

        $selectSql = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $this->baseSelect(), 1);
        $sql = $selectSql . ' ' . $where . ' ORDER BY c.fecha_registro DESC, c.id DESC LIMIT :limit OFFSET :offset';

        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $statement->bindValue('limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll();
        foreach ($rows as &$row) {
            $verification = $this->integrity->verifyPerfilLaboral($row, $row['firma_integridad'] ?? null);
            $row['integrity_valid'] = $verification['valid'];
            $row['integrity_message'] = $verification['message'];
        }

        $total = (int) $this->db->query('SELECT FOUND_ROWS()')->fetchColumn();

        return [
            'items' => $rows,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * @return array<string,mixed>|null
     */
    public function find(int $id): ?array
    {
        $statement = $this->db->prepare($this->baseSelect() . ' WHERE c.id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        if (!$row) {
            return null;
        }

        $row['perfiles'] = $this->perfiles($id);

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
                'INSERT INTO colaboradores
                (identidad, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                 fecha_nacimiento, sexo, direccion, correo, telefono, celular, fotografia,
                 estado_colaborador_id, empleado_activo)
                VALUES
                (:identidad, :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido,
                 :fecha_nacimiento, :sexo, :direccion, :correo, :telefono, :celular, :fotografia,
                 :estado_colaborador_id, :empleado_activo)'
            );
            $params = $this->colaboradorParams($data, true);
            $params['fotografia'] = $data['fotografia'] ?? null;
            $statement->execute($params);
            $id = (int) $this->db->lastInsertId();
            $perfilId = $this->createPerfil($id, $data);
            $this->updatePerfilSignature($perfilId);
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
            $params = $this->colaboradorParams($data, false);
            $params['id'] = $id;
            unset($params['empleado_activo']);

            $fotografiaSql = '';
            if (array_key_exists('fotografia', $data)) {
                $fotografiaSql = ', fotografia = :fotografia';
                $params['fotografia'] = $data['fotografia'] ?? null;
            }

            $statement = $this->db->prepare(
                'UPDATE colaboradores
                 SET identidad = :identidad, primer_nombre = :primer_nombre, segundo_nombre = :segundo_nombre,
                     primer_apellido = :primer_apellido, segundo_apellido = :segundo_apellido,
                     fecha_nacimiento = :fecha_nacimiento, sexo = :sexo, direccion = :direccion,
                     correo = :correo, telefono = :telefono, celular = :celular,
                     estado_colaborador_id = :estado_colaborador_id' . $fotografiaSql . '
                 WHERE id = :id'
            );
            $statement->execute($params);

            $perfilId = $this->currentPerfilId($id);
            if ($perfilId === null) {
                $perfilId = $this->createPerfil($id, $data);
            } else {
                $this->updatePerfil($perfilId, $data);
            }
            $this->updatePerfilSignature($perfilId);
            $this->refreshEmpleadoActivo($id);
            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * @param array<string,mixed> $data
     */
    public function promote(int $id, array $data): void
    {
        $this->db->beginTransaction();

        try {
            $previousEnd = $this->previousDay((string) $data['fecha_inicio']);
            $statement = $this->db->prepare(
                'UPDATE perfiles_laborales
                 SET cargo_activo = 0, es_activo = 0, empleado_activo = 0,
                     fecha_fin = COALESCE(fecha_fin, :fecha_fin)
                 WHERE colaborador_id = :id AND cargo_activo = 1'
            );
            $statement->execute(['fecha_fin' => $previousEnd, 'id' => $id]);

            $perfilId = $this->createPerfil($id, $data);
            $this->updatePerfilSignature($perfilId);
            $this->refreshEmpleadoActivo($id);
            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * @param array<string,mixed> $data
     */
    public function baja(int $id, array $data): void
    {
        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare(
                'UPDATE perfiles_laborales
                 SET cargo_activo = 0, es_activo = 0, empleado_activo = 0,
                     fecha_fin = :fecha_fin, motivo_terminacion_id = :motivo_terminacion_id
                 WHERE colaborador_id = :id AND cargo_activo = 1'
            );
            $statement->execute([
                'fecha_fin' => $data['fecha_fin'],
                'motivo_terminacion_id' => $data['motivo_terminacion_id'],
                'id' => $id,
            ]);

            $statement = $this->db->prepare(
                'UPDATE colaboradores
                 SET empleado_activo = 0, estado_colaborador_id = 5, motivo_baja_id = :motivo
                 WHERE id = :id'
            );
            $statement->execute(['motivo' => $data['motivo_terminacion_id'], 'id' => $id]);

            $perfilId = $this->currentPerfilId($id);
            if ($perfilId !== null) {
                $this->updatePerfilSignature($perfilId);
            }
            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function reintegrar(int $id): void
    {
        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare(
                'UPDATE perfiles_laborales
                 SET cargo_activo = 1, es_activo = 1, empleado_activo = 1,
                     fecha_fin = NULL, motivo_terminacion_id = NULL
                 WHERE colaborador_id = :id AND cargo_activo = 0
                 ORDER BY fecha_inicio DESC, id DESC
                 LIMIT 1'
            );
            $statement->execute(['id' => $id]);

            $statement = $this->db->prepare(
                'UPDATE colaboradores
                 SET empleado_activo = 1, estado_colaborador_id = 1, motivo_baja_id = NULL
                 WHERE id = :id'
            );
            $statement->execute(['id' => $id]);

            $perfilId = $this->currentPerfilId($id);
            if ($perfilId !== null) {
                $this->updatePerfilSignature($perfilId);
            }
            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function delete(int $id): void
    {
        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare('DELETE FROM perfiles_laborales WHERE colaborador_id = :id');
            $statement->execute(['id' => $id]);
            $statement = $this->db->prepare('DELETE FROM colaboradores WHERE id = :id');
            $statement->execute(['id' => $id]);
            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function report(?string $search = null, ?string $sexo = null, ?string $rangoEdad = null): array
    {
        [$where, $params] = $this->buildFilterSql($search, $sexo, $rangoEdad);
        $statement = $this->db->prepare($this->baseSelect() . ' ' . $where . ' ORDER BY c.fecha_registro DESC, c.id DESC');
        $statement->execute($params);
        $rows = $statement->fetchAll();
        foreach ($rows as &$row) {
            $verification = $this->integrity->verifyPerfilLaboral($row, $row['firma_integridad'] ?? null);
            $row['integrity_valid'] = $verification['valid'];
            $row['integrity_message'] = $verification['message'];
        }

        return $rows;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    /**
     * @return array{0: string, 1: array<string,mixed>}
     */
    private function buildFilterSql(?string $search, ?string $sexo, ?string $rangoEdad): array
    {
        $conditions = [];
        $params = [];

        $trimmed = trim((string) $search);
        if ($trimmed !== '') {
            $conditions[] = '(c.identidad LIKE :q1
                           OR c.primer_nombre LIKE :q2
                           OR c.segundo_nombre LIKE :q3
                           OR c.primer_apellido LIKE :q4
                           OR c.segundo_apellido LIKE :q5
                           OR c.correo LIKE :q6
                           OR c.direccion LIKE :q7
                           OR o.OCUPACION LIKE :q8)';
            $needle = '%' . $trimmed . '%';
            for ($i = 1; $i <= 8; $i++) {
                $params['q' . $i] = $needle;
            }
        }

        if ($sexo !== null && $sexo !== '') {
            $conditions[] = 'c.sexo = :sexo';
            $params['sexo'] = $sexo;
        }

        if ($rangoEdad !== null && $rangoEdad !== '') {
            $conditions[] = match ($rangoEdad) {
                '18-25' => 'TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25',
                '26-35' => 'TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35',
                '36-45' => 'TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45',
                '46-55' => 'TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55',
                '56+' => 'TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) >= 56',
                default => '1=1',
            };
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return [$where, $params];
    }

    public function estadisticasPorSexo(): array
    {
        return $this->db->query(
            "SELECT sexo, COUNT(*) AS total FROM colaboradores GROUP BY sexo"
        )->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function estadisticasPorEdad(): array
    {
        return $this->db->query(
            "SELECT CASE
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25 THEN '18-25'
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35 THEN '26-35'
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45 THEN '36-45'
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55 THEN '46-55'
                ELSE '56+'
            END AS rango, COUNT(*) AS total
            FROM colaboradores
            GROUP BY rango
            ORDER BY rango"
        )->fetchAll();
    }

    private function baseSelect(): string
    {
        return 'SELECT
                    c.id,
                    c.identidad,
                    c.primer_nombre,
                    c.segundo_nombre,
                    c.primer_apellido,
                    c.segundo_apellido,
                    CONCAT(c.primer_nombre, " ", c.primer_apellido) AS nombre_completo,
                    c.fecha_nacimiento,
                    TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) AS edad,
                    c.sexo,
                    c.direccion,
                    c.correo,
                    c.telefono,
                    c.celular,
                    c.empleado_activo,
                    c.fotografia,
                    c.fecha_registro,
                    ec.nombre AS estado_colaborador,
                    o.OCUPACION AS ocupacion,
                    te.Nombre AS tipo_empleado,
                    tp.nombre AS planilla,
                    d.nombre AS departamento,
                    pl.ocupacion_id,
                    pl.tipo_empleado_id,
                    pl.planilla_id,
                    pl.departamento_id,
                    pl.salario,
                    pl.fecha_inicio,
                    pl.fecha_fin,
                    pl.cargo_activo,
                    pl.firma_integridad,
                    mt.MOTIVO AS motivo_baja,
                    c.id AS codigo_empleado
                FROM colaboradores c
                LEFT JOIN cat_estados_colaborador ec ON ec.id = c.estado_colaborador_id
                LEFT JOIN perfiles_laborales pl ON pl.colaborador_id = c.id AND pl.cargo_activo = 1
                LEFT JOIN cat_ocupaciones o ON o.C_OCUP = pl.ocupacion_id
                LEFT JOIN cat_tipoempleado te ON te.id = pl.tipo_empleado_id
                LEFT JOIN cat_tipos_planilla tp ON tp.id = pl.planilla_id
                LEFT JOIN departamentos d ON d.id = pl.departamento_id
                LEFT JOIN cat_motivos_terminacion mt ON mt.C_TERMINACION = c.motivo_baja_id';
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function colaboradorParams(array $data, bool $includeActivo): array
    {
        $params = [
            'identidad' => $data['identidad'],
            'primer_nombre' => $data['primer_nombre'],
            'segundo_nombre' => $data['segundo_nombre'] !== '' ? $data['segundo_nombre'] : null,
            'primer_apellido' => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'] !== '' ? $data['segundo_apellido'] : null,
            'fecha_nacimiento' => $data['fecha_nacimiento'] !== '' ? $data['fecha_nacimiento'] : null,
            'sexo' => $data['sexo'],
            'direccion' => $data['direccion'] !== '' ? $data['direccion'] : null,
            'correo' => $data['correo'],
            'telefono' => $data['telefono'] !== '' ? $data['telefono'] : null,
            'celular' => $data['celular'],
            'estado_colaborador_id' => $data['estado_colaborador_id'] > 0 ? $data['estado_colaborador_id'] : 1,
        ];

        if ($includeActivo) {
            $params['empleado_activo'] = 1;
        }

        return $params;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function createPerfil(int $colaboradorId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO perfiles_laborales
             (colaborador_id, ocupacion_id, tipo_empleado_id, planilla_id, departamento_id, salario, fecha_inicio, cargo_activo, es_activo, empleado_activo)
             VALUES
             (:colaborador_id, :ocupacion_id, :tipo_empleado_id, :planilla_id, :departamento_id, :salario, :fecha_inicio, 1, 1, 1)'
        );
        $statement->execute([
            'colaborador_id' => $colaboradorId,
            'ocupacion_id' => $data['ocupacion_id'],
            'tipo_empleado_id' => $data['tipo_empleado_id'],
            'planilla_id' => $data['planilla_id'],
            'departamento_id' => $data['departamento_id'] > 0 ? $data['departamento_id'] : null,
            'salario' => $data['salario'],
            'fecha_inicio' => $data['fecha_inicio'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @param array<string,mixed> $data
     */
    private function updatePerfil(int $perfilId, array $data): void
    {
        $statement = $this->db->prepare(
            'UPDATE perfiles_laborales
             SET ocupacion_id = :ocupacion_id,
                 tipo_empleado_id = :tipo_empleado_id,
                 planilla_id = :planilla_id,
                 departamento_id = :departamento_id,
                 salario = :salario,
                 fecha_inicio = :fecha_inicio,
                 fecha_fin = :fecha_fin
             WHERE id = :id'
        );
        $statement->execute([
            'ocupacion_id' => $data['ocupacion_id'],
            'tipo_empleado_id' => $data['tipo_empleado_id'],
            'planilla_id' => $data['planilla_id'],
            'departamento_id' => $data['departamento_id'] > 0 ? $data['departamento_id'] : null,
            'salario' => $data['salario'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'] !== '' ? $data['fecha_fin'] : null,
            'id' => $perfilId,
        ]);
    }

    private function updatePerfilSignature(int $perfilId): void
    {
        $statement = $this->db->prepare(
            'SELECT p.*, c.identidad
             FROM perfiles_laborales p
             INNER JOIN colaboradores c ON c.id = p.colaborador_id
             WHERE p.id = :id'
        );
        $statement->execute(['id' => $perfilId]);
        $row = $statement->fetch();
        if (!$row) {
            throw new \RuntimeException('No fue posible cargar el perfil laboral para firmarlo.');
        }

        $signature = $this->integrity->signPerfilLaboral($row);
        $statement = $this->db->prepare('UPDATE perfiles_laborales SET firma_integridad = :firma WHERE id = :id');
        $statement->execute(['firma' => $signature, 'id' => $perfilId]);
    }

    private function currentPerfilId(int $colaboradorId): ?int
    {
        $statement = $this->db->prepare(
            'SELECT id FROM perfiles_laborales
             WHERE colaborador_id = :id
             ORDER BY cargo_activo DESC, fecha_inicio DESC, id DESC
             LIMIT 1'
        );
        $statement->execute(['id' => $colaboradorId]);
        $id = $statement->fetchColumn();

        return $id !== false ? (int) $id : null;
    }

    private function refreshEmpleadoActivo(int $colaboradorId): void
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM perfiles_laborales WHERE colaborador_id = :id AND cargo_activo = 1');
        $statement->execute(['id' => $colaboradorId]);
        $active = (int) $statement->fetchColumn() > 0 ? 1 : 0;
        $statement = $this->db->prepare('UPDATE colaboradores SET empleado_activo = :activo WHERE id = :id');
        $statement->execute(['activo' => $active, 'id' => $colaboradorId]);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function perfiles(int $id): array
    {
        $statement = $this->db->prepare(
            'SELECT p.*, c.identidad, o.OCUPACION AS ocupacion, te.Nombre AS tipo_empleado,
                    tp.nombre AS planilla, d.nombre AS departamento, mt.MOTIVO AS motivo_baja
             FROM perfiles_laborales p
             INNER JOIN colaboradores c ON c.id = p.colaborador_id
             INNER JOIN cat_ocupaciones o ON o.C_OCUP = p.ocupacion_id
             INNER JOIN cat_tipoempleado te ON te.id = p.tipo_empleado_id
             INNER JOIN cat_tipos_planilla tp ON tp.id = p.planilla_id
             LEFT JOIN departamentos d ON d.id = p.departamento_id
             LEFT JOIN cat_motivos_terminacion mt ON mt.C_TERMINACION = p.motivo_terminacion_id
             WHERE p.colaborador_id = :id
             ORDER BY p.fecha_inicio DESC, p.id DESC'
        );
        $statement->execute(['id' => $id]);
        $rows = $statement->fetchAll();
        foreach ($rows as &$row) {
            $verification = $this->integrity->verifyPerfilLaboral($row, $row['firma_integridad'] ?? null);
            $row['integrity_valid'] = $verification['valid'];
            $row['integrity_message'] = $verification['message'];
        }

        return $rows;
    }

    private function previousDay(string $date): string
    {
        return (new DateTimeImmutable($date))->modify('-1 day')->format('Y-m-d');
    }
}
