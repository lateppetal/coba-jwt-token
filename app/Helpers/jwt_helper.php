<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWTKey()
{
    return getenv('JWT_SECRET_KEY');
}

function encodeJWT($payload)
{
    $key = getJWTKey();
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // token berlaku 1 jam atau 3600 detik

    $payload = array_merge($payload, [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
    ]);

    return JWT::encode($payload, $key, 'HS256');
}

function decodeJWT($token)
{
    $key = getJWTKey();
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return (array) $decoded;
    } catch (\Exception $e) {
        // Handle invalid token, expired token, etc.
        return null;
    }
}