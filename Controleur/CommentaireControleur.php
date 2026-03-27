<?php

namespace R301\Controleur;

use DateTime;
use R301\Modele\Joueur\Commentaire\Commentaire;
use R301\Modele\Joueur\Commentaire\CommentaireDAO;

class CommentaireControleur {
    private static ?CommentaireControleur $instance = null;
    private readonly CommentaireDAO $commentaires;

    private function __construct() {
        $this->commentaires = CommentaireDAO::getInstance();
    }

    public static function getInstance(): CommentaireControleur {
        if (self::$instance === null) {
            self::$instance = new CommentaireControleur();
        }
        return self::$instance;
    }

    public function ajouterCommentaire(string $contenu, string $joueurId): bool {
        $commentaireACreer = new Commentaire(0, $contenu, new DateTime());
        return $this->commentaires->insertCommentaire($commentaireACreer, $joueurId);
    }

    public function listerLesCommentairesDuJoueur(int $joueurId): array {
        $commentaires = $this->commentaires->selectCommentaireByJoueurId($joueurId);
        return array_map(fn($c) => [
            'id'      => $c->getCommentaireId(),
            'contenu' => $c->getContenu(),
            'date'    => $c->getDate()->format('Y-m-d H:i:s'),
        ], $commentaires);
    }

    public function supprimerCommentaire(string $commentaireId): bool {
        return $this->commentaires->deleteCommentaire($commentaireId);
    }
}
