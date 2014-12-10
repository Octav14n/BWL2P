<?php
// Session Initialisieren (nutzt Cookies als Standard-Methode.)
session_id('bwl-localhost');
session_start();

// SQL-Anmeldedaten stehen in der local.php
$sql_credits = include_once'local.php';
$db = new PDO('mysql:dbname=bai3-bwl;host=localhost', $sql_credits['user'], $sql_credits['pass']);
unset($sql_credits); // löschen der SQL-Anmeldedaten.

require_once 'inc/Login.php';
require_once 'inc/Warenkorb.php';
require_once 'inc/Produkt.php';

// Initialiseren des Users.
User::init($db);
Warenkorb::init($db);
Produkt::init($db);


require_once 'vendor/autoload.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('./inc');
$twig = new Twig_Environment($loader, array(
    'debug' => true
));