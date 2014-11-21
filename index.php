<?php
define('SQL_PROD_SEL', 'SELECT `ProduktID`, `BauteilID`, `Name`, `Beschreibung`, `Preis`, `KategorieID` FROM `Produkt` ');
define('SQL_BAUT_SEL', 'SELECT b.UnterBauteilID, IFNULL(b.Menge, 1) AS Menge, Name FROM Bauteil
  LEFT JOIN BauteilUnterbauteil b ON b.BauteilID = Bauteil.BauteilID
  LEFT JOIN BauteilUnterbauteil a ON a.BauteilID = b.UnterBauteilID WHERE Bauteil.BauteilID = ?');
define('SQL_PHOT_SEL', 'SELECT PhotoID, URI, Beschreibung FROM Photo WHERE ProduktID = ?');

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

// SQL-Anmeldedaten stehen in der local.php
$sql_credits = include_once'local.php';
$db = new PDO('mysql:dbname=bai3-bwl;host=localhost', $sql_credits['user'], $sql_credits['pass']);
unset($sql_credits); // löschen der SQL-Anmeldedaten.
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
?>
<!DOCTYPE html>
<html>

<head>
    <title>OnlineShop</title>

    <meta charset="ISO-8859-1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <link href="style.css" type="text/css" rel="stylesheet" />

</head>

<body>
<table align="center">
    <tr>
        <td>
            <div id="root_site">
                <div id="head_main_container">
                    <div class="header_background">
                        <table>
                            <tr>
                                <td>OnlineShop <br />BWL2</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="navi_main_container" >
                    <div class="navi_container">
                        <ul id="navigation_main">
                            <li class="startseite"><a href=""> &Uuml;ber uns </a>
                                <ul class="dropdown_navi">
                                    <li><a href="">News</a></li>
                                    <li><a href="">Wetter</a></li>
                                    <li><a href="">Dowland</a></li>
                                </ul>
                            </li>
                            <li class="termine"><a href="">Angebote</a>
                                <ul class="dropdown_navi">
                                    <li><a href="">tour</a></li>
                                    <li><a href="">Wann</a></li>
                                    <li><a href="">Wo</a></li>
                                </ul>
                            </li>

                            <li class="uber"><a href="">Jobs</a>
                                <ul class="dropdown_navi">
                                    <li><a href="">Ausildung</a></li>
                                    <li><a href="">Volzeit</a></li>
                                    <li><a href="">Aushilfe</a></li>
                                </ul>
                            </li>
                        </ul>

                    </div>
                </div>

                <div id="content_main_container" >
                    <div class="content_left_main">
                        <table class="content_left_table" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>

                                    <table class="news_box">
                                        <tr>
                                            <td class="content_table_headline" colspan="2" >Artikel Tabelle
                                                <a href="./">Zurück</a>
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
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">


                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50"> </td>
                                            <td> </td>
                                        </tr>
                                    </table>
                                    <br />
                                    <table class="news_box">
                                        <tr>
                                            <td class="content_table_headline" colspan="2" >News</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>

                                                <p>Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum.</p>

                                                <p>Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,</p>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td ></td>
                                            <td></td>
                                        </tr>
                                    </table>

                                </td>
                            </tr>
                        </table>

                    </div>
                    <div class="content_right_main">
                        <table class="content_right_table" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content_table_headline">Suchen  </td>
                            </tr>
                            <tr>
                                <td>
                                    <form action="index.php" method="post">
                                        <p>Artikel:</p><input type="text" name="artbes" />
                                        <input type="submit" />
                                    </form>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                <div id="footer_main_container" ></div>
                <div id="footer_container">
                    <table class="footer_table_setup" align="center" border="0">
                        <tr>
                            <td></td>
                            <td rowspan="5"><a href="https://www.facebook.com/" target="_blank"><img src="images/logo.gif"/> </a> </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td><a href="">Impressum</a></td>
                            <td><a href="">Kontakt</a></td>
                        </tr>


                        <tr>
                            <td><a href="">Datenschutz</a></td>
                            <td><a href=""> Links</a></td>
                        </tr>
                        <tr>
                            <td><a href="">FAQ</a></td>
                            <td><a href="">Partnerschaft</a></td>
                        </tr>

                    </table>
                </div>
            </div>
        </td>
    </tr>
</table>

</body>
</html>