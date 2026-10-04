<h1>Modifier une rencontre</h1>

<?php

use R301\Controleur\RencontreControleur;
use R301\Vue\Component\Formulaire;

$controleur = RencontreControleur::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_GET['id'])
        && isset($_POST['dateHeure'])
        && isset($_POST['equipeAdverse'])
        && isset($_POST['adresse'])
        && isset($_POST['lieu'])
) {
    if ($controleur->modifierRencontre(
        (int) $_GET['id'],
        $_POST['dateHeure'],
        $_POST['equipeAdverse'],
        $_POST['adresse'],
        $_POST['lieu']
    )) {
        header('Location: /rencontre');
        exit;
    } else {
        error_log("Erreur lors de la modification de la rencontre");
    }
} else {
    if (!isset($_GET['id'])) {
        header("Location: /rencontre");
    } else {
        $rencontre = $controleur->getRencontreById((int) $_GET['id']);
        $dateStr = is_array($rencontre['date_heure']) ? $rencontre['date_heure']['date'] : $rencontre['date_heure'];
        $dateForInput = date('Y-m-d\TH:i', strtotime($dateStr));

        $formulaire = new Formulaire("/rencontre/modifier?id=" . $rencontre['id']);
        $formulaire->setDateTime("Date", "dateHeure", date("Y-m-d H:i"), $dateForInput);
        $formulaire->setText("Equipe adverse", "equipeAdverse", "", $rencontre['equipe_adverse']);
        $formulaire->setText("Adresse", "adresse", "", $rencontre['adresse']);
        $formulaire->setSelect("Lieu", ['DOMICILE', 'EXTERIEUR'], "lieu", $rencontre['lieu_recontre']);
        $formulaire->addButton("Submit", "update", "Valider", "Modifier");
        echo $formulaire;
    }
}
