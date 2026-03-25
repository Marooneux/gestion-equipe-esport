# r401_team_management_auth

Service d'authentification JWT pour la gestion de l'équipe de sport dans le cadre du projet en R401. Expose une API REST permettant d'obtenir et de vérifier des tokens JWT.

## URL de base

```
https://r401auth.alwaysdata.net
```

## Endpoints

### POST /auth/login

Authentifie un utilisateur et retourne un token JWT valable **1 heure**.

**Corps de la requête (JSON) :**
```json
{
  "login": "nom_utilisateur",
  "password": "mot_de_passe"
}
```

**Réponse succès (200) :**
```json
{
  "status_code": 200,
  "status_message": "Le token a été créer",
  "data": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Réponse erreur (403) :**
```json
{
  "status_code": 403,
  "status_message": "L'utilisateur et/ou le mot de passe sont incorrectes",
  "data": null
}
```

---

### POST /auth/verify

Vérifie la validité d'un token JWT (signature + expiration).

**Corps de la requête (JSON) :**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Réponse succès (200) :**
```json
{
  "status_code": 200,
  "status_message": "Le token est valide",
  "data": true
}
```

**Réponse erreur (400) :**
```json
{
  "status_code": 400,
  "status_message": "Le token n'est pas valide",
  "data": null
}
```

---

## Exemples de requêtes

### cURL

```bash
# Obtenir un token
curl -X POST https://r401auth.alwaysdata.net/auth/login \
  -H "Content-Type: application/json" \
  -d '{"login": "admin", "password": "monmotdepasse"}'

# Vérifier un token
curl -X POST https://r401auth.alwaysdata.net/auth/verify \
  -H "Content-Type: application/json" \
  -d '{"token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."}'
```

### JavaScript (fetch)

```js
// Obtenir un token
const res = await fetch('https://r401auth.alwaysdata.net/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ login: 'admin', password: 'monmotdepasse' })
});
const { data: token } = await res.json();

// Vérifier un token
const res = await fetch('https://r401auth.alwaysdata.net/auth/verify', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ token })
});
const result = await res.json();
```

### PHP (cURL)

```php
// Obtenir un token
$ch = curl_init('https://r401auth.alwaysdata.net/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['login' => 'admin', 'password' => 'monmotdepasse']));
$response = json_decode(curl_exec($ch));
$token = $response->data;
```

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
├── .env                    # Variables d'environnement (non versionné)
├── .htaccess               # Réécriture d'URL + protection fichiers sensibles
└── schema.sql              # Schéma de la base de données
```

## Payload JWT

Le token généré contient les informations suivantes :

| Champ | Description |
|-------|-------------|
| `role` | Rôle de l'utilisateur en base |
| `user_id` | ID de l'utilisateur en base |
| `exp` | Timestamp d'expiration (1h après émission) |

## Installation locale

1. Cloner le dépôt et le placer dans le répertoire web (ex: `laragon/www/`)
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
