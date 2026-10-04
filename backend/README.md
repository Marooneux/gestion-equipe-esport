# Team Management API

## Presentation

Cette application est une API RESTful de gestion de joueurs.

Elle permet de gerer les joueurs, les commentaires, les rencontres, les feuilles de match et les statistiques.

## Auteurs

- Claudio CUMBANE
- Luka Wacker

## Ressources disponibles

Les principales ressources exposees sont:

- `GET|POST /joueurs`
- `GET|PUT|DELETE /joueurs/{id}`
- `GET|POST /joueurs/{id}/commentaires`
- `GET|PUT|DELETE /joueurs/{id}/commentaires/{commentaireId}`
- `GET|POST /rencontre`
- `GET|PUT|DELETE /rencontre/{id}`
- `GET|POST /feuilledematche`
- `GET|PUT|DELETE /feuilledematche/{id}`
- `GET /statistiques`
- `GET /statistiques/joueurs`
- `GET /statistiques/joueurs/{id}`

Acces aux ressources:

1. Envoyer des requetes HTTP (GET, POST, PUT, DELETE) sur les routes ci-dessus.
2. Ajouter un token JWT dans l'en-tete `Authorization: Bearer <token>` pour les routes protegees.

## Configuration VirtualHost locale

Exemple de VirtualHost Apache:

```apache
<VirtualHost *:80>
    ServerName team_management_api.local
    DocumentRoot "C:/chemin/vers/team_management_api.local"

    <Directory "C:/chemin/vers/team_management_api.local">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
</VirtualHost>
```

Ajouter ensuite une entree hosts locale vers `127.0.0.1` pour `team_management_api.local`.

## Acces a l'application et a la documentation

- API: `https://r401teammanagementapi.alwaysdata.net`
- Documentation Swagger UI: `https://r401teammanagementapi.alwaysdata.net/docs/`
- Specification OpenAPI: `https://r401teammanagementapi.alwaysdata.net/openapi.yaml`







## Dependances

- PHP
- Apache (VirtualHost local)
- MySQL
- JWT (authentification Bearer)
- OpenAPI + Swagger UI
