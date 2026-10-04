# API de gestion d'équipe

API REST qui gère les données de l'équipe : joueurs, commentaires, rencontres, feuilles de match et statistiques. Toutes les routes sont protégées : chaque requête doit contenir un token JWT, que l'API fait valider par le [service d'authentification](../auth/).

## Endpoints

Toutes les routes attendent l'en-tête `Authorization: Bearer <token>`.

| Ressource | Routes |
|---|---|
| Joueurs | `GET` `POST` `/joueurs` · `GET` `PUT` `DELETE` `/joueurs/{id}` |
| Commentaires | `GET` `POST` `/joueurs/{id}/commentaires` · `PUT` `DELETE` `/joueurs/{id}/commentaires/{commentaireId}` |
| Rencontres | `GET` `POST` `/rencontre` · `GET` `PUT` `DELETE` `/rencontre/{id}` · `PATCH` `/rencontre/{id}` (saisie du résultat) |
| Feuilles de match | `GET` `POST` `/feuilledematche` · `GET` `PUT` `DELETE` `/feuilledematche/{id}` |
| Statistiques | `GET` `/statistiques` (équipe) · `GET` `/statistiques/joueurs` |

Documentation interactive : https://r401teammanagementapi.alwaysdata.net/docs/ (spécification dans `openapi.yaml`)

## Structure

```
backend/
├── index.php          Point d'entrée : déclaration des routes et middleware d'authentification
├── View/              Route handlers : lecture de la requête et réponse JSON
├── Controleur/        Logique métier (Singletons)
├── Modele/            Entités, énumérations et DAO, regroupés par domaine
│   ├── Joueur/
│   ├── Rencontre/
│   ├── Participation/
│   ├── Statistiques/
│   └── Utilisateur/
├── Utils/             Routeur, réponses HTTP, vérification JWT
├── docs/              Swagger UI
├── openapi.yaml
├── schema.sql         Schéma de la base
└── .htaccess          Réécriture des URLs vers index.php et blocage du .env
```

Le routage est fait maison (`Utils/Routing`) et le chargement des classes passe par un autoloader PSR-4.

## Installation locale

### Prérequis
PHP 8.1+ avec `pdo_mysql` et `curl`, Apache avec `mod_rewrite`, MySQL.

### 1. Base de données
Importer `schema.sql`. Le script crée la base `r301`, un utilisateur MySQL dédié et les tables.

### 2. Fichier `.env`
À créer à la racine du dossier `backend/` (il est ignoré par Git) :

```ini
DB_HOST=localhost
DB_NAME=r301
DB_USER=utilisateur
DB_PASSWORD=mot_de_passe
```

### 3. Service d'authentification
Par défaut, l'API valide les tokens auprès du service en ligne. Pour utiliser un service d'authentification local, modifier la constante `AUTH_VERIFY_URL` dans `index.php` (ex : `http://auth.local/auth/verify`).

### 4. VirtualHost Apache

```apache
<VirtualHost *:80>
    ServerName api.local
    DocumentRoot "C:/chemin/vers/backend"

    <Directory "C:/chemin/vers/backend">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Ajouter `127.0.0.1 api.local` dans le fichier hosts.
