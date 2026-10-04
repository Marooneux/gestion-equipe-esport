<?php

namespace R301\Modele;

use Exception;
use PDO;

class DatabaseHandler {
    private static ?DatabaseHandler $instance = null;
    private readonly PDO $linkpdo;
    private readonly string $server;
    private readonly string $db;
    private readonly string $login;
    private readonly string $mdp;

    private function __construct(){
        try{
            $env = parse_ini_file(__DIR__ . '/../.env') ?: [];
            $this->server = $env['DB_HOST'] ?? $_ENV['DB_HOST'] ?? '';
            $this->db = $env['DB_NAME'] ?? $_ENV['DB_NAME'] ?? '';
            $this->login = $env['DB_USER'] ?? $_ENV['DB_USER'] ?? '';
            $this->mdp = $env['DB_PASSWORD'] ?? $_ENV['DB_PASSWORD'] ?? '';
            $this->linkpdo=new PDO("mysql:host=".$this->server.";dbname=".$this->db,$this->login,$this->mdp);
        }catch(Exception $e){
            die("Erreur : ".$e->getMessage());
        }
    }

    public static function getInstance(): DatabaseHandler
    {
        if (self::$instance == null) {
            self::$instance = new DatabaseHandler();
        }
        return self::$instance;
    }

    public function pdo(): PDO {
        return $this->linkpdo;
    }
}