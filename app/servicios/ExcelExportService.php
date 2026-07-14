<?php

declare(strict_types=1);

namespace App\Servicios;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Exporta el reporte de colaboradores en formato XLSX.
 */
final class ExcelExportService
{
    /**
     * @param array<int,array<string,mixed>> $rows
     */
    public function download(array $rows): never
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte');
        $sheet->setCellValue('A1', 'Reporte de colaboradores');
        $sheet->setCellValue('A2', 'Generado: ' . date('Y-m-d H:i:s'));

        $headers = [
            'Codigo',
            'Documento',
            'Nombre',
            'Edad',
            'Sexo',
            'Direccion',
            'Correo',
            'Celular',
            'Puesto',
            'Tipo empleado',
            'Planilla',
            'Departamento',
            'Salario',
            'Fecha inicio',
            'Fecha fin',
            'Cargo activo',
            'Empleado activo',
            'Motivo',
            'Integridad',
        ];
        $sheet->fromArray($headers, null, 'A4');

        $rowNumber = 5;
        foreach ($rows as $row) {
            $sheet->fromArray([
                $row['codigo_empleado'],
                $row['identidad'],
                $row['nombre_completo'],
                $row['edad'] ?? 'N/A',
                $row['sexo'],
                $row['direccion'] ?? '',
                $row['correo'],
                $row['celular'],
                $row['ocupacion'],
                $row['tipo_empleado'],
                $row['planilla'],
                $row['departamento'] ?? '',
                (float) $row['salario'],
                $row['fecha_inicio'],
                $row['fecha_fin'] ?? 'N/A',
                (int) $row['cargo_activo'] === 1 ? 'Si' : 'No',
                (int) $row['empleado_activo'] === 1 ? 'Si' : 'No',
                $row['motivo_baja'] ?? 'No aplica',
                $row['integrity_message'],
            ], null, 'A' . $rowNumber);

            $color = !empty($row['integrity_valid']) ? 'C6EFCE' : 'FFC7CE';
            $sheet->getStyle('S' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
            $rowNumber++;
        }

        $sheet->getStyle('A4:S4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:S4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('146C94');
        $sheet->setAutoFilter('A4:S' . max(4, $rowNumber - 1));
        foreach (range('A', 'S') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'reporte_colaboradores_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}
