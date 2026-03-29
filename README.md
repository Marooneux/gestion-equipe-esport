# API Team Management

## Documentation API

La specification OpenAPI est maintenue dans [openapi.yaml](openapi.yaml).

La documentation interactive Swagger UI est disponible dans [docs/index.html](docs/index.html).

## Serveur Apache2 (votre configuration)

Configuration VirtualHost utilisee:

```apache
<VirtualHost *:80>
		Define serverName team_management_api.local
		ServerName ${serverName}
		DocumentRoot /Users/claudio/code/project/${serverName}

		<Directory "/Users/claudio/code/project/${serverName}">
				Options Indexes FollowSymLinks
				AllowOverride None
				Require all granted
		</Directory>

		RewriteEngine On
		RewriteCond %{REQUEST_URI} !\.(css|jpg)$
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule ^ /index.php [QSA,L]
</VirtualHost>
```

URLs a utiliser avec ce VirtualHost:

- API: `http://team_management_api.local/`
- Swagger UI: `http://team_management_api.local/docs/`
- Specification OpenAPI: `http://team_management_api.local/openapi.yaml`

## Alternative sans Apache2

Vous pouvez aussi lancer le serveur PHP integre:

```bash
php -S localhost:8000
```

Puis ouvrir:

- API: `http://localhost:8000/index.php`
- Swagger UI: `http://localhost:8000/docs/`

## Authentification

Toutes les routes sont protegees par un token Bearer JWT.

Dans Swagger UI:

1. Cliquer sur "Authorize".
2. Saisir `Bearer <votre_token>`.
3. Appeler les endpoints.

## Routes principales

- `/joueurs`
- `/joueurs/{id}`
- `/joueurs/{id}/commentaires`
- `/joueurs/{id}/commentaires/{commentaireId}`
- `/rencontre`
- `/rencontre/{id}`
- `/feuilledematche`
- `/feuilledematche/{id}`
- `/statistiques`
- `/statistiques/joueurs`
- `/statistiques/joueurs/{id}`

## Format de reponse

Reponse de succes:

```json
{
	"success": true,
	"message": "...",
	"data": {},
	"meta": {}
}
```

Reponse d'erreur:

```json
{
	"success": false,
	"message": "...",
	"error": {
		"code": "...",
		"details": "..."
	}
}
```

## Regle de maintenance

Si une route change dans [index.php](index.php), mettre a jour [openapi.yaml](openapi.yaml) dans le meme commit.
