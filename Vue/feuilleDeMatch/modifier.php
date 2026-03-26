<?php

use R301\Controleur\ParticipationControleur;

$controleur = ParticipationControleur::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && isset($_POST['poste'])
    && isset($_POST['titulaireOuRemplacant'])
    && isset($_POST['joueurId']) && $_POST['joueurId'] !== ""
    && isset($_POST['rencontreId'])
) {
    switch($_POST['action']) {
        case "create":
            if (!$controleur->assignerUnParticipant(
                (int) $_POST['joueurId'],
                (int) $_POST['rencontreId'],
                $_POST['poste'],
                $_POST['titulaireOuRemplacant']
            )) {
                error_log("Erreur lors de l'ajout d'une participation");
            }
            break;
        case "update":
            if (isset($_POST['participationId'])) {
                if (!$controleur->modifierParticipation(
                    (int) $_POST['participationId'],
                    $_POST['poste'],
                    $_POST['titulaireOuRemplacant'],
                    (int) $_POST['joueurId']
                )) {
                    error_log("Erreur lors de la modification de la participation");
                }
            }
            break;
        case "delete":
            if (isset($_POST['participationId'])) {
                if (!$controleur->supprimerLaParticipation((int) $_POST['participationId'])) {
                    error_log("Erreur lors de la suppression de la participation");
                }
            }
            break;
    }
    header('Location: /feuilleDeMatch/feuilleDeMatch?id='.$_POST['rencontreId']);
} else {
    if (isset($_POST['rencontreId'])) {
        header('Location: /feuilleDeMatch/feuilleDeMatch?id='.$_POST['rencontreId']);
    } else {
        header('Location: /rencontre');
    }
}
