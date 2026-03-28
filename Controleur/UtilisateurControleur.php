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

    public function seConnecter(string $username, string $password): bool {
        $reponse = api_post('/auth', ['login' => $username, 'password' => $password]);
        if (isset($reponse['status_code']) && $reponse['status_code'] === 200) {
            session_set_cookie_params(1800);
            ini_set('session.gc_maxlifetime', 1800);
            $_SESSION['username'] = $username;
            return true;
        }
        return false;
    }
}
