<?php

define('API_URL', 'http://localhost:8080');


// Requete pour obtenir un token de l'api d'authentification
function auth_get_token($login, $password) {
    $contexte = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode(['login' => $login, 'password' => $password])
        ]
    ]);
    $reponse = file_get_contents("https://r401auth.alwaysdata.net/auth/login", false, $contexte);
    return json_decode($reponse, true);
}


// Requete GET vers backend
function api_get($endpoint) {
    $token = $_SESSION['token'] ?? '';
    $contexte = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $token
        ]
    ]);
    $reponse = file_get_contents(API_URL . $endpoint, false, $contexte);
    return json_decode($reponse, true);
}

// Requete POST vers backend
function api_post($endpoint, $donnees) {
    $token = $_SESSION['token'] ?? '';
    $contexte = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer " . $token,
            'content' => json_encode($donnees)
        ]
    ]);
    $reponse = file_get_contents(API_URL . $endpoint, false, $contexte);
    return json_decode($reponse, true);
}

// Requete PUT vers backend
function api_put($endpoint, $donnees) {
    $token = $_SESSION['token'] ?? '';
    $contexte = stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer " . $token,
            'content' => json_encode($donnees)
        ]
    ]);
    $reponse = file_get_contents(API_URL . $endpoint, false, $contexte);
    return json_decode($reponse, true);
}

// Requete DELETE vers backend
function api_delete($endpoint) {
    $token = $_SESSION['token'] ?? '';
    $contexte = stream_context_create([
        'http' => [
            'method' => 'DELETE',
            'header' => 'Authorization: Bearer ' . $token
        ]
    ]);
    $reponse = file_get_contents(API_URL . $endpoint, false, $contexte);
    return json_decode($reponse, true);
}
