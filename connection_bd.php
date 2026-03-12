<?php
class connection_bd {

    function getConnexion() {
        $host = 'mysql-r401auth.alwaysdata.net';
        $dbname = 'r401auth_users';
        $user = 'r401auth';
        $mdp = 'projetr401';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $mdp);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;

        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
}
?>