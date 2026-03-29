<?php

namespace R301\Controleur;

use DateTime;
use R301\Modele\Joueur\Commentaire\Commentaire;
use R301\Modele\Joueur\Commentaire\CommentaireDAO;
use R301\Modele\Joueur\JoueurDAO;

class CommentaireControleur {
    private static ?CommentaireControleur $instance = null;
    private readonly CommentaireDAO $commentaires;
    private readonly JoueurDAO $joueurs;

    private function __construct() {
        $this->commentaires = CommentaireDAO::getInstance();
        $this->joueurs = JoueurDAO::getInstance();
    }

    public static function getInstance(): CommentaireControleur {
        if (self::$instance == null) {
            self::$instance = new CommentaireControleur();
        }
        return self::$instance;
    }

    public function listerLesCommentairesDuJoueur(int $joueurId) : array {
        if ($this->joueurs->selectJoueurById($joueurId) === false) {
            return [];
        }

        return $this->commentaires->selectCommentaireByJoueurId($joueurId);
    }

    public function ajouterCommentaire(string $contenu, int $joueurId): bool {
        if ($this->joueurs->selectJoueurById($joueurId) === false) {
            return false;
        }

        $commentaireACreer = new Commentaire(
            0,
            $contenu,
            new DateTime()
        );

        return $this->commentaires->insertCommentaire($commentaireACreer, $joueurId);
    }

    public function modifierCommentaire(int $joueurId, int $commentaireId, string $contenu): bool
    {
        if ($this->joueurs->selectJoueurById($joueurId) === false) {
            return false;
        }

        return $this->commentaires->updateCommentaireByIdForJoueur($joueurId, $commentaireId, $contenu);
    }

    public function supprimerCommentaire(int $joueurId, int $commentaireId) : bool {
        if ($this->joueurs->selectJoueurById($joueurId) === false) {
            return false;
        }

        return $this->commentaires->deleteCommentaireForJoueur($joueurId, $commentaireId);
    }
}