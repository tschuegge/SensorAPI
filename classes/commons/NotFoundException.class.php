<?php
namespace DanuserWebservice\SensorApi\commons;

/**
 * Exception wenn ein Datensatz nicht gefunden wurde, löst einen HTTP 404 aus
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */
class NotFoundException extends \Exception
{
    public function __construct($modelname, $keyvalue)
    {
        parent::__construct($modelname . " with ID " . $keyvalue . " not found");
    }
}
