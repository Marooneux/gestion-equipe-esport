<?php

namespace R301\Modele\Joueur\Commentaire;

use DateTime;
use PDO;
use R301\Modele\DatabaseHandler;

class CommentaireDAO {
    private static ?CommentaireDAO $instance = null;
    private readonly DatabaseHandler $database;

    private function __construct() {
        $this->database = DatabaseHandler::getInstance();
    }

    public static function getInstance(): CommentaireDAO {
        if (self::$instance == null) {
            self::$instance = new CommentaireDAO();
        }
        return self::$instance;
    }

    private function mapToCommentaire(array $dbLine): Commentaire {
    return new Commentaire(
        $dbLine['commentaire_id'],
        $dbLine['contenu'],
        new DateTime($dbLine['date'])
    );
}

    public function selectCommentaireByJoueurId(int $joueurId): array {
        $query = 'SELECT * FROM commentaire WHERE joueur_id = :joueur_id';
        $statement = $this->database->pdo()->prepare($query);
        if ($statement->execute(['joueur_id' => $joueurId])) {
            return array_map(
                function($commentaire) { return $this->mapToCommentaire($commentaire); },
                $statement->fetchAll(PDO::FETCH_ASSOC)
            );
        } else {
            exit();
        }
    }

    public function selectCommentaireByIdForJoueur(int $joueurId, int $commentaireId): Commentaire|false
    {
        $query = 'SELECT * FROM commentaire WHERE commentaire_id = :commentaire_id AND joueur_id = :joueur_id';
        $statement = $this->database->pdo()->prepare($query);
        $statement->bindValue(':commentaire_id', $commentaireId);
        $statement->bindValue(':joueur_id', $joueurId);
        $statement->execute();

        if ($statement->rowCount() <= 0) {
            return false;
        }

        return $this->mapToCommentaire($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function insertCommentaire(Commentaire $commentaire, int $joueurId): bool {
        $query = 'INSERT INTO commentaire(contenu,date,joueur_id) 
            values (:contenu,:date,:joueur_id)';
        $statement = $this->database->pdo()->prepare($query);
        $statement->bindValue(':joueur_id', $joueurId);
        $statement->bindValue(':contenu', $commentaire->getContenu());
        $statement->bindValue(':date', $commentaire->getDate()->format('Y-m-d H:i'));

        return $statement->execute();
    }

    public function updateCommentaireByIdForJoueur(int $joueurId, int $commentaireId, string $contenu): bool
    {
        $query = 'UPDATE commentaire
                  SET contenu = :contenu
                  WHERE commentaire_id = :commentaireId AND joueur_id = :joueurId';
        $statement = $this->database->pdo()->prepare($query);
        $statement->bindValue(':contenu', $contenu);
        $statement->bindValue(':commentaireId', $commentaireId);
        $statement->bindValue(':joueurId', $joueurId);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function deleteCommentaireForJoueur(int $joueurId, int $commentaireId): bool {
        $query = 'DELETE FROM commentaire WHERE commentaire_id = :commentaireId AND joueur_id = :joueurId';
        $statement = $this->database->pdo()->prepare($query);
        $statement->bindValue(':commentaireId', $commentaireId);
        $statement->bindValue(':joueurId', $joueurId);
        $statement->execute();

        return $statement->rowCount() > 0;
    }
}