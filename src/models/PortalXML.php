<?php
namespace App\Models;
use App\Config\Connection;

class PortalXML {
    public $id;
    public $portal_id;
    public $header;
    public $content;
    public $footer;

    public static function findById(int $id) {
        $stmt = Connection::getInstance()->prepare("select * from portal_xml where portal_id = $id limit 1");
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\Models\PortalXML');
        return $stmt->fetch();
    }
}
