<?php

namespace R301\Utils\Http_response;

function send_json(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    header('Content-Type:application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');

    $jsonResponse = json_encode($payload);
    if ($jsonResponse === false) {
        http_response_code(500);
        echo '{"success":false,"message":"Erreur interne lors de la serialization JSON.","error":{"code":"JSON_ENCODE_ERROR"}}';
        return;
    }

    echo $jsonResponse;
}

function send_success(int $statusCode, string $message, mixed $data = [], ?array $meta = null): void
{
    $response = [
        'success' => true,
        'message' => $message,
        'data' => $data,
    ];

    if ($meta !== null) {
        $response['meta'] = $meta;
    }

    send_json($statusCode, $response);
}

function send_error(
    int $statusCode,
    string $message,
    string $errorCode,
    ?string $details = null,
    ?array $fields = null
): void {
    $response = [
        'success' => false,
        'message' => $message,
        'error' => [
            'code' => $errorCode,
        ],
    ];

    if ($details !== null) {
        $response['error']['details'] = $details;
    }

    if ($fields !== null && $fields !== []) {
        $response['error']['fields'] = $fields;
    }

    send_json($statusCode, $response);
}

function deliver_response($status_code, $status_message, $data = null): void
{
    if ((int)$status_code >= 400) {
        send_error(
            (int)$status_code,
            (string)$status_message,
            'LEGACY_ERROR',
            is_scalar($data) ? (string)$data : null,
            is_array($data) ? $data : null
        );
        return;
    }

    send_success((int)$status_code, (string)$status_message, $data ?? []);
}