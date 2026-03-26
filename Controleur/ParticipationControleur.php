<?php
namespace R301\Controleur;

use DateTime;
use DateTimeZone;
use R301\Modele\Joueur\Joueur;
use R301\Modele\Joueur\JoueurStatut;
use R301\Modele\Participation\FeuilleDeMatch;
use R301\Modele\Participation\Participation;
use R301\Modele\Participation\Performance;
use R301\Modele\Participation\Poste;
use R301\Modele\Participation\TitulaireOuRemplacant;
use R301\Modele\Rencontre\Rencontre;
use R301\Modele\Rencontre\RencontreLieu;
use R301\Modele\Rencontre\RencontreResultat;

require_once __DIR__ . '/ApiClient.php';

class ParticipationControleur {
    private static ?ParticipationControleur $instance = null;
    private readonly JoueurControleur $joueurs;
    private readonly RencontreControleur $rencontres;

    private function __construct(JoueurControleur $joueurs) {
        $this->joueurs = $joueurs;
        $this->rencontres = RencontreControleur::getInstance();
    }

    public static function getInstance(): ParticipationControleur {
        if (self::$instance == null) {
            self::$instance = new ParticipationControleur(JoueurControleur::getInstance());
        }
        return self::$instance;
    }

    //Cette méthode permet de briser la dépendance cyclique entre les deux controleurs
    public static function getInstanceFromJoueurControleur(JoueurControleur $joueurs): ParticipationControleur {
        if (self::$instance == null) {
            self::$instance = new ParticipationControleur($joueurs);
        }
        return self::$instance;
    }

    private function buildJoueurFromData(array $data): Joueur {
        return new Joueur(
            $data['id'],
            $data['nom'],
            $data['prenom'],
            $data['numero_licence'],
            new DateTime($data['date_naissance']),
            $data['taille'],
            $data['poids'],
            isset($data['statut']) ? JoueurStatut::fromName($data['statut']) : null
        );
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

    private function buildParticipationFromApiData(array $data): Participation {
        $joueur = $this->buildJoueurFromData($data['joueur']);
        $rencontre = $this->buildRencontreFromData($data['rencontre']);

        return new Participation(
            $data['id'],
            $joueur,
            $rencontre,
            TitulaireOuRemplacant::fromName($data['titularité']),
            isset($data['performance']) && $data['performance'] !== null ? Performance::fromName($data['performance']) : null,
            Poste::fromName($data['poste'])
        );
    }

    public function lejoueurEstDejaSurLaFeuilleDeMatch(int $rencontreId, int $joueurId) : bool {
        $feuille = $this->getFeuilleDeMatch($rencontreId);
        foreach ($feuille->getParticipants() as $participation) {
            if ($participation->getParticipant()->getJoueurId() === $joueurId) {
                return true;
            }
        }
        return false;
    }

    public function listerToutesLesParticipations() : array {
        $reponse = api_get('/feuilledematche');
        if (!isset($reponse['data']) || !is_array($reponse['data'])) return [];
        return array_map(fn($d) => $this->buildParticipationFromApiData($d), $reponse['data']);
    }

    public function getFeuilleDeMatch(int $rencontreId) : FeuilleDeMatch {
        $toutesLesParticipations = $this->listerToutesLesParticipations();
        $participationsDuMatch = array_filter(
            $toutesLesParticipations,
            fn($p) => $p->getRencontre()->getRencontreId() === $rencontreId
        );
        return new FeuilleDeMatch(array_values($participationsDuMatch));
    }

    public function assignerUnParticipant(
        int $joueurId,
        int $rencontreId,
        Poste $poste,
        TitulaireOuRemplacant $titulaireOuRemplacant
    ) : bool {
        $donnees = [
            'joueur_id' => $joueurId,
            'rencontre_id' => $rencontreId,
            'poste' => $poste->name,
            'titularité' => $titulaireOuRemplacant->name
        ];
        $reponse = api_post('/feuilledematche', $donnees);
        return isset($reponse['status_code']) && $reponse['status_code'] === 201;
    }

    public function assignerUnParticipantByArray(Participation $participantAAjouter) {
        return $this->assignerUnParticipant(
            $participantAAjouter->getParticipant()->getJoueurId(),
            $participantAAjouter->getRencontre()->getRencontreId(),
            $participantAAjouter->getPoste(),
            $participantAAjouter->getTitulaireOuRemplacant()
        );
    }

    // Non disponible : pas d'endpoint PUT /feuilledematche/{id} dans le backend
    public function modifierParticipation(
        int $participationId,
        Poste $poste,
        TitulaireOuRemplacant $titulaireOuRemplacant,
        int $joueurId
    ) : bool {
        return false;
    }

    // Non disponible : pas d'endpoint DELETE /feuilledematche/{id} dans le backend
    public function supprimerLaParticipation(int $participationId) : bool {
        return false;
    }

    // Non disponible : pas d'endpoint pour les performances dans le backend
    public function mettreAJourLaPerformance(
        int $participationId,
        string $performance
    ) : bool {
        return false;
    }

    // Non disponible : pas d'endpoint pour les performances dans le backend
    public function supprimerLaPerformance(int $participationId) : bool {
        return false;
    }

    public function buildParticipationFromArray($data) {
        $joueur = $this->joueurs->getJoueurById($data['joueur_id']);
        $rencontre = $this->rencontres->getRencontreById($data['rencontre_id']);

        return new Participation(
            $data['id'],
            $joueur,
            $rencontre,
            TitulaireOuRemplacant::fromName($data['titularité']),
            Performance::fromName($data['performance']),
            Poste::fromName($data['poste'])
        );
    }
}
