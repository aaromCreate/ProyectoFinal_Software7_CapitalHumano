<?php

declare(strict_types=1);

namespace App\Modelos;

use Config\Database;
use PDO;

/**
 * Consulta los catalogos del sistema.
 */
final class Catalogo
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function ocupaciones(): array
    {
        return $this->db->query('SELECT C_OCUP AS id, OCUPACION AS nombre FROM cat_ocupaciones WHERE Activo = 1 ORDER BY OCUPACION')->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function tiposEmpleado(): array
    {
        return $this->db->query('SELECT id, TRIM(Nombre) AS nombre FROM cat_tipoempleado WHERE Activo = 1 ORDER BY Nombre')->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function tiposPlanilla(): array
    {
        return $this->db->query('SELECT id, nombre FROM cat_tipos_planilla ORDER BY id')->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function motivosTerminacion(): array
    {
        return $this->db->query('SELECT C_TERMINACION AS id, MOTIVO AS nombre FROM cat_motivos_terminacion ORDER BY MOTIVO')->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function departamentos(): array
    {
        return $this->db->query('SELECT id, nombre FROM departamentos WHERE activo = 1 ORDER BY nombre')->fetchAll();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function estadosColaborador(): array
    {
        return $this->db->query('SELECT id, nombre FROM cat_estados_colaborador ORDER BY id')->fetchAll();
    }

    /**
     * @return array<string,array<int,array<string,mixed>>>
     */
    public function formCatalogs(): array
    {
        return [
            'ocupaciones' => $this->ocupaciones(),
            'tipos_empleado' => $this->tiposEmpleado(),
            'tipos_planilla' => $this->tiposPlanilla(),
            'motivos_terminacion' => $this->motivosTerminacion(),
            'departamentos' => $this->departamentos(),
            'estados_colaborador' => $this->estadosColaborador(),
        ];
    }
}
