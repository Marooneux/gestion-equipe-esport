<?php

define('API_URL', 'http://localhost:8080');

function api_get($endpoint) {
    $reponse = file_get_contents(API_URL . $endpoint);
    return json_decode($reponse, true);
}

function api_post($endpoint, $donnees) {
    $contexte = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($donnees)
        ]
    ]);
    $reponse = file_get_contents(API_URL . $endpoint, false, $contexte);
    return json_decode($reponse, true);
}

function api_put($endpoint, $donnees) {
    $contexte = stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($donnees)
        ]
    ]);
    $reponse = file_get_contents(API_URL . $endpoint, false, $contexte);
    return json_decode($reponse, true);
}

function api_delete($endpoint) {
    $contexte = stream_context_create([
        'http' => ['method' => 'DELETE']
    ]);
    $reponse = file_get_contents(API_URL . $endpoint, false, $contexte);
    return json_decode($reponse, true);
}
