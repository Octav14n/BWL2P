<?php
define('SQL_PROD_SEL', 'SELECT `ProduktID`, `BauteilID`, `Name`, `Beschreibung`, `Preis`, `KategorieID` FROM `Produkt` ');
define('SQL_BAUT_SEL', 'SELECT b.UnterBauteilID, IFNULL(b.Menge, 1) AS Menge, Name FROM Bauteil
  LEFT JOIN BauteilUnterbauteil b ON b.BauteilID = Bauteil.BauteilID
  LEFT JOIN BauteilUnterbauteil a ON a.BauteilID = b.UnterBauteilID WHERE Bauteil.BauteilID = ?');
define('SQL_PHOT_SEL', 'SELECT PhotoID, URI, Beschreibung FROM Photo WHERE ProduktID = ?');

require_once 'inc/Login.php';

/**
 * Funktion um Bauteile in einer Tabelle rekursiev darzustellen.
 * @param PDOStatement $query_bauteile SQL-Statement das die Unterbauteile enthält.
 * @param int $mengeMulti Multiplikator der auf die Mengen angewandt wird.
 * @param int $depth Tiefe der Verschachtelung des aktuellen Bauteils.
 */
function showBauteile(PDOStatement $query_bauteile, $mengeMulti = 1, $depth = 0) {
    while ($query_bauteile->rowCount() > 0) {
        $bauteile = $query_bauteile->fetchAll(PDO::FETCH_CLASS);
        $bauteil = $bauteile[0];
            ?>
            <tr>
                <td style="text-align: right;"><?= $mengeMulti  ?>x</td>
                <td><?= str_repeat('+', $depth) . ' ' . $bauteil->Name ?></td>
            </tr>
        <?php
        foreach ($bauteile as $bauteil) {
            $query_bauteile->execute(array ($bauteil->UnterBauteilID));
            showBauteile($query_bauteile, $bauteil->Menge * $mengeMulti, $depth+1);
        }/*endforeach*/
    }/*endwhile*/
}

function obHandler($buffer) {
    $inhalt = file_get_contents('inc/main.snip.htm');
    $inhalt = str_replace('__INHALT__', $buffer, $inhalt);
    $inhalt = str_replace('__USER__', User::getInstance(), $inhalt);
    return $inhalt;
}

// SQL-Anmeldedaten stehen in der local.php
$sql_credits = include_once'local.php';
$db = new PDO('mysql:dbname=bai3-bwl;host=localhost', $sql_credits['user'], $sql_credits['pass']);
unset($sql_credits); // löschen der SQL-Anmeldedaten.

// Initialiseren des Users.
User::init($db);

if (!empty($_POST['artbes'])) {
    // Suchen nach einem Produkt
    $query_produkte = $db->prepare(SQL_PROD_SEL . 'WHERE Beschreibung LIKE :beschreibung');
    $query_produkte->bindValue('beschreibung', "%$_POST[artbes]%");
    echo 'Suche nach "' . htmlspecialchars($_POST['artbes']) . '"';
} elseif(!empty($_GET['artikel'])) {
    // Anzeigen der Details eines Produktes
    $query_produkte = $db->prepare(SQL_PROD_SEL . 'WHERE ProduktID LIKE :prodid');
    $query_produkte->bindValue('prodid', $_GET['artikel']);
    // Aufbauen eines SQL-Statements um Bauteile abzurufen.
    $query_bauteile = $db->prepare(SQL_BAUT_SEL);
} else {
    // Anzeigen aller Produkte
    $query_produkte = $db->prepare(SQL_PROD_SEL);
}

$query_produkte->execute();
$query_photo = $db->prepare(SQL_PHOT_SEL);
$produkte = $query_produkte->fetchAll(PDO::FETCH_CLASS);
ob_start('obHandler');
?>
<table class="products">
<thead>
<tr>
    <td>&nbsp;</td>
    <td>Produkt</td>
    <td>Beschreibung</td>
    <td>Preis</td>
</tr>
</thead>
<tbody><?php foreach ($produkte as $produkt) { ?>
    <tr>
        <td>
            <?php
            $query_photo->execute(array($produkt->ProduktID));
            $photos = $query_photo->fetchAll(PDO::FETCH_CLASS);
            foreach ($photos as $photo) {
                ?>
                <img src="<?php echo $photo->URI; ?>" title="<?php echo htmlentities($photo->Beschreibung); ?>" class="preview" />
            <?php } ?>
        </td>
        <td><a href="./?artikel=<?php echo $produkt->ProduktID ?>"><?php echo $produkt->Name; ?></a></td>
        <td><?php
            echo $produkt->Beschreibung;
            if (isset($query_bauteile)) {?>
                <table>
                    <thead>
                    <tr>
                        <td>Menge</td>
                        <td>Name</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $query_bauteile->execute(array($produkt->BauteilID));
                    showBauteile($query_bauteile);
                    ?>
                    </tbody>
                </table>
            <?php }/*endif*/ ?></td>
        <td><?php echo $produkt->Preis; ?>€</td>
    </tr>
<?php } ?></tbody>
</table>
<?php ob_end_flush(); ?>