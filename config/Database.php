<?php

declare(strict_types=1);

namespace Config;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Gestiona una unica conexion PDO reutilizable para toda la aplicacion.
 */
final class Database
{
    private static ?PDO $connection = null;

    /**
     * Obtiene una conexion PDO con utf8mb4, excepciones y prepared statements reales.
     *
     * @throws RuntimeException Si la conexion falla.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = (string) ($_ENV['DB_HOST'] ?? '127.0.0.1');
        $port = (string) ($_ENV['DB_PORT'] ?? '3306');
        $database = (string) ($_ENV['DB_DATABASE'] ?? 'CapitalHumano');
        $username = (string) ($_ENV['DB_USERNAME'] ?? 'root');
        $password = (string) ($_ENV['DB_PASSWORD'] ?? '');

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException('No fue posible conectar con la base de datos. Revise la configuracion.', 0, $exception);
        }

        return self::$connection;
    }
}
