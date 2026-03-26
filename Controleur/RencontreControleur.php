<?php

namespace R301\Controleur;

use DateTime;
use DateTimeZone;
use R301\Modele\Rencontre\Rencontre;
use R301\Modele\Rencontre\RencontreLieu;
use R301\Modele\Rencontre\RencontreResultat;

require_once __DIR__ . '/ApiClient.php';

class RencontreControleur {
    private static ?RencontreControleur $instance = null;

    private function __construct() {}

    public static function getInstance(): RencontreControleur {
        if (self::$instance == null) {
            self::$instance = new RencontreControleur();
        }
        return self::$instance;
    }

    private function buildRencontreFromData(array $data): Rencontre {
        $dateTimeData = $data['date_heure'];
        if (is_array($dateTimeData)) {
            $datetime = new DateTime($dateTimeData['date'], new DateTimeZone($dateTimeData['timezone']));
        } else {
            $datetime = new DateTime($dateTimeData);
        }

        return new Rencontre(
            $datetime,
            $data['equipe_adverse'],
            $data['adresse'],
            isset($data['lieu_recontre']) && $data['lieu_recontre'] !== null ? RencontreLieu::fromName($data['lieu_recontre']) : null,
            isset($data['resultat']) && $data['resultat'] !== null ? RencontreResultat::fromName($data['resultat']) : null,
            $data['id']
        );
    }

    public function ajouterRencontre(
        DateTime $dateHeure,
        string $equipeAdverse,
        string $adresse,
        RencontreLieu $lieu
    ) : bool {
        if ($dateHeure < new DateTime()) {
            return false;
        }

        $rencontreAAjouter = new Rencontre($dateHeure, $equipeAdverse, $adresse, $lieu);
        return $this->ajouterRencontreFromArray($rencontreAAjouter);
    }

    public function ajouterRencontreFromArray($rencontreAAjouter) {
        $reponse = api_post('/rencontre', $rencontreAAjouter->jsonSerialize());
        return isset($reponse['status_code']) && $reponse['status_code'] === 201;
    }

    // Non disponible : le backend ne fournit pas d'endpoint pour enregistrer un résultat
    public function enregistrerResultat(
        int $rencontreId,
        string $resultat
    ) : bool {
        return false;
    }

    public function getRencontreById(int $rencontreId) : ?Rencontre {
        $reponse = api_get('/rencontre/' . $rencontreId);
        if (!isset($reponse['data'])) return null;
        return $this->buildRencontreFromData($reponse['data']);
    }

    public function listerToutesLesRencontres() : array {
        $reponse = api_get('/rencontre');
        if (!isset($reponse['data']) || !is_array($reponse['data'])) return [];
        return array_map(fn($d) => $this->buildRencontreFromData($d), $reponse['data']);
    }

    public function modifierRencontre(
        int $rencontreId,
        DateTime $dateHeure,
        string $equipeAdverse,
        string $adresse,
        RencontreLieu $lieu
    ) : bool {
        $rencontreActuelle = $this->getRencontreById($rencontreId);

        if ($rencontreActuelle === null || $rencontreActuelle->estPassee() || $dateHeure < new DateTime()) {
            return false;
        }

        $rencontreAModifier = new Rencontre($dateHeure, $equipeAdverse, $adresse, $lieu, null, $rencontreId);
        return $this->modifierRencontreByArray($rencontreAModifier);
    }

    public function modifierRencontreByArray($rencontreAModifier) {
        $reponse = api_put('/rencontre/' . $rencontreAModifier->getRencontreId(), $rencontreAModifier->jsonSerialize());
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }

    public function supprimerRencontre(int $rencontreId) : bool {
        $rencontre = $this->getRencontreById($rencontreId);
        if ($rencontre === null || $rencontre->getResultat() !== null) {
            return false;
        }
        $reponse = api_delete('/rencontre/' . $rencontreId);
        return isset($reponse['status_code']) && $reponse['status_code'] === 200;
    }
}
