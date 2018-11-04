<?php
namespace DanuserWebservice\SensorApi\repositories;

use DanuserWebservice\SensorApi\commons\Database;
use DanuserWebservice\SensorApi\models\Sensor;
use DanuserWebservice\SensorApi\commons\NotFoundException;
use DanuserWebservice\SensorApi\commons\BadRequestException;

/**
 * Verwaltet Sensoren
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */
class SensorsRepository
{
    private $userid;

    /**
     * Initialisiert ein SensorsRepository mit einer User-ID auf der alle Operationen durchgeführt werden
     * @param $userid int User-ID auf der alle Operationen durchgeführt werden
     */
    public function __construct(int $userid)
    {
        $this->userid = $userid;
    }

    /**
     * Mappt ein Array in ein Sensor-Objekt
     * @param $sensor array Array mit den Properties eines Sensors
     */
    public function map(array $sensor) : Sensor
    {
        if (!array_key_exists("id", $sensor) ||
            !array_key_exists("name", $sensor) ||
            !array_key_exists("userid", $sensor)) {
            throw new BadRequestException("Given data does not contain 'id', 'name' and 'userid'");
        }
        $sensorObj = new Sensor();
        $sensorObj->id = (int)$sensor['id'];
        $sensorObj->name = $sensor['name'];
        $sensorObj->userid = (int)$sensor['userid'];
        return $sensorObj;
    }

    /**
     * Gibt alle Sensoren zurück
     */
    public function getAll() : array
    {
        $stmt = Database::getConn()->prepare("SELECT * FROM `sensors` WHERE `userid` = :userid");
        $stmt->bindValue(":userid", $this->userid);
        $stmt->execute();

        $sensors = array();
        while (($row = $stmt->fetch()) !== false) {
            \array_push($sensors, $this->map($row));
        }
        $stmt->closeCursor();
        return $sensors;
    }

    /**
     * Gibt eine einzelnen Sensor zurück
     *
     * @param $id int ID des Sensors
     */
    public function getById(int $id) : Sensor
    {
        $stmt = Database::getConn()->prepare("SELECT * FROM `sensors` WHERE `id` = :id AND `userid` = :userid");
        $stmt->bindValue(":id", $id);
        $stmt->bindValue(":userid", $this->userid);
        $stmt->execute();

        $row = $stmt->fetch();
        $stmt->closeCursor();
        if ($row === false) {
            throw new NotFoundException("sensor", $id);
        }
        return $this->map($row);
    }

    /**
     * Fügt einen Sensor hinzu und gibt dessen ID zurück
     * @param $sensor Sensor Sensor der hinzugefügt wird
     */
    public function add(Sensor $sensor) : int
    {
        $stmt = Database::getConn()->prepare("INSERT INTO `sensors`(`name`, `userid`) VALUES(:name, :userid)");
        $stmt->bindValue(":name", $sensor->name);
        $stmt->bindValue(":userid", $this->userid);
        $stmt->execute();
        $id = Database::getConn()->lastInsertId();
        $stmt->closeCursor();
        return $id;
    }

    /**
     * Ändert einen Sensor
     * @param $sensor Sensor Sensor der geändert wird
     */
    public function edit(Sensor $sensor) : void
    {
        $stmt = Database::getConn()->prepare("UPDATE `sensors` SET `name` = :name WHERE `id` = :id AND `userid` = :userid");
        $stmt->bindValue(":name", $sensor->name);
        $stmt->bindValue(":id", $sensor->id);
        $stmt->bindValue(":userid", $this->userid);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Löscht einen Sensor
     * @param $sensor Sensor Sensor der gelöscht wird 
     */
    public function delete(Sensor $sensor) : void
    {
        $stmt = Database::getConn()->prepare("DELETE FROM `sensors` WHERE `id` = :id AND `userid` = :userid");
        $stmt->bindValue(":id", $sensor->id);
        $stmt->bindValue(":userid", $this->userid);
        $stmt->execute();
        $stmt->closeCursor();
    }

}
