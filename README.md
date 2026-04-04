# r401_team_management_auth

Service d'authentification JWT pour la gestion de l'équipe de sport dans le cadre du projet R4.01. Expose une API REST permettant d'obtenir et de vérifier des tokens JWT.

---

## Auteurs

| Nom             | Email                            |
| --------------- | -------------------------------- |
| Wacker Luka     | luka.wacker@etu.iut-tlse3.fr     |
| Cumbane Claudio | claudio.cumbane@etu.iut-tlse3.fr |

---

## Accès au service

### URL de base

```
https://r401auth.alwaysdata.net
```

---

## Documentation API

```
https://r401auth.alwaysdata.net/docs/
```

---

## Technologies utilisées

- **PHP** — logique serveur, génération et validation JWT
- **JWT (HS256)** — authentification sans état via token signé
- **MySQL / PDO** — vérification des credentials en base
- **Apache / mod_rewrite** — réécriture d'URL + protection des fichiers sensibles

---

## Structure du projet

```
r401_team_management_auth/
├── api/
│   ├── get_token.php       # Logique de l'endpoint /auth/login
│   └── verify_token.php    # Logique de l'endpoint /auth/verify
├── src/
│   ├── modele/
│   │   ├── DatabaseHandler.php  # Connexion PDO (singleton)
│   │   └── verifAuth.php        # Vérification des credentials en BD
│   └── utils/
│       └── jwt_utils.php        # Génération et validation JWT (HS256)
├── docs/
│   └── openapi.yaml        # Spécification OpenAPI 3.0.3
├── .env                    # Variables d'environnement (non versionné)
├── .htaccess               # Réécriture d'URL + protection fichiers sensibles
└── schema.sql              # Schéma de la base de données
```

---

## Installation locale

1. Cloner le dépôt dans le répertoire web (ex: `laragon/www/`)
2. Créer un fichier `.env` à la racine :

```ini
DB_SERVER=localhost
DB_NAME=nom_de_la_base
DB_LOGIN=utilisateur
DB_PASSWORD=motdepasse
JWT_SECRET=votre_secret
```

3. Importer `schema.sql` dans votre base de données
4. S'assurer que `mod_rewrite` est activé (Apache)

### Configuration Apache

#### Modules requis

```
php
php-mysql
rewrite
```

#### Virtual host

```apache
<VirtualHost *:80>
    ServerName ${serverName}
    DocumentRoot /var/www/${serverName}

    <Directory "/var/www/${serverName}">
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

    RewriteEngine On
    RewriteCond %{REQUEST_URI} !\.(css|jpg|jpeg|gif|ico|js)$
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ /index.php [QSA,L]
</VirtualHost>
```
