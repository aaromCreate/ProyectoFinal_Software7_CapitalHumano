<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Colaborador;
use App\Servicios\AuthService;
use App\Servicios\ExcelExportService;
use App\Utilidades\Sanitizer;

/**
 * Genera el reporte visual, estadisticas y exportable de colaboradores.
 */
final class ReporteController
{
    public function index(): void
    {
        AuthService::ensure('reportes', 'ver');
        $q = Sanitizer::text($_GET['q'] ?? '');
        $sexo = Sanitizer::text($_GET['sexo'] ?? '');
        $edad = Sanitizer::text($_GET['edad'] ?? '');
        $page = Sanitizer::int($_GET['page'] ?? 1);
        $page = $page < 1 ? 1 : $page;

        $modelo = new Colaborador();
        $paginacion = $modelo->paginated($q, $page, 15, $sexo, $edad);
        render_view('reportes/index', [
            'title' => 'Reporte de colaboradores',
            'rows' => $paginacion['items'],
            'paginacion' => $paginacion,
            'q' => $q,
            'sexo' => $sexo,
            'edad' => $edad,
            'por_sexo' => $modelo->estadisticasPorSexo(),
            'por_edad' => $modelo->estadisticasPorEdad(),
        ]);
    }

    public function export(): void
    {
        AuthService::ensure('reportes', 'exportar');
        $q = Sanitizer::text($_GET['q'] ?? '');
        $sexo = Sanitizer::text($_GET['sexo'] ?? '');
        $edad = Sanitizer::text($_GET['edad'] ?? '');
        (new ExcelExportService())->download((new Colaborador())->report($q, $sexo, $edad));
    }
}
