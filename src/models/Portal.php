<?php
namespace App\Models;

use App\Config\Connection;

class Portal {
    public $id;
    public $name;

    public static function find(): array {
        $stmt = Connection::getInstance()->prepare('select * from portal');
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\Models\Portal');
        return $stmt->fetchAll();
    }
}