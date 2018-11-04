<?php
namespace DanuserWebservice\SensorApi\config;

use DanuserWebservice\SensorApi\models\Settings;

/**
 * Beispiel Settingsdatei
 * Name der Datei muss dem Host (ohne www) entsprechen (z.B. localhost.settings.php oder api.myhost.com.settings.php)
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */
$settings = new Settings();

$settings->database_host = "localhost";
$settings->database_name = "dbname";
$settings->database_port = 3306;
$settings->database_user = "dbuser";
$settings->database_password = "dbpassword";
