# Interface web du coach

Application web qui permet au coach de gérer son équipe. Elle ne se connecte à aucune base de données : toutes les données passent par le [backend](../backend/), et la connexion par le [service d'authentification](../auth/).

Démo : https://frontendr401.alwaysdata.net/login (compte `coach` / `sport`)

## Pages

| Page | Contenu |
|---|---|
| Connexion | Récupère un token JWT auprès du service d'authentification et le conserve en session |
| Tableau de bord | Statistiques de l'équipe et de chaque joueur |
| Joueurs | Liste, recherche, ajout, modification, suppression, commentaires du coach |
| Rencontres | Liste des matchs, ajout, modification, saisie du résultat |
| Feuille de match | Composition par poste (titulaires et remplaçants) et évaluation des performances |

Toutes les pages sauf la connexion redirigent vers `/login` si l'utilisateur n'est pas authentifié.

## Structure

```
frontend/
├── index.php              Point d'entrée : routage et contrôle de session
├── Controleur/
│   ├── ApiClient.php      Appels HTTP vers le backend et le service d'authentification
│   └── ...                Un contrôleur par ressource
├── Vue/
│   ├── Component/         Composants réutilisables (formulaires, listes déroulantes)
│   ├── joueur/
│   ├── rencontre/
│   └── feuilleDeMatch/
└── stylesheet.css
```

## Installation locale

### Prérequis
PHP 8.1+ (avec `allow_url_fopen` activé et l'extension `openssl`), Apache avec `mod_rewrite`.

### 1. URLs des API
Par défaut, le frontend utilise les API en ligne. Pour pointer vers des services locaux, modifier dans `Controleur/ApiClient.php` :
- la constante `API_URL` (ex : `http://api.local`)
- l'URL de connexion (ex : `http://auth.local/auth/login`)

### 2. VirtualHost Apache

```apache
<VirtualHost *:80>
    ServerName front.local
    DocumentRoot "C:/chemin/vers/frontend"

    <Directory "C:/chemin/vers/frontend">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Ajouter `127.0.0.1 front.local` dans le fichier hosts, puis ouvrir http://front.local/login.
