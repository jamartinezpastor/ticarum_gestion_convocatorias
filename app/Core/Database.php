<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $cfg = require __DIR__ . '/../../config/config.php';
            $dsn = "mysql:host={$cfg['db']['host']};dbname={$cfg['db']['name']};charset=utf8mb4";

            self::$pdo = new PDO($dsn, $cfg['db']['user'], $cfg['db']['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
            ]);
        }
        return self::$pdo;
    }
}