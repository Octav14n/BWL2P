<?php
/**
 * Created by PhpStorm.
 * User: octavian
 * Date: 21.11.14
 * Time: 10:32
 */

class Warenkorb {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    function __toString() {
        return '';
    }
} 