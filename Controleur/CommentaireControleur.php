<?php

namespace R301\Controleur;

require_once __DIR__ . '/ApiClient.php';

class CommentaireControleur {
    private static ?CommentaireControleur $instance = null;

    private function __construct() {}

    public static function getInstance(): CommentaireControleur {
        if (self::$instance === null) {
            self::$instance = new CommentaireControleur();
        }
        return self::$instance;
    }

    public function ajouterCommentaire(string $contenu, string $joueurId): bool {
        $reponse = api_post("/joueurs/$joueurId/commentaires", ['contenu' => $contenu]);
        return isset($reponse['status_code']) && $reponse['status_code'] === 201;
    }

    public function listerLesCommentairesDuJoueur(int $joueurId): array {
        return api_get("/joueurs/$joueurId/commentaires")['data'] ?? [];
    }

    public function supprimerCommentaire(string $commentaireId): bool {
        $reponse = api_delete("/commentaires/$commentaireId");
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }
}
