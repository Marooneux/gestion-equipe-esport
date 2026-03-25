<h1>Ajouter un joueur</h1>
<?php
require_once __DIR__ . '/../../Controleur/ApiClient.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donnees = [
        'id' => 0,
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'numero_licence' => $_POST['numeroDeLicence'],
        'date_naissance' => $_POST['dateDeNaissance'],
        'taille' => (int) $_POST['tailleEnCm'],
        'poids' => (int) $_POST['poidsEnKg'],
        'statut' => $_POST['statut'],
    ];
    $reponse = api_post('/joueurs', $donnees);
    if ($reponse['status_code'] === 201) {
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
