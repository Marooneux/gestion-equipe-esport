<?php

use R301\Controleur\JoueurControleur;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    JoueurControleur::getInstance()->supprimerJoueur((int) $_POST['id']);
}

header('Location: /joueur');
exit;
