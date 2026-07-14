<?php

declare(strict_types=1);

namespace Api\Controladores;

use App\Modelos\Colaborador;
use App\Servicios\AuthService;

/**
 * API REST para entregar datos a la Contraloria General.
 */
final class ApiController
{
    public function porSexo(): never
    {
        AuthService::ensure('api', 'consultar');

        header('Content-Type: application/json; charset=utf-8');
        $data = (new Colaborador())->estadisticasPorSexo();
        $response = [
            'fuente' => 'Sistema de Capital Humano',
            'fecha' => date('Y-m-d H:i:s'),
            'total_colaboradores' => array_sum(array_column($data, 'total')),
            'por_sexo' => $data,
        ];
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
