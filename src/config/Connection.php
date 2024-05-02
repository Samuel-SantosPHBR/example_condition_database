<?php

namespace App\Config;

class Connection {
    private static \PDO $instace;

    public static function getInstance(): \PDO {
        if (!isset(self::$instace)) {
            self::$instace = new \PDO('mysql:host=localhost;dbname=laravel', 'root', '');
        }

        return self::$instace;
    }
} 