<?php
use App\Service\XmlService;

require_once 'vendor/autoload.php';

function dd(...$missed) {
    var_dump($missed);
    die;
}


echo (new XmlService)->createXML();