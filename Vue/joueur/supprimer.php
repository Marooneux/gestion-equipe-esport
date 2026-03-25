<?php
require_once __DIR__ . '/../../Controleur/ApiClient.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    api_delete('/joueurs/' . $_POST['id']);
}

header('Location: /joueur');
exit;
