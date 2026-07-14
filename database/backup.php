<?php

declare(strict_types=1);

/**
 * Genera un backup SQL de la base CapitalHumano.
 *
 * Uso desde la terminal:
 *   php database/backup.php
 *
 * El archivo se guarda en almacenamiento/exports/.
 */

$root = dirname(__DIR__);
$envFile = $root . DIRECTORY_SEPARATOR . '.env';
$env = [
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'CapitalHumano',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => '',
];

if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (array_key_exists($key, $env)) {
            $env[$key] = trim($value, "\"'");
        }
    }
}

$backupDir = $root . DIRECTORY_SEPARATOR . 'almacenamiento' . DIRECTORY_SEPARATOR . 'exports';
if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true) && !is_dir($backupDir)) {
    fwrite(STDERR, "No se pudo crear la carpeta de backups: {$backupDir}\n");
    exit(1);
}

$filename = 'backup_' . $env['DB_DATABASE'] . '_' . date('Y-m-d_H-i-s') . '.sql';
$destination = $backupDir . DIRECTORY_SEPARATOR . $filename;

$passwordPart = $env['DB_PASSWORD'] !== '' ? "-p" . escapeshellarg($env['DB_PASSWORD']) : '';
$command = sprintf(
    'mysqldump -h %s -P %s -u %s %s %s > %s',
    escapeshellarg($env['DB_HOST']),
    escapeshellarg($env['DB_PORT']),
    escapeshellarg($env['DB_USERNAME']),
    $passwordPart,
    escapeshellarg($env['DB_DATABASE']),
    escapeshellarg($destination)
);

exec($command . ' 2>&1', $output, $exitCode);
if ($exitCode !== 0) {
    fwrite(STDERR, "Error al generar el backup.\n" . implode("\n", $output) . "\n");
    exit($exitCode);
}

echo "Backup creado: {$destination}\n";
