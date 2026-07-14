<?php

declare(strict_types=1);

namespace App\Servicios;

use RuntimeException;

/**
 * Sube fotografias y documentos PDF de forma segura.
 */
final class FileUploadService
{
    private const ALLOWED_IMAGES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_SIZE = 2 * 1024 * 1024; // 2MB

    public function uploadImage(array $file, string $subfolder): ?string
    {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $this->validateUpload($file, self::ALLOWED_IMAGES);

        $extension = pathinfo((string) $file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(8)) . '.' . strtolower($extension);
        $folder = ROOT_PATH . DIRECTORY_SEPARATOR . 'publico' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $subfolder;
        $this->ensureFolder($folder);
        $destination = $folder . DIRECTORY_SEPARATOR . $filename;

        if (!is_writable($folder)) {
            throw new RuntimeException("La carpeta de destino no tiene permisos de escritura: {$folder}.");
        }
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('No fue posible guardar el archivo en el servidor.');
        }

        return 'uploads/' . $subfolder . '/' . $filename;
    }

    public function uploadPdf(array $file): ?string
    {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $this->validateUpload($file, ['application/pdf']);

        $extension = pathinfo((string) $file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(8)) . '.' . strtolower($extension);
        $folder = ROOT_PATH . DIRECTORY_SEPARATOR . 'publico' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'historial';
        $this->ensureFolder($folder);
        $destination = $folder . DIRECTORY_SEPARATOR . $filename;

        if (!is_writable($folder)) {
            throw new RuntimeException("La carpeta de destino no tiene permisos de escritura: {$folder}.");
        }
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('No fue posible guardar el archivo en el servidor.');
        }

        return 'uploads/historial/' . $filename;
    }

    /**
     * @param array<int,string> $allowedTypes
     */
    private function validateUpload(array $file, array $allowedTypes): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->uploadErrorMessage($file['error']));
        }
        if (!in_array($file['type'], $allowedTypes, true)) {
            throw new RuntimeException('Tipo de archivo no permitido. Formatos validos: ' . implode(', ', $allowedTypes) . '.');
        }
        if ($file['size'] > self::MAX_SIZE) {
            throw new RuntimeException('El archivo excede el limite de ' . (self::MAX_SIZE / 1024 / 1024) . ' MB.');
        }
    }

    private function ensureFolder(string $folder): void
    {
        if (is_dir($folder)) {
            return;
        }
        if (!mkdir($folder, 0755, true) && !is_dir($folder)) {
            throw new RuntimeException("No se pudo crear la carpeta de destino: {$folder}. Verifique los permisos.");
        }
    }

    private function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el limite configurado en el servidor (upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el limite del formulario.',
            UPLOAD_ERR_PARTIAL => 'El archivo se subio parcialmente.',
            UPLOAD_ERR_NO_FILE => 'No se selecciono ningun archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'No existe la carpeta temporal del servidor.',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco.',
            UPLOAD_ERR_EXTENSION => 'Una extension de PHP detuvo la subida.',
            default => 'Error desconocido al subir el archivo.',
        };
    }
}
