# r401_team_management_frontend

Application web frontend de gestion d'équipe de sport dans le cadre du projet R4.01. Interface permettant de gérer les joueurs, les rencontres, les feuilles de match et les statistiques, en communiquant avec l'API backend via authentification JWT.

---

## Auteurs

| Nom             | Email                            |
| --------------- | -------------------------------- |
| Wacker Luka     | luka.wacker@etu.iut-tlse3.fr     |
| Cumbane Claudio | claudio.cumbane@etu.iut-tlse3.fr |

---

## Accès au site

### URL

[https://frontendr401.alwaysdata.net/login](https://frontendr401.alwaysdata.net/login)

### Identifiants de connexion

| Compte | Login | Mot de passe |
| ------ | ----- | ------------ |
| Coach  | coach | sport        |

---

## Documentation API

### API principale — Gestion de l'équipe

```
https://r401teammanagementapi.alwaysdata.net/docs/
```

Cette API expose les ressources suivantes :

- **Joueurs** — création, lecture, modification, suppression
- **Rencontres** — gestion des matchs
- **Participations** — lien joueurs/rencontres
- **Commentaires** — évaluations des joueurs

### API secondaire — Authentification JWT

```
https://r401auth.alwaysdata.net/docs/
```

Cette API gère l'authentification des utilisateurs :

- **Login** — vérification des identifiants, génération d'un token JWT
- **Vérification du token** — validation et décodage du JWT pour les requêtes protégées
- **Logout** — invalidation du token

---

## Technologies utilisées

- **PHP** — logique serveur, routage, contrôleurs
- **HTML / CSS** — interface utilisateur
- **JWT** — authentification via token (service d'auth dédié)
- **MySQL / PDO** — persistance des données
- **Apache / mod_rewrite** — réécriture d'URL

---

## Structure du projet

```
r401_team_management_frontend/
├── Controleur/
│   ├── ApiClient.php              # Client HTTP vers l'API backend
│   ├── JoueurControleur.php       # Gestion des joueurs
│   ├── RencontreControleur.php    # Gestion des rencontres
│   ├── ParticipationControleur.php
│   ├── CommentaireControleur.php
│   ├── StatistiquesControleur.php
│   └── UtilisateurControleur.php  # Authentification / session
├── Vue/
│   ├── login.php
│   ├── tableauDeBord.php
│   ├── joueur.php
│   ├── rencontre.php
│   ├── joueur/                    # Vues CRUD joueurs
│   ├── rencontre/                 # Vues CRUD rencontres
│   ├── feuilleDeMatch/            # Feuille de match et évaluations
│   └── Component/                 # Composants réutilisables (formulaires, selects)
├── index.php                      # Point d'entrée, routage, session
├── stylesheet.css
├── .htaccess                      # Réécriture d'URL
└── schema.sql                     # Schéma de la base de données
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
API_URL=https://r401teammanagementapi.alwaysdata.net
AUTH_URL=https://r401auth.alwaysdata.net
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
