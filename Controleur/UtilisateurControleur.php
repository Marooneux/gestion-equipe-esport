<?php

namespace R301\Controleur;

require_once __DIR__ . '/ApiClient.php';

class UtilisateurControleur {
    private static ?UtilisateurControleur $instance = null;

    private function __construct() {}

    public static function getInstance(): UtilisateurControleur {
        if (self::$instance === null) {
            self::$instance = new UtilisateurControleur();
        }
        return self::$instance;
    }

    public function seConnecter($username, $password) {
        $reponse = auth_get_token($username, $password);
        if ($reponse['status_code'] === 200) {
            $_SESSION['username'] = $username;
            $_SESSION['token'] = $reponse['data'];
            return true;
        }
        return false;
    }
}
