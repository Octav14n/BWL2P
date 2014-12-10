<?php
$params = array();
require_once 'inc/init.php';

// Parameter fuer Twig (Layout)
$params = array_merge($params, array(
    'produkte' => Produkt::getInstance()->getProdukte(),
    'user' => User::getInstance()->getUser(),
    'warenkorb' => Warenkorb::getMainInstance()
));
// Msg ist eine Text-Zeile die als parameter uebergeben wird und im HTML ausgegeben wird.
if (isset($_GET['msg'])) {
    $params['msg'] = $_GET['msg'];
}

// Ein Produkt in den Warenkorb legen.
if (isset($_POST['artIDInWarenkorb']) && is_numeric($_POST['artIDInWarenkorb'])) {
    $wk = Warenkorb::getMainInstance();
    $wk->addProduct($_POST['artIDInWarenkorb'], 1);
}

if (isset($_GET['warenkorb'])) {
    // Wir wollen uns den Warenkorb angucken.
    unset($params['produkte']);
} elseif (isset($_GET['buy'])) {
    // Wir wollen kaufen.
    if (isset($_POST['iban'])) {
        // Der Nutzer hat die IBan bereits eingegeben. Kauf durchfuehren.
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri/?msg=Bestellung Erfolgreich.");

        Warenkorb::getMainInstance()->purchase($_POST['iban']);
    }

    echo $twig->render('buy.twig', $params);
    exit;
}

echo $twig->render('main.snip.twig', $params);
?>