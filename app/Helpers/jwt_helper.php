<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWT($payload)
{
    $key = getenv('JWT_SECRET');
    $jwt = JWT::encode($payload, $key, 'HS256');
    return $jwt;
}

function decodeJWT($token)
{
    $key = getenv('JWT_SECRET');
    return JWT::decode($token, new Key($key, 'HS256'));
}
