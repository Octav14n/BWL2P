<?php
$params = array();
require_once 'inc/init.php';

$params = array_merge($params, array(
    'produkte' => Produkt::getInstance()->getProdukte(),
    'user' => User::getInstance()->getUser(),
    'warenkorb' => Warenkorb::getMainInstance()
));
if (isset($_GET['msg'])) {
    $params['msg'] = $_GET['msg'];
}

if (isset($_POST['artIDInWarenkorb']) && is_numeric($_POST['artIDInWarenkorb'])) {
    $wk = Warenkorb::getMainInstance();
    $wk->addProduct($_POST['artIDInWarenkorb'], 1);
}

if (isset($_GET['warenkorb'])) {
    unset($params['produkte']);
} elseif (isset($_GET['buy'])) {
    if (isset($_POST['iban'])) {
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