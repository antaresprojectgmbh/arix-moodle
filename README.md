# Arix/Antares moodle plugin

## Installation
### Dateisystem
Um das Plugin zu installieren, verschieben Sie einfach den gesamten Plugin-Ordner in das Repository Verzeichnis der moodle Installation.

### Webinterface
Alternativ lässt sich das Plugin über das moodle-Webinterface installieren.

Hierzu navigieren Sie einfach nach
> Dashboard -> Website-Administration -> Plugins -> Plugin installieren

und laden die ZIP Datei hoch.


## Konfiguration

Nachdem Sie das Plugin installiert haben, muss dieses zuerst aktiviert werden.

Navigieren Sie hierzu einfach nach
> Dashboard -> Website-Administration -> Plugins -> Repositories -> Übersicht

und aktivieren das Plugin mit dem Namen `arix`.

Danach muss noch eine Instanz des Plugins erstellt werden. Gehen Sie wieder zur Repository Übersicht und öffnen Sie die Einstellungen des arix-Plugins. Klicken Sie nun auf den Button mit der Aufschrift `Repository-Instanz erstellen`.

Es erscheint ein Formular mit den folgenden Einstellungsmöglichkeiten:

 - Name: {z.B. Antares oder Arix} Der anzeige Name des Plugins.
 - Name des Plugins: kann leer gelassen werden.
 - Arix URL: Eine Optionale URL zu einem Arix API-Provider. Wird keine URL angegeben wird **http://arix.datenbank-bildungsmedien.net/** verwendet.
 - Kontext: {z.B. HE/30/030999} [land]/[standortnummer]/[schulnummer]

Füllen Sie alle Felder aus und klicken auf Speichern.
Das Plugin sollte nun einsatzbereit sein.

**Hinweis**: Das Plugin verweist auf Externe Inhalte. Es ist daher nötig Inhalte als Link/URL hinzuzufügen.
