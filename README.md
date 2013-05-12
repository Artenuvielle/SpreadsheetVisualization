René Martin, Student der Medieninformatik (2. Semester) an der Fakultät Infomatik, Mathematik und Naturwissenschaften der HTWK Leipzig

Dokumentation "Objektverwaltung mit Google Drive", Coding Contest
=============================================


Eine der Aufgaben des 5. Coding-Contest ist es Daten von einem Google Spreadsheet zu visualisieren.
Diese Lösung basiert auf einem Apache-Server, welcher ein Frontend bereitstellt, welches über Ajax-Calls einen Webdienst aufruft.

->Einrichtung
-------------

Wie bereits genannt, wird ein Apache-Server benötigt, auf dem das SSL Modul aktiviert ist.

Zunächst muss die Datei "config/config.php" angepasst werden.


-> Testen
---------

Zum Testen kann https://docs.google.com/spreadsheet/pub?key=0AndFUHBiUH0OdGhoQWRpMW80QzQ5TVhkQXZHMDBDTGc&output=html verwendet werden, jedoch wird die schreibende Funktion nur funktionieren, wenn man einen Account bei Google hat, welche schreibend auf das Spreadsheet zugrifen kann.


-> Selbstständigkeitserklärung
------------------------------

Jeglicher Quellcode (PHP, Javascript und HTML) wurde von mir selbst erstellt, mit den Ausnahmen der Inhalte der folgenden Frameworks:

JQuery

Bootstrap

OpenLayers

Zend GData




René Martin, 12.05.2013

Kontakt: renem1@gmx.net