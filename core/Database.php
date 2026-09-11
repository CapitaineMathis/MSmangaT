<?php

class Database {

    private static $instance = null;

    public static function getConnection()
    {
        if (self::$instance === null) {
            $config_file = './config.local.php';
            if (!file_exists($config_file)){
                die("Erreur: the config file dose not exist");
            }
            $config = require $config_file;


            $host = $config['DB-host'];
            $dbname = $config['DB-name'];
            $user = $config['DB-user'];
            $pass = $config['DB-pass'];
            try {
                self::$instance = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8",
                    $user,
                    $pass
                );

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}