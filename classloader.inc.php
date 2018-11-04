<?php

/**
 * Lädt alle benötigen Klassen und Libaries
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */

// Libaries
require_once(__DIR__ . "/vendor/autoload.php");

// Eigene Klassen nach Namespaces
const NAMESPACE_PREFIX = 'DanuserWebservice\SensorApi';
spl_autoload_register(function ($classname) {
    if (substr($classname, 0, strlen(NAMESPACE_PREFIX)) === NAMESPACE_PREFIX) {
        $classname = str_replace(NAMESPACE_PREFIX, "", $classname);
        $classname = str_replace("\\", "/", $classname);
        require_once __DIR__ . "/classes" . $classname . ".class.php";
    }
});
