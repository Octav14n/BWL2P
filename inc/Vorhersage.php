<?php
define('SQL_VORHERSAGE_SELECT', 'SELECT b.BauteilID, IFNULL(vJetzt.Ist, vVor.Ist - b.AufLager) AS Verbrauch, vJetzt.Soll, AVG(vM.Ist) AS Avg FROM Bauteil b
  LEFT JOIN Vorhersage vVor ON vVor.BauteilID = b.BauteilID AND vVor.Zeitraum = ?
  LEFT JOIN Vorhersage vJetzt ON b.BauteilID = vJetzt.BauteilID AND vJetzt.Zeitraum = vVor.Zeitraum + 1
  LEFT JOIN Vorhersage vM ON vM.BauteilID = b.BauteilID
  GROUP BY vM.BauteilID, vVor.Ist, b.AufLager, vJetzt.Soll, vVor.Zeitraum, vJetzt.Zeitraum');
define('SQL_VORHERSAGE_MAX', 'SELECT MAX(Zeitraum) FROM Vorhersage');
define('SQL_VORHERSAGE_INSERT', 'INSERT INTO Vorhersage (BauteilID, Soll, Ist, Zeitraum) VALUES (?, ?, ?, ?)');
define('SQL_VORHERSAGE_BUPDATE', 'UPDATE Bauteil SET AufLager = ? WHERE BauteilID = ?');
define('ALPHA', .2);

class Vorhersage {
    /** @var PDO */
    private static $db;
    /** @var PDOStatement */
    private static $query_sel;
    private $zeitraum;
    private $bauteils;

    public function __construct($zeitraum) {
        assert(is_numeric($zeitraum));
        $this->zeitraum = $zeitraum;
        static::$query_sel->execute(array($zeitraum));
        $this->bauteils = static::$query_sel->fetchAll(PDO::FETCH_CLASS);
        foreach ($this->bauteils as $bauteil) {
            $bauteil->NextSoll = $bauteil->Soll + ALPHA*($bauteil->Verbrauch - $bauteil->Soll);
        }

    }

    public function getZeitraum() {
        return $this->zeitraum;
    }

    public function getBauteils() {
        return $this->bauteils;
    }

    public function einfuegen() {
        if ($this->zeitraum >= static::getMaxZeitraum()) {
            $query_vorhersage = static::$db->prepare(SQL_VORHERSAGE_INSERT);
            $query_bauteil = static::$db->prepare(SQL_VORHERSAGE_BUPDATE);
            foreach ($this->bauteils as $bauteil) {
                // Soll eintragen
                $query_vorhersage->execute(array($bauteil->BauteilID, $bauteil->NextSoll, $bauteil->AufLager, ($this->zeitraum + 1)));
                // Lager befuellen.
                $query_bauteil->execute(array($bauteil->NextSoll, $bauteil->BauteilID));
            }
            return true;
        } else {
            echo 'Einträge exisisteren. ' . $this->zeitraum;
            return false;
        }
    }

    /** @param $db PDO */
    static public function init($db) {
        static::$db = $db;
        static::$query_sel = $db->prepare(SQL_VORHERSAGE_SELECT);
    }

    static public function getMaxZeitraum() {
        $q = static::$db->query(SQL_VORHERSAGE_MAX);
        return $q->fetch(PDO::FETCH_COLUMN)[0];
    }
} 