<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

final class PdoConnectionFactory
{
    private static ?\PDO $instance = null;

    /** @param array{host:string,port:string,database:string,user:string,password:string} $config */
    public static function create(array $config): \PDO
    {
        if (self::$instance instanceof \PDO) {
            return self::$instance;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database'],
        );

        self::$instance = new \PDO($dsn, $config['user'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$instance;
    }
}
