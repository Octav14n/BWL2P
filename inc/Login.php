<?php
/**
 * Created by PhpStorm.
 * User: octavian
 * Date: 21.11.14
 * Time: 10:36
 */
define('SQL_USER_SEL', 'SELECT KundeID, Nutzername, Passwort, Vorname, Nachname, EMail, Geschlecht FROM Kunde WHERE Nutzername = ?');

class User {
    /** @var $instance User */
    private static $instance;
    /** @var $db PDO */
    private static $db;
    private $query_user;
    private $loginUser;

    private function __construct() {
        $this->query_user = User::$db->prepare(SQL_USER_SEL);
        if (isset($_POST['userName']) && isset($_POST['userPass'])) {
            $this->doLogin($_POST['userName'], $_POST['userPass']);
        } else if (isset($_SESSION['userName'])) {
            $this->doLogin($_SESSION['userName'], null);
        }
    }

    public static function init(PDO $db) {
        static::$db = $db;
    }

    /**
     * @return User
     */
    public static function getInstance() {
        if (static::$instance == null) {
            assert(static::$db != null, __CLASS__ . ' wurde benutzt bevor es initialisiert wurde.');
            static::$instance = new User();
        }
        return static::$instance;
    }

    public function getUser() {
        if ($this->loginUser) {
            $user = new stdClass();
            $user->Nutzername = $this->loginUser->Nutzername;
            $user->Vorname = $this->loginUser->Vorname;
            $user->Nachname = $this->loginUser->Nachname;
            return $user;
        } else {
            return null;
        }
    }

    public function getKundeID() {
        if (!$this->loginUser)
            return -1;
        return $this->loginUser->KundeID;
    }

    public function isLogin() {
        return isset($this->loginUser);
    }

    private function doLogin($name, $pass) {
        $this->query_user->execute(array($name));
        if ($user = $this->query_user->fetchObject()) {
            if ($pass === null || $user->Passwort == $pass) {
                $this->loginUser = $user;
                $_SESSION['userID'] = $user->KundeID;
                $_SESSION['userName'] = $user->Nutzername;
            }
        }
    }
}