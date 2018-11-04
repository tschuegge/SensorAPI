Sensordaten REST API
====================

Restful HTTP API für einfache Sensordaten.

Datenmodell
-----------

### User
```json
{
    "id": 0,         // ID als Integer
    "name": "",      // Username als String
    "class": ""      // Klassenname als String
}
```

### Sensor
```json
{
    "id": 0,         // ID als Integer (wird bei POST überschrieben)
    "name": "",      // Name des Sensors als String
    "userid": 0      // ID des Users als Integer
}
```

### SensorData
```json
{
    "id": 0,         // ID als Integer (wird bei POST überschrieben)
    "data": 0,       // Sensordaten als Integer
    "timestamp": "", // Datum als String im ISO8601-Format (z.B. 2018-01-31 20:00:00)
    "sensorid": 0    // ID des Sensors als Integer
}
```

REST API Endpunkte
------------------
Es können nur Daten des eigenen Benutzers angezeigt oder verändert werden.

#### GET /api/[username]
Gibt den Benutzer im Datenmodell [User](#user) zurück.

#### GET /api/[username]/sensor
Gibt alle Sensoren des Benutzers im Datenmodell [Sensor](#sensor) zurück.

#### GET /api/[username]/sensor/[id]
Gibt einen Sensor des Benutzers anhand seiner ID im Datenmodell [Sensor](#sensor) zurück.

#### POST /api/[username]/sensor
Legt einen neuen Sensor an. Das Datenmodell [Sensor](#sensor) muss übergeben werden. Die Felder *id* und *userid* dürfen `null` sein. Die URL des neu angelegten Sensors befindet sich im HTTP-Header *Location*.

#### PUT /api/[username]/sensor/[id]
Editiert einen bestehenden Sensor. Das Datenmodell [Sensor](#sensor) muss übergeben werden. Das Feld *userid* darf `null` sein.

#### DELETE /api/[username]/sensor/[id]
Löscht einen bestehenden Sensor.

#### GET /api/[username]/sensordata
Gibt alle Sensordaten des Benutzers im Datenmodell [SensorData](#sensordata) zurück.

#### GET /api/[username]/sensordata/[id]
Gibt einen Sensordaten-Datensatz des Benutzers anhand seiner ID im Datenmodell [SensorData](#sensordata) zurück.

#### GET /api/[username]/sensordata/sensor/[id]
Gibt alle Sensordaten eines Sensors des Benutzers im Datenmodell [SensorData](#sensordata) zurück.

#### POST /api/[username]/sensordata
Legt einen neuen Sensordaten-Datensatz an. Das Datenmodell [SensorData](#sensordata) muss übergeben werden. Die Felder *ID* und *timestamp* dürfen `null` sein. Die URL des neu angelegten Sensordaten-Datensatzes befindet sich im HTTP-Header *Location*.

#### DELETE /api/[username]/sensordata/[id]
Löscht einen bestehenden Sensordaten-Datensatz.

HTTP-Statuscodes
----------------

#### 200
Anfrage ok, Daten werden als JSON geliefert

#### 204
Anfrage ok, es folgen keine Daten (z.B bei POST, PUT oder DELETE)

#### 400
Anfrage fehlerhaft, es folgt eine Fehlermeldung

#### 403
Der angegebene Username existiert nicht, es folgt eine Fehlermeldung

#### 404
Datensatz wurde nicht gefunden, oder es wurde versucht einen Datensatz anzulegen, der auf einen nicht existierenden Datensatz verweist (z.B Sensordaten mit nicht existierendem Sensor), es folgt eine Fehlermeldung

### 500
Fehler aufgetreten, es folgt eine Fehlermeldung

---

Inbetriebnahme Entwicklungumgebung
----------------------------------
1. [VisualStudio Code](http://code.visualstudio.com) installieren
    - Workspace in VSCode öffnen
2. Empfohlene Erweiterungen in VSCode installieren: ```> Extensions: Show Workspace Recommend Extensions```
3. Lokaler Apache Webserver mit PHP und MariaDB/MySQL installieren (z.B [MAMP](http://mamp.info) oder [XAMPP](https://www.apachefriends.org))
    - Datenbankserver auf Port 8889 betreiben
    - Standardport für Apache ist egal, da ein virtueller Host definiert wird
4. Lokaler virtueller Host auf Projektverzeichnis konfigurieren
    - In der Apache Konfiguration (*httpd.conf*, bei MAMP für macOS unter */Applications/MAMP/conf/apache/*) eine neuer virtueller Host am Ende des Files hinzufügen:
        ```
        # Virtueller Host REST API Template
        Listen 10081
        <VirtualHost *:10085>
            ServerName sensorapi.local
            DocumentRoot "Path to repository/sensorapi.juergdanuser.ch"
        </VirtualHost>
        <Directory "Path to repository/sensorapi.juergdanuser.ch">
            AllowOverride All
        </Directory>
        ```
    - Es kann jeder beliebige freie Port verwendet werden. Dazu muss nur in der Apache-Konfiguration ein anderer Port verwendet werden.
5. PHP konfigurieren (*php.ini*, bei MAMP für macOS unter */Applications/MAMP/bin/php/php7.1.0/conf*)
    - Error Reporting aktivieren: ```error_reporting  =  E_ALL```
    - Ausgabe von Fehlern aktivieren: ```display_errors = On```
    - XDebug-Erweiterung aktivieren (bei MAMP gefindet sich die Extension bereits in der *php.ini*, jedoch mit einem ```;``` auskommentiert, kontollieren ob der Pfad stimmt)
    - XDebug konfigurieren: In der Section *[xdebug]* folgendes hinzufügen:
        ```
        xdebug.remote_enable = 1
        xdebug.remote_autostart = 1
        ```
6. Datenbank erstellen
    - Neue Datenbank erstellen
    - SQL-Befehle in *dbschema.sql* ausführen


### Debugging
Die Debug-Konfiguration ***Listen vor XDebug*** kann gestartet werden, dann kann mit einem Webbrowser ein Request auf ein Script gemacht werden, und bei einem Breakpoint wird angehalten.


### Veröffentlichen
Die Sensor API wird auf https://sensorapi.juergdanuser.ch betrieben. Falls jemand eine eigene Instanz betreiben möchte, bitte folgende Schritte vornehmen:
1. Datenbank mit dem Schema aus *dbschema.sql* auf dem Server erstellen
2. Settings im Ordner `config` anhand von `example.settings.php` erstellen
3. *deploy.sh* anhand des Beispiels *deploy.example.sh* erstellen
4. Abhängigkeiten mit Composer installieren (VSCode Task *Install dependencies (composer)*)
5. Deploy-Script ausführen (VSCode Task *Deploy*)
