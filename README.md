# Arix/Antares moodle plugin

## Installation

Laden Sie unter https://github.com/antaresprojectgmbh/arix-moodle/releases das neueste Release herunter und entpacken Sie es in einen beliebigen Ordner.
Anschließend navigieren Sie mit ihrem Terminal in diesen Ordner, um die folgenden Schritte auszuführen.

### Erstellen der zip Datei

Bevor Sie das Plugin installieren können, müssen Sie ihren API-Token in die Datei arix.php eintragen.
Um diesen Vorgang zu vereinfachen, haben wir ein kleines Script erstellt.

Führen Sie einfach den folgenden Befehl aus:
```
./build.sh -t <ihr API-token>
```

Sie sollten jetzt eine Datei **arix-moodle-.zip** im aktuellem Verzeichnis finden.
Diese Datei kann zur Installation verwendet werden.

### Webinterface
Das Plugin lässt sich über das moodle-Webinterface installieren.

Hierzu navigieren Sie einfach nach
> Dashboard -> Website-Administration -> Plugins -> Plugin installieren

und laden die ZIP Datei hoch.


## Konfiguration

Nachdem Sie das Plugin installiert haben, muss dieses zuerst aktiviert werden.

Navigieren Sie hierzu nach
> Dashboard -> Website-Administration -> Plugins -> Repositories -> Übersicht

und aktivieren das Plugin mit dem Namen `arix`.

Danach muss noch eine Instanz des Plugins erstellt werden. Gehen Sie wieder zur Repository Übersicht und öffnen Sie die Einstellungen des arix-Plugins.
Klicken Sie nun auf den Button mit der Aufschrift `Repository-Instanz erstellen`.

Es erscheint ein Formular mit den folgenden Einstellungsmöglichkeiten:

 - Name: {z.B. Antares oder Arix} Der anzeige Name des Plugins.
 - Name des Plugins: kann leer gelassen werden.
 - Arix URL: Eine Optionale URL zu einem Arix API-Provider.
   
   Wird keine URL angegeben wird **http://arix.datenbank-bildungsmedien.net/** verwendet.
 - Kontext: {z.B. HE/30/030999} [land]/[standortnummer]/[schulnummer]
   
   Der Kontext kann auch verkürzt werden:
   NRW ist z.B. der allgemeine Kontext Nordrhein-Westfalen.
   NRW/VIE ist der allgemeine Kontext für den Standort Viersen in NRW.
   Es werden bei einer Suchanfrage nur die Medien ausgegeben, die auch für diesen Kontext zur Verfügung stehen. Im ersten Fall also nur Landeslizenzen, im zweiten Landes- sowie Kreislizenzen
   Der kürzeste funktionierende Kontext besteht aus mindestens dem Land.


Füllen Sie alle Felder aus und klicken auf Speichern.
Das Plugin sollte nun einsatzbereit sein.

**Hinweis**: Das Plugin verweist auf Externe Inhalte. Es ist daher nötig Inhalte als Link/URL hinzuzufügen.
