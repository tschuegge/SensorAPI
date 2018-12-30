<?php
require_once(__DIR__ . "/../../classloader.inc.php");

use PHPUnit\Framework\TestCase;
use Httpful\Request;
use DanuserWebservice\SensorApi\commons\Database;
use DanuserWebservice\SensorApi\commons\CurrentSettings;

abstract class RestApiTestCase extends TestCase
{
    public static $baseurl = "http://localhost:10085/api/";

    /**
     * Testdaten einfügen
     */
    protected function setupTestData()
    {
        CurrentSettings::getCurrent("localhost");
        Database::getConn()->exec("INSERT INTO `users` VALUES (999999998, 'UnitTestUser1', 'UnitTestClass'), (999999999, 'UnitTestUser2', 'UnitTestClass')");
        Database::getConn()->exec("INSERT INTO `sensors` VALUES (999999996, 'UnitTestSensor1User1', 999999998), (999999997,'UnitTestSensor2User1' ,999999998), (999999998, 'UnitTestSensor1User2', 999999999), (999999999, 'UnitTestSensor2User2', 999999999)");
        Database::getConn()->exec("INSERT INTO `sensordata` VALUES (999999988, 1.1, '2018-10-03 01:00:00', 999999996), (999999989, 2.22, '2018-10-03 02:00:00', 999999996), (999999990, 3.333, '2018-10-03 03:00:00', 999999996), (999999991, 4.4444, '2018-10-03 04:00:00', 999999997), (999999992, 5.55555, '2018-10-03 05:00:00', 999999997), (999999993, 6.666666, '2018-10-03 06:00:00', 999999997), (999999994, 7.7777777, '2018-10-03 07:00:00', 999999998), (999999995, 8.88888888, '2018-10-03 08:00:00', 999999998), (999999996, 9.999999999, '2018-10-03 09:00:00', 999999998), (999999997, 10, '2018-10-03 10:00:00', 999999999), (999999998, 11, '2018-10-03 11:00:00', 999999999), (999999999, 12, '2018-10-03 12:00:00', 999999999)");
    }

    /**
     * Änderung an der DB verwerfen
     */
    protected function cleanupTestData()
    {
        Database::getConn()->exec("DELETE FROM `users` WHERE id = 999999998 OR id = 999999999");
    }

    /**
     * Führt einen HTTP GET Request aus
     *
     * @param $endpoint string Aufzurufender Endpunkt ohne führenden Slash
     */
    protected function doHttpGetRequest(string $endpoint)
    {
        return Request::get(self::$baseurl . $endpoint)->send();
    }

    /**
     * Führt einen HTTP POST Request aus
     *
     * @param $endpoint string Aufzurufender Endpunkt ohne führenden Slash
     * @param $payload string Payload der mitgesendet wird
     */
    protected function doHttpPostRequest(string $endpoint, string $payload)
    {
        return Request::post(self::$baseurl . $endpoint)->sendsJson()->body($payload)->send();
    }

    /**
     * Führt einen HTTP PUT Request aus
     *
     * @param $endpoint string Aufzurufender Endpunkt ohne führenden Slash
     * @param $payload string Payload der mitgesendet wird
     */
    protected function doHttpPutRequest(string $endpoint, string $payload)
    {
        return Request::put(self::$baseurl . $endpoint)->sendsJson()->body($payload)->send();
    }

    /**
     * Führt einen HTTP DELETE Request aus
     *
     * @param $endpoint string Aufzurufender Endpunkt ohne führenden Slash
     */
    protected function doHttpDeleteRequest(string $endpoint)
    {
        return Request::delete(self::$baseurl . $endpoint)->send();
    }
}
