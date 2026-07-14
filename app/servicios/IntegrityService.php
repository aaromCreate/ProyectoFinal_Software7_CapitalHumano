<?php

declare(strict_types=1);

namespace App\Servicios;

use RuntimeException;

/**
 * Firma y verifica datos sensibles de perfiles laborales con OpenSSL.
 */
final class IntegrityService
{
    private string $privateKeyPath;
    private string $publicKeyPath;

    public function __construct()
    {
        $this->privateKeyPath = ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'keys' . DIRECTORY_SEPARATOR . 'private.pem';
        $this->publicKeyPath = ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'keys' . DIRECTORY_SEPARATOR . 'public.pem';
    }

    /**
     * @param array<string,mixed> $data
     */
    public function canonicalPerfilLaboral(array $data): string
    {
        $payload = [
            'identidad' => (string) ($data['identidad'] ?? ''),
            'codigo_empleado' => (int) ($data['colaborador_id'] ?? $data['codigo_empleado'] ?? 0),
            'salario' => number_format((float) ($data['salario'] ?? 0), 2, '.', ''),
            'tipo_empleado_id' => (int) ($data['tipo_empleado_id'] ?? 0),
            'planilla_id' => (int) ($data['planilla_id'] ?? 0),
            'departamento_id' => (int) ($data['departamento_id'] ?? 0),
            'ocupacion_id' => (int) ($data['ocupacion_id'] ?? 0),
            'fecha_inicio' => (string) ($data['fecha_inicio'] ?? ''),
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('No fue posible construir los datos sensibles para firmar.');
        }

        return $json;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function signPerfilLaboral(array $data): string
    {
        return $this->signPayload($this->canonicalPerfilLaboral($data), 'No fue posible firmar el perfil laboral.');
    }

    private function signPayload(string $payload, string $errorMessage): string
    {
        $key = $this->loadPrivateKey();
        $signature = '';
        if (!openssl_sign($payload, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException($errorMessage);
        }

        return base64_encode($signature);
    }

    /**
     * @param array<string,mixed> $data
     * @return array{valid:bool,message:string}
     */
    public function verifyPerfilLaboral(array $data, ?string $signatureBase64): array
    {
        return $this->verifyPayload($this->canonicalPerfilLaboral($data), $signatureBase64);
    }

    /**
     * @return array{valid:bool,message:string}
     */
    private function verifyPayload(string $payload, ?string $signatureBase64): array
    {
        if ($signatureBase64 === null || trim($signatureBase64) === '') {
            return ['valid' => false, 'message' => 'Firma ausente'];
        }

        $signature = base64_decode($signatureBase64, true);
        if ($signature === false) {
            return ['valid' => false, 'message' => 'Firma ilegible'];
        }

        if (!is_file($this->publicKeyPath)) {
            return ['valid' => false, 'message' => 'Clave publica no encontrada'];
        }

        $key = openssl_pkey_get_public((string) file_get_contents($this->publicKeyPath));
        if ($key === false) {
            return ['valid' => false, 'message' => 'Clave publica invalida'];
        }

        $result = openssl_verify($payload, $signature, $key, OPENSSL_ALGO_SHA256);
        if ($result === 1) {
            return ['valid' => true, 'message' => 'Integridad completa'];
        }

        return ['valid' => false, 'message' => $result === 0 ? 'Registro vulnerado' : 'Error OpenSSL'];
    }

    /**
     * @return resource
     */
    private function loadPrivateKey()
    {
        if (!is_file($this->privateKeyPath)) {
            throw new RuntimeException('No existe config/keys/private.pem. Genere las llaves OpenSSL antes de guardar.');
        }

        $key = openssl_pkey_get_private((string) file_get_contents($this->privateKeyPath));
        if ($key === false) {
            throw new RuntimeException('La clave privada no pudo cargarse.');
        }

        return $key;
    }
}
