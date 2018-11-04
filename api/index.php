<?php

use DanuserWebservice\SensorApi\commons\NotFoundException;
use DanuserWebservice\SensorApi\repositories\UsersRepository;
use DanuserWebservice\SensorApi\repositories\SensorsRepository;
use DanuserWebservice\SensorApi\models\User;
use DanuserWebservice\SensorApi\commons\BadRequestException;
use DanuserWebservice\SensorApi\repositories\SensorDataRepository;

/**
 * REST API für Sensordaten
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */

require_once("../classloader.inc.php");

// *** CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Location');
Flight::route("OPTIONS *", function () {
    Flight::halt(200);
});

// *** Security
function checkUserAndGetId($username) : int
{
    try {
        $repo = new UsersRepository();
        return $repo->getByName($username)->id;
    } catch (NotFoundException $e) {
        Flight::halt(403, $e->getMessage());
    }
}

// Typische Fehler (Hilfestellungen durch spezifische Fehlermeldung)
Flight::route("POST|PUT *", function () {
    if (Flight::request()->type != "application/json") {
        Flight::halt(400, "No JSON given, check Content-Type header");
    }
    if (!json_decode(Flight::request()->getBody())) {
        Flight::halt(400, "JSON decode error: " . json_last_error_msg());
    }
    return true;
});



// **************
// *** ROUTEN ***
// **************


// *** sensor

Flight::route("GET /@username/sensor(/@id)", function ($username, $id) {
    $repo = new SensorsRepository(checkUserAndGetId($username));
    if ($id === null) {
        Flight::json($repo->getAll());
    } else {
        try {
            Flight::json($repo->getById($id));
        } catch (NotFoundException $e) {
            Flight::halt(404, $e->getMessage());
        }
    }
});
Flight::route("POST /@username/sensor", function ($username) {
    $repo = new SensorsRepository(checkUserAndGetId($username));
    try {
        $data = $repo->map(Flight::request()->data->getData());
        $id = $repo->add($data);
        header("Location: " . ((empty($_SERVER['HTTPS'])) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "/" . $id);
        Flight::stop(204);
    } catch (BadRequestException $e) {
        Flight::halt(400, $e->getMessage());
    }
});
Flight::route("PUT /@username/sensor/@id", function ($username, $id) {
    $repo = new SensorsRepository(checkUserAndGetId($username));
    try {
        $data = $repo->map(Flight::request()->data->getData());

        try {
            $repo->getById($id);
        } catch (NotFoundException $e) {
            Flight::halt(404, $e->getMessage());
        }

        if ($data->id != $id) {
            Flight::halt(400, "Trying to PUT an sensor with ID " . $data->id . " to ID " . $id . " (set by URL)");
            return false;
        }
        $repo->edit($data);
        Flight::stop(204);
    } catch (BadRequestException $e) {
        Flight::halt(400, $e->getMessage());
    }
});
Flight::route("DELETE /@username/sensor/@id", function ($username, $id) {
    $repo = new SensorsRepository(checkUserAndGetId($username));
    try {
        $sensor = $repo->getById($id);
        $repo->delete($sensor);
        Flight::stop(204);
    } catch (NotFoundException $e) {
        Flight::halt(404, $e->getMessage());
    }
});


// *** sensordata

Flight::route("GET /@username/sensordata(/@id)", function ($username, $id) {
    $repo = new SensorDataRepository(checkUserAndGetId($username));
    if ($id === null) {
        Flight::json($repo->getAll());
    } else {
        try {
            Flight::json($repo->getById($id));
        } catch (NotFoundException $e) {
            Flight::halt(404, $e->getMessage());
        }
    }
});
Flight::route("GET /@username/sensordata/sensor/@id", function ($username, $id) {
    $sensorDataRepo = new SensorDataRepository(checkUserAndGetId($username));
    $sensorsRepo = new SensorsRepository(checkUserAndGetId($username));

    try {
        $sensor = $sensorsRepo->getById($id);
        Flight::json($sensorDataRepo->getBySensor($sensor));
    } catch (NotFoundException $e) {
        Flight::halt(404, $e->getMessage());
    }
});
Flight::route("POST /@username/sensordata", function ($username) {
    $repo = new SensorDataRepository(checkUserAndGetId($username));
    try {
        $data = $repo->map(Flight::request()->data->getData());
        $id = $repo->add($data);
        header("Location: " . ((empty($_SERVER['HTTPS'])) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "/" . $id);
        Flight::stop(204);
    } catch (BadRequestException $e) {
        Flight::halt(400, $e->getMessage());
    } catch (NotFoundException $e) {
        Flight::halt(404, $e->getMessage());
    }
});
Flight::route("PUT /@username/sensordata/@id", function ($username, $id) {
    Flight::halt(404, "PUT is not implemented for sensordata");
});
Flight::route("DELETE /@username/sensordata/@id", function ($username, $id) {
    $repo = new SensorDataRepository(checkUserAndGetId($username));
    try {
        $sensordata = $repo->getById($id);
        $repo->delete($sensordata);
        Flight::stop(204);
    } catch (NotFoundException $e) {
        Flight::halt(404, $e->getMessage());
    }
});


// *** user

Flight::route("GET /@username", function ($username) {
    try {
        $repo = new UsersRepository();
        Flight::json($repo->getByName($username));
    } catch (NotFoundException $e) {
        Flight::halt(404, $e->getMessage());
    }
});


Flight::start();
