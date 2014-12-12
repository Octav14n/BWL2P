<?php
require_once 'inc/init.php';
require_once 'inc/Vorhersage.php';
require_once 'inc/Assoziation.php';
Vorhersage::init($db);
Assoziation::init($db);

// Maximalen Zeitraum bestimmen der fuer den Vorhersagen bestehen.
$maxZeitraum = Vorhersage::getMaxZeitraum();
if (!isset($_GET['zeitraum']) || !is_numeric($_GET['zeitraum']) || $_GET['zeitraum'] < 1 || $_GET['zeitraum'] > $maxZeitraum) {
    // Wenn kein (oder ein falscher) Zeitraum gewaehlt ist, wird der 1. angezeigt.
    $vorhersage = new Vorhersage(1);
} else {
    $vorhersage = new Vorhersage($_GET['zeitraum']);
}

if (isset($_POST['expGlat'])) {
    // Es soll ein neuer Zeitraum eingefuegt werden.
    if ($vorhersage->einfuegen()) {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri/admin.php?msg=Eintragen Erfolgreich.");
    }
    exit;
}

echo $twig->render('admin.twig', array(
    'msg' => @$_GET['msg'],
    'zeitraum' => $vorhersage->getZeitraum(),
    'vorhersage' => $vorhersage->getBauteils(),
    'maxZeitraum' => $maxZeitraum,
    'assos' => Assoziation::getInstance()->getAssos()
));