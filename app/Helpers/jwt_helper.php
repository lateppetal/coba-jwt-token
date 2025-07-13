<?php
<<<<<<< HEAD
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWT($payload)
{
    $key = getenv('JWT_SECRET');
    $jwt = JWT::encode($payload, $key, 'HS256');
    return $jwt;
=======

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
>>>>>>> 3c9707526f6e37fc5130ad8f2fb6009dc93cc25c
}

function decodeJWT($token)
{
<<<<<<< HEAD
    $key = getenv('JWT_SECRET');
    return JWT::decode($token, new Key($key, 'HS256'));
}
=======
    $key = getJWTKey();
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return (array) $decoded;
    } catch (\Exception $e) {
        // Handle invalid token, expired token, etc.
        return null;
    }
}
>>>>>>> 3c9707526f6e37fc5130ad8f2fb6009dc93cc25c
