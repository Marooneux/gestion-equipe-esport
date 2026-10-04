# Service d'authentification

API qui vérifie les identifiants des utilisateurs et délivre des tokens **JWT**. Le [frontend](../frontend/) l'utilise pour la connexion, et le [backend](../backend/) pour valider chaque requête.

## Endpoints

| Méthode | Route | Corps (JSON) | Réponse |
|---|---|---|---|
| `POST` | `/auth/login` | `{ "login": "...", "password": "..." }` | Token JWT valable 1 heure |
| `POST` | `/auth/verify` | `{ "token": "..." }` | `200` si le token est valide, `400` sinon |

Le token contient l'identifiant de l'utilisateur, son rôle et sa date d'expiration. Il est signé en HS256 avec un secret défini dans le `.env`.

Documentation interactive : https://r401auth.alwaysdata.net/docs/ (spécification dans `docs/openapi.yaml`)

## Structure

```
auth/
├── api/
│   ├── get_token.php        Endpoint /auth/login
│   └── verify_token.php     Endpoint /auth/verify
├── src/
│   ├── modele/
│   │   ├── DatabaseHandler.php   Connexion PDO (Singleton)
│   │   └── verifAuth.php         Vérification des identifiants
│   └── utils/
│       └── jwt_utils.php         Génération et validation des JWT
├── docs/                    OpenAPI + Swagger UI
├── ressources/schema.sql    Table des utilisateurs + compte de démo
└── .htaccess                Réécriture des routes et blocage de .env et *.sql
```

## Installation locale

### Prérequis
PHP 8.1+ avec `pdo_mysql`, Apache avec `mod_rewrite`, MySQL.

### 1. Base de données
Créer une base puis importer `ressources/schema.sql`. Le script crée le compte de démo `coach` / `sport`.

### 2. Fichier `.env`
À créer à la racine du dossier `auth/` (il est ignoré par Git) :

```ini
DB_SERVER=localhost
DB_NAME=nom_de_la_base
DB_LOGIN=utilisateur
DB_PASSWORD=mot_de_passe
JWT_SECRET=un_secret_long_et_aleatoire
```

### 3. VirtualHost Apache
Les routes sont gérées par le `.htaccess`, il faut donc autoriser son utilisation :

```apache
<VirtualHost *:80>
    ServerName auth.local
    DocumentRoot "C:/chemin/vers/auth"

    <Directory "C:/chemin/vers/auth">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Ajouter `127.0.0.1 auth.local` dans le fichier hosts, puis tester :

```bash
curl -X POST http://auth.local/auth/login -H "Content-Type: application/json" -d "{\"login\":\"coach\",\"password\":\"sport\"}"
```
