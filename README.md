René Martin, Student der Medieninformatik (2. Semester) an der Fakultät Infomatik, Mathematik und Naturwissenschaften der HTWK Leipzig

Dokumentation "Objektverwaltung mit Google Drive", Coding Contest
=================================================================


Eine der Aufgaben des 5. Coding-Contest ist es Daten von einem Google Spreadsheet zu visualisieren.
Diese Lösung basiert auf einem Apache-Server, welcher ein Frontend bereitstellt, welches über Ajax-Calls einen Webdienst aufruft.

->Einrichtung
-------------

Wie bereits genannt, wird ein Apache-Server benötigt, auf dem das SSL Modul aktiviert ist.

Zunächst muss die Datei "config/config.php" angepasst werden. In dieser werden globale Variablen festgelegt, welche den Webdienst beeinflussen.

Anschließend kann die index.php aus dem Standard Verzeichnis aufgerufen werden. Es müssten nun die Wohnungen per Ajax vom Webdienst abgefragt und anschließend visualisiert werden.


-> Testen
---------

Zum Testen kann https://docs.google.com/spreadsheet/pub?key=0AndFUHBiUH0OdGhoQWRpMW80QzQ5TVhkQXZHMDBDTGc&output=html verwendet werden, jedoch wird die schreibende Funktion nur funktionieren, wenn man einen Account bei Google hat, welche schreibend auf das Spreadsheet zugrifen kann.

-> Webdienst
------------

Der Webdienst besteht aus 2 Schnittstellen, welche mit dem Google Spreadsheet arbeiten, die sich im Unterverzeichnis /webservice/ befinden. Im folgenden sind die möglichen Abfragen an die Dateien index.php und book.php aus diesem Verzeichnis erklärt. Beide liefern die Ergebnisse immer im JSON Format.

### index.php (nur get-requests)

Ließt Daten aus einem Google Spreadsheet und gibt diese formatiert zurück.

Ausgabe:

{

  obj[] {

    worksheetname > {

      name > Name des Objekts

      adress > Straße, Postleitzahl und Stadt des Objekts (wird nur gesendet, falls Details angefordert werden)

      position > Koordinaten des Objekts für die Karte

      fitsfilter > gibt an, ob das Objekt dem gesetzten Filter entspricht (richtige Stadt/PLZ und enthält freie Tage)

      dates > Array von Tagen im angeforderten Bereich und ihr Belegungsstatus (wird nur gesendet, falls Details angefordert werden)

    }

  }

  cardzoom > Array mit Begrenzungen, welche die Karte anzeigen soll

}

Parameter:

* dow=String  // gibt an, welche worksheets betrachtet werden sollen; mögliche Eingaben:

* * String=""  // Alle worksheets werden gelesen

* * String="od6". // Nur das worksheet mit dem Namen "od6" wird gelesen

* * String="od6+od7"  // Nur die worksheets mit den Namen "od6" und "od7" werden gelesen

* city=String  // Setzt Filter für Stadt/PLZ; alle worksheets die diesem nicht entsprechen werden mit fitsfilter="0" geliefert

* date=Datum  // Setzt Datum, ab welchem der Belegungsstatus geprüft werden soll; Standard: heutiges Datum; Falls nicht gesetzt wird der Filter nicht auf das Datum angewendet

* todate=Datum  // Setzt Datum, bis zu dem der Belegungsstatus geprüft werden soll; Standard: Wert von date

* detail=String  // Falls gesetzt, werden Adresse und Belegungsdaten mit gesendet

### book.php (nur Post-requests über https)

Verändert Wert einer Spreadsheetzelle, in deren Reihe ein gewisses Datum gefunden wurde.

Ausgabe:

{

  result > "success" bei Erfolg, ansonsten Fehlercode (Erklärung siehe Quelltext)

}

Parameter:

* dow=String  // gibt an, welches worksheets bearbeitet werden sollen; nur ein Name erlaubt

* date=Datum  // Datum, an welchem der Nutzer das Objekt Buchen will

* name=String  // Name des Nutzers; wird in Spreadsheet eingetragen

* usr=String  // Nutzername eines Google Accounts, der Zugriff auf das Spreadsheet hat; falls nicht gesetzt wird globaler Account verwendet

* pwd=String  // Passwort eines Google Accounts, der Zugriff auf das Spreadsheet hat; falls nicht gesetzt wird globaler Account verwendet 

-> Selbstständigkeitserklärung
------------------------------

Jeglicher Quellcode (PHP, Javascript und HTML) wurde von mir selbst erstellt, mit den Ausnahmen der Inhalte der folgenden Frameworks:

JQuery

Bootstrap

OpenLayers

Zend GData




René Martin, 12.05.2013

Kontakt: renem1@gmx.net
