<?php
define('SQL_ASSO_SELECT', 'SELECT
  w1.ProduktID AS ID1,
  b1.Name AS Name1,
  w2.ProduktID AS ID2,
  b2.Name AS Name2,
  (COUNT(*) / (SELECT COUNT(*) FROM Warenkorb w3 WHERE w3.Bestelldatum IS NOT NULL AND w3.ProduktID = w1.ProduktID)) AS confidence FROM Warenkorb w1
  JOIN Warenkorb w2 ON
                      w1.Bestelldatum = w2.Bestelldatum AND
                      w1.KundenID = w2.KundenID AND
                      w1.ProduktID != w2.ProduktID
  JOIN Produkt b1 ON w1.ProduktID = b1.ProduktID
  JOIN Produkt b2 ON w2.ProduktID = b2.ProduktID
GROUP BY w1.ProduktID, b1.Name, w2.ProduktID, b2.Name
ORDER BY confidence DESC');

class Assoziation {
    /** @var $instance User */
    private static $instance;
    /** @var $db PDO */
    private static $db;

    private $assos;

    private function __construct() {
        $query = static::$db->prepare(SQL_ASSO_SELECT);
        $query->execute();
        $this->assos = $query->fetchAll(PDO::FETCH_CLASS);
    }

    public function getAssos() {
        return $this->assos;
    }

    public static function init(PDO $db) {
        static::$db = $db;
    }

    /**
     * @return Assoziation
     */
    public static function getInstance() {
        if (static::$instance == null) {
            assert(static::$db != null, __CLASS__ . ' wurde benutzt bevor es initialisiert wurde.');
            static::$instance = new Assoziation();
        }
        return static::$instance;
    }
}