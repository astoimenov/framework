<?php

namespace LittleNinja;

use mysqli;

class Database {

    private static $db = null;

    private function __construct() {
        $app = App::getInstance();
        $config = $app->getConfig();
        $db = new mysqli(
                $config->db['host'], $config->db['user'], $config->db['pass'], $config->db['name']
        );
        self::$db = $db;
    }

    public static function getInstance() {
        static $instance = null;

        if ($instance === null) {
            $instance = new static();
        }

        return $instance;
    }

    public static function getDb() {
        return self::$db;
    }

}
