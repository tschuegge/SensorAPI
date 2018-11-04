<?php
namespace DanuserWebservice\SensorApi\repositories;

use DanuserWebservice\SensorApi\commons\Database;
use DanuserWebservice\SensorApi\commons\NotFoundException;
use DanuserWebservice\SensorApi\commons\BadRequestException;
use DanuserWebservice\SensorApi\models\User;

/**
 * Verwaltet Sensoren
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */
class UsersRepository
{

    /**
     * Mappt ein Array in ein User-Objekt
     * @param $user array Array mit den Properties eines Users
     */
    public function map(array $user) : User
    {
        if (!array_key_exists("id", $user) ||
            !array_key_exists("name", $user) ||
            !array_key_exists("class", $user)) {
            throw new BadRequestException("Given data does not contain 'id', 'name' and 'class'");
        }
        $userObj = new User();
        $userObj->id = (int)$user['id'];
        $userObj->name = $user['name'];
        $userObj->class = $user['class'];
        return $userObj;
    }

    /**
     * Gibt eine einzelnen User per Name zurück
     *
     * @param $name string Name des Users
     */
    public function getByName(string $name) : User
    {
        $stmt = Database::getConn()->prepare("SELECT * FROM `users` WHERE `name` = :name");
        $stmt->bindValue(":name", $name);
        $stmt->execute();

        $row = $stmt->fetch();
        $stmt->closeCursor();
        if ($row === false) {
            throw new NotFoundException("User", $name);
        }
        return $this->map($row);
    }
}
