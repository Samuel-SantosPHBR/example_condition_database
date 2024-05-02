<?php

namespace App\Models;

use App\Config\Connection;

class PortalConditions {
    public $id;
    public $portal_id;
    public $parent_condition_id;
    public $value_to_compare;
    public $modifier;
    public $value_compared;
    public $else_condition;
    public $value;
    public $funcion_auxiliar;
    public $field;

    public static function find($id): array {
        $stmt = Connection::getInstance()->prepare("select * from portal_condition where portal_id = $id");
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\Models\PortalConditions');
        return $stmt->fetchAll();
    }
}