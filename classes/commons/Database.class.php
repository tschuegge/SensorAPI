<?php
namespace DanuserWebservice\SensorApi\commons;

/**
 * Geöffnete Datenbankverbindung
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */
class Database
{
    private static $instance;
    private $connection;

    /**
     * Konstruktor (private da Singleton)
     */
    private function __construct()
    {
        $this->connection = new \PDO("mysql:hostname=" . CurrentSettings::getCurrent()->database_host . ";port=" . CurrentSettings::getCurrent()->database_port . ";dbname=" . CurrentSettings::getCurrent()->database_name, CurrentSettings::getCurrent()->database_user, CurrentSettings::getCurrent()->database_password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Gibt die DB-Connection zurück
     */
    public static function getConn() : \PDO
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
