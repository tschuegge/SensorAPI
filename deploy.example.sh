#!/bin/bash

# Allfälligen macOS Finder Abfall löschen (nur macOS)
find ./ -name '.DS_Store' -type f -delete

# API auf dem Remote Server löschen
ssh username@host.com "rm -rf ./deploypath/*"

# Folgende Ordner und Unterordner inkl. Dateien hochladen: api, classes, dbadmin, vendor
scp -r ./api ./classes ./dbadmin ./vendor username@host.com:/deploypath/

# Folgende Dateien hochladen: classloader.inc.php, index.php
scp ./classloader.inc.php ./index.php username@host.com:/deploypath/

# Config Ordner anlegen und Config übertragen
ssh username@host.com "mkdir ./deploypath/config"
scp ./config/example.settings.php ./index.php username@host.com:/deploypath/config/