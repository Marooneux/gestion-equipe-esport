<?php

namespace R301\Modele\Rencontre;
use DateTime, DateTimeZone;
use InvalidArgumentException;

class Rencontre implements \JsonSerializable {
    private int $rencontreId;
    private DateTime $dateEtHeure;
    private string $equipeAdverse;
    private string $adresse;
    private ?RencontreLieu $lieu;
    private ?RencontreResultat $resultat;

    public function __construct(
        DateTime $dateEtheure,
        string $equipeAdverse,
        string $adresse,
        ?RencontreLieu $lieu,
        ?RencontreResultat $resultat = null,
        int $rencontreId = 0
    ) {
        $this->rencontreId = $rencontreId;
        $this->dateEtHeure = $dateEtheure;
        $this->equipeAdverse = $equipeAdverse;
        $this->adresse = $adresse;
        $this->lieu = $lieu;
        $this->resultat = $resultat;
    }

    public function getRencontreId(): int
    {
        return $this->rencontreId;
    }

    public function getDateEtHeure(): DateTime
    {
        return $this->dateEtHeure;
    }

    public function setDateEtHeure(DateTime $dateEtHeure): void {
        $this->dateEtHeure = $dateEtHeure;
    }

    public function getEquipeAdverse(): string
    {
        return $this->equipeAdverse;
    }

    public function setEquipeAdverse(string $equipeAdverse): void
    {
        $this->equipeAdverse = $equipeAdverse;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getLieu(): ?RencontreLieu
    {
        return $this->lieu;
    }

    public function setLieu(?RencontreLieu $lieu): void
    {
        $this->lieu = $lieu;
    }

    public function joue(): bool {
        return $this->resultat !== null;
    }

    public function gagne(): bool {
        return $this->resultat === RencontreResultat::VICTOIRE;
    }

    public function nul(): bool {
        return $this->resultat === RencontreResultat::NUL;
    }

    public function perdu(): bool {
        return $this->resultat === RencontreResultat::DEFAITE;
    }

    public function getResultat(): ?RencontreResultat
    {
        return $this->resultat;
    }

    public function setResultat(?RencontreResultat $resultat): void
    {
        $this->resultat = $resultat;
    }

    public function estPassee(): bool {
        return $this->dateEtHeure < new DateTime();
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->rencontreId,
            'date_heure' => $this->dateEtHeure,
            'equipe_adverse' => $this->equipeAdverse,
            'adresse' => $this->adresse,
            'lieu_recontre' => $this->lieu?->name,
            'resultat' => $this->resultat?->name
        ];
    }

    public static function buildRencontreFromArray(array $data) {
        if(sizeof($data) != 6) {
            throw new InvalidArgumentException('Vous devez modifier toutes les champs de la ressource.');
        }

        $dateTimeArray = $data["date_heure"];
        $datetime = new Datetime($dateTimeArray["date"], new DateTimeZone($dateTimeArray["timezone"]));

        if ($datetime < date("Y-m-d H:i:s")) {
            return false;
        }

        return new self(
            $datetime,
            $data['equipe_adverse'],
            $data['adresse'],
            RencontreLieu::fromName($data['lieu_recontre']),
            $data['resultat'] ? RencontreResultat::fromName($data['resultat']) : null,
            $data['id']
        );
    }
}
