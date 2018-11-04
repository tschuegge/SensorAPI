<?php
namespace DanuserWebservice\SensorApi\commons;

use DanuserWebservice\SensorApi\models\Settings;

/**
 * Aktuelle Einstellungen
 *
 * @author Jürg Danuser <juerg.danuser@danuserwebservice.com>
 */
class CurrentSettings
{
    private static $instance;

    private $currentSettings;

    /**
     * Konstruktor (private da Singleton)
     * @param $forceSettingsName string Lädt zwingend die angegebenen Einstellungen
     */
    private function __construct(string $forceSettingsName = null)
    {
        $host = $forceSettingsName;
        if ($host === null) {
            $host = strtolower(str_replace("www.", "", $_SERVER['SERVER_NAME']));
        }
        $path = __DIR__ . "/../../config/" . $host . ".settings.php";
        if (!file_exists($path)) {
            throw new \Exception("No Setting found for: " . $_SERVER['SERVER_NAME']);
        }
        require $path; // muss Variable $settings beinhalten
        $this->currentSettings = $settings;
    }

    /**
     * Gibt die aktuellen Einstellungen zurück
     * @param $forceSettingsName string Lädt zwingend die angegebenen Einstellungen
     */
    public static function getCurrent(string $forceSettingsName = null) : Settings
    {
        if (self::$instance === null) {
            self::$instance = new CurrentSettings($forceSettingsName);
        }
        return self::$instance->currentSettings;
    }
}
