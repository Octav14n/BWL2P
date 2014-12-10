<?php
require_once 'inc/init.php';
require_once 'inc/Vorhersage.php';
Vorhersage::init($db);

$maxZeitraum = Vorhersage::getMaxZeitraum();
if (!isset($_GET['zeitraum']) || !is_numeric($_GET['zeitraum']) || $_GET['zeitraum'] < 1 || $_GET['zeitraum'] > $maxZeitraum) {
    $vorhersage = new Vorhersage(1);
} else {
    $vorhersage = new Vorhersage($_GET['zeitraum']);
}

if (isset($_POST['expGlat'])) {
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
    'maxZeitraum' => $maxZeitraum
));