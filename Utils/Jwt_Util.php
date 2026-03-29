<?php

namespace R301\Utils\Jwt;

function generate_jwt(array $headers, array $payload, string $secret): string
{
    $headersEncoded = base64url_encode(json_encode($headers));
    $payloadEncoded = base64url_encode(json_encode($payload));

    $signature = hash_hmac('SHA256', "$headersEncoded.$payloadEncoded", $secret, true);
    $signatureEncoded = base64url_encode($signature);

    return "$headersEncoded.$payloadEncoded.$signatureEncoded";
}

function is_jwt_valid(string $jwt, string $secret): bool
{
    $tokenParts = explode('.', $jwt);
    if (count($tokenParts) !== 3) {
        return false;
    }

    $header = base64_decode($tokenParts[0]);
    $payload = base64_decode($tokenParts[1]);
    $signatureProvided = $tokenParts[2];

    $payloadObject = json_decode($payload);
    if (!isset($payloadObject->exp)) {
        return false;
    }

    $expiration = $payloadObject->exp;
    $isTokenExpired = ($expiration - time()) < 0;

    $base64UrlHeader = base64url_encode($header);
    $base64UrlPayload = base64url_encode($payload);
    $signature = hash_hmac('SHA256', $base64UrlHeader . '.' . $base64UrlPayload, $secret, true);
    $base64UrlSignature = base64url_encode($signature);

    $isSignatureValid = ($base64UrlSignature === $signatureProvided);

    return !$isTokenExpired && $isSignatureValid;
}

function base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function get_authorization_header(): ?string
{
    $headers = null;

    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }

    return $headers;
}

function get_bearer_token(): ?string
{
    $headers = get_authorization_header();

    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            if ($matches[1] === 'null') {
                return null;
            }
            return $matches[1];
        }
    }

    return null;
}

function verify_token_with_auth_api(string $token, string $verifyUrl): bool
{
    $payload = json_encode(['token' => $token]);
    if ($payload === false) {
        return false;
    }

    $curl = curl_init($verifyUrl);
    if ($curl === false) {
        return false;
    }

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

    $rawResponse = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($rawResponse === false || $statusCode !== 200) {
        return false;
    }

    $decoded = json_decode($rawResponse, true);
    if (!is_array($decoded)) {
        return true;
    }

    if (isset($decoded['valid'])) {
        return (bool) $decoded['valid'];
    }

    if (isset($decoded['isValid'])) {
        return (bool) $decoded['isValid'];
    }

    if (isset($decoded['success'])) {
        return (bool) $decoded['success'];
    }

    return true;
}
