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
            $token = $reponse['data'];
            $role = $this->extraireRole($token);

            if ($role !== 'coach' && $role !== 'joueur') {
                return false;
            }

            $_SESSION['username'] = $username;
            $_SESSION['token'] = $token;
            $_SESSION['role'] = $role;
            return true;
        }
        return false;
    }

    private function extraireRole(string $token): ?string {
        $parties = explode('.', $token);
        if (count($parties) !== 3) {
            return null;
        }
        $payload = json_decode(base64_decode(strtr($parties[1], '-_', '+/')), true);
        return $payload['role'] ?? null;
    }
}
