<?php
/**
 * Created by PhpStorm.
 * User: octavian
 * Date: 21.11.14
 * Time: 10:36
 */
define('SQL_USER_SEL', )

class User {
    /** @var $instance User */
    private static $instance;
    /** @var $db PDO */
    private static $db;
    private $query_user;

    private function __construct() {
        assert(User::$instance == null, 'User ist ein singelton. Es wurde versucht eine zweite Instanz zu bilden.');
        if (isset($_POST['userName'])) {
            $this->query_user = User::$db->prepare()
        }
    }

    public static function init(PDO $db) {
        User::$db = $db;
    }

    /**
     * @return User
     */
    public static function getInstance() {
        if (User::$instance == null) {
            assert(User::$db != null, 'User wurde benutzt bevor es initialisiert wurde.');
            User::$instance = new User();
        }
        return User::$instance;
    }

    function __toString() {
        return $this->getLoginFormString();
    }

    private function getLoginFormString() {
        return '<tr><td class="content_table_headline">Login</td></tr><tr><td><form method="post" action="./">
<p>Nutzername:</p><input name="userName" />
<p>Passwort:</p><input name="userPass" type="password" />
<input type="submit" value="Login">
</form></td></tr>';
    }
} 