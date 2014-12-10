<?php
/**
 * Created by PhpStorm.
 * User: octavian
 * Date: 21.11.14
 * Time: 10:32
 */
define('SQL_WARENKORB_SELECT', 'SELECT WarenkorbID, ProduktID, Menge FROM Warenkorb w WHERE Bestelldatum IS NULL AND w.KundenID = ?');
define('SQL_WARENKORB_INSERT', 'INSERT INTO Warenkorb (KundenID, ProduktID, Menge) VALUE (?, ?, ?)');
define('SQL_WARENKORB_UPDATE', 'UPDATE Warenkorb SET Menge = ? WHERE WarenkorbID = ?');
define('SQL_RECHNUNG_INSERT', 'INSERT INTO Rechnung (KundeID, IBAN) VALUES (?, ?)');
define('SQL_RECHNUNGPOS_INSERT', 'INSERT INTO RechnungWarenkorb (RechnungID, WarenkorbID) VALUES (?, ?)');
define('SQL_WARENKORB_PURCHASE', 'UPDATE Warenkorb SET Bestelldatum = NOW() WHERE WarenkorbID = ?');

class Warenkorb {
    private static $instance;
    /** @var PDO */
    private static $db;
    /** @var PDOStatement */
    private static $update_query;
    /** @var PDOStatement */
    private static $insert_query;
    private $warenkorb = array();
    private $changeAble;
    private $kundeID;

    public function __construct($id) {
        if ($id === null && User::getInstance()->isLogin()) {
            $this->selectWarenkorb(User::getInstance()->getKundeID());
            $this->changeAble = true;
        } else {
            $this->selectWarenkorb($id);
            $this->changeAble = false;
        }
    }

    public static function init(PDO $db) {
        self::$db = $db;
        self::$insert_query = $db->prepare(SQL_WARENKORB_INSERT);
        self::$update_query = $db->prepare(SQL_WARENKORB_UPDATE);
    }

    /**
     * Gibt den Warenkorb des angemeldeten Benutzers zurueck,
     * @return Warenkorb
     */
    public static function getMainInstance() {
        if (!isset(static::$instance))
            static::$instance = new Warenkorb(null);
        return static::$instance;
    }

    public function addProduct($produktID, $menge) {
        assert(is_numeric($produktID) && is_numeric($menge) && $this->changeAble);
        if ($ware = $this->getWareByProduktID($produktID)) {
            $ware->Menge += $menge;
            static::$update_query->execute(array($ware->Menge, $ware->WarenkorbID));
        } elseif ($this::$insert_query->execute(array($this->kundeID, $produktID, $menge))) {
            $obj = new stdClass();
            $obj->ProduktID = $produktID;
            $obj->Menge = $menge;
            $obj->WarenkorbID = $this::$db->lastInsertId();
            $this->warenkorb[] = $obj;
        }
    }

    public function getWarenkorbArtikel() {
        $p = Produkt::getInstance();
        foreach ($this->warenkorb as $ware) {
            // Waren einen Namen geben.
            $ware->ProduktName = $p->getProduktByID($ware->ProduktID)->Name;
        }
        return $this->warenkorb;
    }

    /**
     * Setzt den Warenkorbstatus auf "bestellt". Erstellt eine Rechnung.
     * @param $iban IBan die belastet werden soll.
     */
    public function purchase($iban) {
        $rechnung_query = self::$db->prepare(SQL_RECHNUNG_INSERT);
        if ($rechnung_query->execute(array($this->kundeID, $iban))) {
            // Rechnung wurde erstellt.
            $rechnung_id = self::$db->lastInsertId();
            $rechnung_pos_query = self::$db->prepare(SQL_RECHNUNGPOS_INSERT);
            $warenkorb_query = self::$db->prepare(SQL_WARENKORB_PURCHASE);
            $p = Produkt::getInstance();
            foreach ($this->warenkorb as $ware) {
                // Alle Warenkorb-Items (waren) auf die Rechnung setzten.
                $rechnung_pos_query->execute(array($rechnung_id, $ware->WarenkorbID));
                $warenkorb_query->execute(array($ware->WarenkorbID));
                $produkt = $p->getProduktByID($ware->ProduktID);
                $p->purchaseBauteil($produkt->BauteilID, $ware->Menge);
            }
        }
    }

    /**
     * Sucht ein (geladenes) Warenkorb-Objekt nach der uebergebenen ProduktID aus.
     * @param $produktID int Die ProduktID nach der ein Warenkorb-Objekt gesucht werden soll.
     */
    private function getWareByProduktID($produktID) {
        foreach ($this->warenkorb as $ware) {
            if ($ware->ProduktID == $produktID) {
                return $ware;
            }
        }
        return null;
    }

    public function getCount() {
        return count($this->warenkorb);
    }

    private function selectWarenkorb($id) {
        $query = Warenkorb::$db->prepare(SQL_WARENKORB_SELECT);
        $this->kundeID = $id;
        $query->execute(array($id));
        $this->warenkorb = $query->fetchAll(PDO::FETCH_CLASS);
    }
} 