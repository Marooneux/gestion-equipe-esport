<h1>Ajouter un joueur</h1>
<?php

use R301\Controleur\JoueurControleur;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controleur = JoueurControleur::getInstance();
    $succes = $controleur->ajouterJoueur(
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['numeroDeLicence'],
        new DateTime($_POST['dateDeNaissance']),
        (int) $_POST['tailleEnCm'],
        (int) $_POST['poidsEnKg'],
        $_POST['statut']
    );
    if ($succes) {
        header('Location: /joueur');
        exit;
    }
}
?>

<div class="container">
    <form action="/joueur/ajouter" method="post">
        <div class="row">
            <div class="col-20"><label for="nom">Nom</label></div>
            <div class="col-80"><input type="text" id="nom" name="nom" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="prenom">Prenom</label></div>
            <div class="col-80"><input type="text" id="prenom" name="prenom" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="numeroDeLicence">Numéro de license</label></div>
            <div class="col-80"><input type="text" id="numeroDeLicence" name="numeroDeLicence" placeholder="00042" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="dateDeNaissance">Date de naissance</label></div>
            <div class="col-80"><input type="date" id="dateDeNaissance" name="dateDeNaissance" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="tailleEnCm">Taille (en cm)</label></div>
            <div class="col-80"><input type="text" id="tailleEnCm" name="tailleEnCm" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="poidsEnKg">Poids (en kg)</label></div>
            <div class="col-80"><input type="text" id="poidsEnKg" name="poidsEnKg" required></div>
        </div>
        <div class="row">
            <div class="col-20"><label for="statut">Statut</label></div>
            <div class="col-80">
                <select id="statut" name="statut">
                    <option value="ACTIF">ACTIF</option>
                    <option value="BLESSE">BLESSE</option>
                    <option value="ABSENT">ABSENT</option>
                    <option value="SUSPENDU">SUSPENDU</option>
                </select>
            </div>
        </div>
        <div class="row">
            <input class="create" type="submit" value="Valider">
        </div>
    </form>
</div>
