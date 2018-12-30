<?php
namespace DanuserWebservice\SensorApi\repositories;

use DanuserWebservice\SensorApi\commons\Database;
use DanuserWebservice\SensorApi\models\SensorData;
use DanuserWebservice\SensorApi\commons\NotFoundException;
use DanuserWebservice\SensorApi\commons\BadRequestException;
use DanuserWebservice\SensorApi\models\Sensor;

/**
 * Verwaltet Sensoren
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */
class SensorDataRepository
{
    private $userid;

    /**
     * Initialisiert ein SensorDataRepository mit einer User-ID auf der alle Operationen durchgeführt werden
     * @param $userid int User-ID auf der alle Operationen durchgeführt werden
     */
    public function __construct(int $userid)
    {
        $this->userid = $userid;
    }

    /**
     * Mappt ein Array in ein SensorData-Objekt
     * @param $sensorData array Array mit den Properties eines SensorData-Datensatzes
     */
    public function map(array $sensorData) : SensorData
    {
        if (!array_key_exists("id", $sensorData) ||
            !array_key_exists("data", $sensorData) ||
            !array_key_exists("timestamp", $sensorData) ||
            !array_key_exists("sensorid", $sensorData)) {
            throw new BadRequestException("Given data does not contain 'id', 'data', 'timestamp' and 'sensorid'");
        }
        $sensorDataObj = new SensorData();
        $sensorDataObj->id = (int)$sensorData['id'];
        $sensorDataObj->data = round($sensorData['data'], 9);
        $sensorDataObj->timestamp = $sensorData['timestamp'];
        $sensorDataObj->sensorid = (int)$sensorData['sensorid'];
        return $sensorDataObj;
    }

    /**
     * Gibt alle Sensordaten zurück
     */
    public function getAll() : array
    {
        $stmt = Database::getConn()->prepare("SELECT d.* FROM `sensordata` d INNER JOIN `sensors` s ON d.`sensorid` = s.`id` WHERE s.`userid` = :userid");
        $stmt->bindValue(":userid", $this->userid);
        $stmt->execute();

        $sensordata = array();
        while (($row = $stmt->fetch()) !== false) {
            \array_push($sensordata, $this->map($row));
        }
        $stmt->closeCursor();
        return $sensordata;
    }

    /**
     * Gibt Sensordaten eine Sensors zurück
     */
    public function getBySensor(Sensor $sensor) : array
    {
        $stmt = Database::getConn()->prepare("SELECT * FROM `sensordata` WHERE `sensorid` = :sensorid");
        $stmt->bindValue(":sensorid", $sensor->id);
        $stmt->execute();

        $sensordata = array();
        while (($row = $stmt->fetch()) !== false) {
            \array_push($sensordata, $this->map($row));
        }
        $stmt->closeCursor();
        return $sensordata;
    }

    /**
     * Gibt einen einzelnen Sensordaten-Datensatz zurück
     *
     * @param $id int ID des Sensordaten-Datensatzes
     */
    public function getById(int $id) : SensorData
    {
        $stmt = Database::getConn()->prepare("SELECT d.* FROM `sensordata` d INNER JOIN `sensors` s ON d.`sensorid` = s.`id` WHERE d.`id` = :id AND s.`userid` = :userid");
        $stmt->bindValue(":id", $id);
        $stmt->bindValue(":userid", $this->userid);
        $stmt->execute();

        $row = $stmt->fetch();
        $stmt->closeCursor();
        if ($row === false) {
            throw new NotFoundException("sensordata", $id);
        }
        return $this->map($row);
    }

    /**
     * Fügt einen Sensordaten-Datensatz hinzu und gibt dessen ID zurück
     * @param $sensor SensorData Sensordaten-Datensatz der hinzugefügt wird
     */
    public function add(SensorData $sensordata) : int
    {
        // Check Sensor
        $sensorRepo = new SensorsRepository($this->userid);
        $sensorRepo->getById($sensordata->sensorid);

        $stmt = Database::getConn()->prepare("INSERT INTO `sensordata`(`data`, `sensorid`) VALUES(:data, :sensorid)");
        $stmt->bindValue(":data", $sensordata->data);
        $stmt->bindValue(":sensorid", $sensordata->sensorid);
        $stmt->execute();
        $id = Database::getConn()->lastInsertId();
        $stmt->closeCursor();
        return $id;
    }

    /**
     * Löscht einen Sensordaten-Datensatz
     * @param $sensor SensorData Sensordaten-Datensatz der gelöscht wird 
     */
    public function delete(SensorData $sensordata) : void
    {
        $stmt = Database::getConn()->prepare("DELETE FROM `sensordata` WHERE `id` = :id");
        $stmt->bindValue(":id", $sensordata->id);
        $stmt->execute();
        $stmt->closeCursor();
    }

}
