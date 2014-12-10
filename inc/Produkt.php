<?php
define('SQL_PROD_SEL', 'SELECT `ProduktID`, `BauteilID`, `Name`, `Beschreibung`, `Preis`, `KategorieID` FROM `Produkt` ');
define('SQL_PROD_SEL_ID', 'SELECT `ProduktID`, `BauteilID`, `Name`, `Beschreibung`, `Preis`, `KategorieID` FROM Produkt WHERE ProduktID = ?');
define('SQL_BAUT_SEL', 'SELECT b.UnterBauteilID, IFNULL(b.Menge, 1) AS Menge, Name, AufLager FROM Bauteil
  LEFT JOIN BauteilUnterbauteil b ON b.BauteilID = Bauteil.BauteilID
  LEFT JOIN BauteilUnterbauteil a ON a.BauteilID = b.UnterBauteilID WHERE Bauteil.BauteilID = ?');
define('SQL_PHOT_SEL', 'SELECT PhotoID, URI, Beschreibung FROM Photo WHERE ProduktID = ?');
define('SQL_BAUT_PURCHASE', 'UPDATE Bauteil SET AufLager = AufLager - ? WHERE BauteilID = ?');

class Produkt {
    /** @var $instance Produkt */
    private static $instance;
    /** @var $query_bauteile PDOStatement */
    private static $query_bauteile;
    /** @var $query_bauteile_purchase PDOStatement */
    private static $query_bauteile_purchase;
    /** @var $db PDO */
    private static $db;
    private $produkte;

    private function __construct() {
        $this->selectProdukte();
    }

    public static function init(PDO $db) {
        Produkt::$db = $db;
        static::$query_bauteile = $db->prepare(SQL_BAUT_SEL);
        static::$query_bauteile_purchase = $db->prepare(SQL_BAUT_PURCHASE);
    }

    /**
     * @return Produkt
     */
    public static function getInstance() {
        if (static::$instance == null) {
            assert(static::$db != null, __CLASS__ . ' wurde benutzt bevor es initialisiert wurde.');
            self::$instance = new Produkt();
        }
        return static::$instance;
    }

    /**
     * Reduziert das angegebene Bauteil um die angegebene Menge.
     * @param $bauteilID ID Des gekauften bauteils.
     * @param $menge Menge des Bauteils.
     */
    public function purchaseBauteil($bauteilID, $menge) {
        if (static::$query_bauteile->execute(array($bauteilID))) {
            $bauteils = static::$query_bauteile->fetchAll(PDO::FETCH_CLASS);
            $AufLager = $bauteils[0]->AufLager;

            if ($AufLager > 0 || is_null($bauteils[0]->UnterBauteilID)) {
                // Einige(/Alle) dieses Bauteils sind auf Lager ODER es ist ein Basis-Bauteil.
                $delta_menge = $menge;
                if (!is_null($bauteils[0]->UnterBauteilID))
                    $delta_menge = min($menge, $AufLager);
                $menge -= $delta_menge;
                static::$query_bauteile_purchase->execute(array($delta_menge, $bauteilID));
            }

            if ($menge > 0) {
                // Wir konnten noch nicht alle Bauteile beschaffen.
                foreach ($bauteils as $bauteil) {
                    $this->purchaseBauteil($bauteil->UnterBauteilID, $menge * $bauteil->Menge);
                }
            }
        }
    }

    public function getProdukte() {
        return $this->produkte;
    }

    /**
     * Gibt ein Produkt(-Objekt) anhand dessen ID zurueck.
     * @param $id ProduktID
     */
    public function getProduktByID($id) {
        $query = static::$db->prepare(SQL_PROD_SEL_ID);
        $query->execute(array($id));
        return $query->fetchObject();
    }

    /**
     * Gibt ein Bauteil(-Objekt) anhand dessen ID zurueck.
     * @param $id BauteilID
     */
    public function getBauteilByID($id) {
        static::$query_bauteile->execute(array($id));
        return static::$query_bauteile->fetchObject();
    }

    /** Holt alle Produkte aus der DB und gibt diese zurueck. */
    private function selectProdukte() {
        $query_bauteile = null;
        $query_produkte = null;
        if (!empty($_POST['artbes'])) {
            // Suchen nach einem Produkt
            $query_produkte = static::$db->prepare(SQL_PROD_SEL . 'WHERE Beschreibung LIKE :beschreibung');
            $query_produkte->bindValue('beschreibung', "%$_POST[artbes]%");
            echo 'Suche nach "' . htmlspecialchars($_POST['artbes']) . '"';
        } elseif(!empty($_GET['artikel'])) {
            // Anzeigen der Details eines Produktes
            $query_produkte = static::$db->prepare(SQL_PROD_SEL . 'WHERE ProduktID LIKE :prodid');
            $query_produkte->bindValue('prodid', $_GET['artikel']);
            // Aufbauen eines SQL-Statements um Bauteile abzurufen.
            $query_bauteile = static::$db->prepare(SQL_BAUT_SEL);
        } else {
            // Anzeigen aller Produkte
            $query_produkte = static::$db->prepare(SQL_PROD_SEL);
        }

        $query_produkte->execute();
        $query_photo = static::$db->prepare(SQL_PHOT_SEL);
        $this->produkte = $query_produkte->fetchAll(PDO::FETCH_CLASS);
        foreach ($this->produkte as $produkt) {
            $query_photo->execute(array($produkt->ProduktID));
            $produkt->Photos = $query_photo->fetchAll(PDO::FETCH_CLASS);
            if (isset($query_bauteile)) {
                $query_bauteile->execute(array($produkt->BauteilID));
                $produkt->Bauteil = $this->showBauteile($query_bauteile);
            }
        }
    }

    /**
     * Funktion um Bauteile in einer Tabelle rekursiev darzustellen.
     * @param PDOStatement $query_bauteile SQL-Statement das die Unterbauteile enthält.
     * @param int $mengeMulti Multiplikator der auf die Mengen angewandt wird.
     * @param int $depth Tiefe der Verschachtelung des aktuellen Bauteils.
     */
    private function showBauteile(PDOStatement $query_bauteile, $mengeMulti = 1, $depth = 0) {
        $str = '';
        while ($query_bauteile->rowCount() > 0) {
            $bauteile = $query_bauteile->fetchAll(PDO::FETCH_CLASS);
            $bauteil = $bauteile[0];
            $str .= '<tr>
                <td style="text-align: right;">' . $mengeMulti . 'x</td>
                <td>' . str_repeat('+', $depth) . ' ' . $bauteil->Name . '</td>
            </tr>';
            foreach ($bauteile as $bauteil) {
                $query_bauteile->execute(array ($bauteil->UnterBauteilID));
                $str .= $this->showBauteile($query_bauteile, $bauteil->Menge * $mengeMulti, $depth+1);
            }/*endforeach*/
        }/*endwhile*/
        return $str;
    }
} 