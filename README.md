BWL2P
=====

BWL2 Praktikum

- - - 

Installation
----
1. Einen MySQL nutzer "bai3-bwl" und eine Datenbank "bai3-bwl" anlegen.
2. doc/bai3-bwl.sql in der Datenbank bai3-bwl ausführen (per PhpMyAdmin).
3. local.php anlegen mit dem Inhalt:<br />
```php
    <?php
    return array(
        'user' => 'bai3-bwl',
        'pass' => '<passwort von bai3-bwl>',
    );
    ?>
```

TWIG
----
Dieses Projekt benutzt (und beinhaltet) TWIG.
TWIG ist eine Template-Engine [Dokumentation](http://twig.sensiolabs.org/documentation).

Aufbau
----
* <b>index.php</b> kümmert sich um die Darstellung des Shops.
* <b>admin.php</b> kümmert sich um die Darstellung des Administrations-Bereichs.<br />
  Diese PHP-Datei sollte auf einem Server liegen der nur über das Intranet erreichbar ist,<br />
  da hier keine Authentifikation erforderlich ist.
* <b>inc/</b> (Ordner)<br />
  inc beinhaltet die meiste Logik. Logik ist gekapselt in Klassen die als Komponenten (siehe SE1 vorlesung) fungieren.
    * <b>init.php</b> Initialisiert die DB, alle Komponenten und TWIG.
    * <b>Login.php</b> ist fuer die Nutzerverwaltung, insbesondere die Authentifikation des Besuchers, verantwortlich.
    * <b>Produkt.php</b> ist fuer die Produktverwaltung und Bauteilverwaltung verantwortlich.
    * <b>Warenkorb.php</b> ist fuer die Warenkorbsverwaltung und die Bestellung (inklusieve Rechnung) verantwortlich.
    * <b>Vorhersage.php</b> ist fuer administrative Aufgaben verantwortlich. Insbesondere die Primärbedarfsanalyse.
    * <b>Assoziation.php</b> ist fuer die Assoziationsanalyse verantwortlich.