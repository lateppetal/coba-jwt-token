<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class JWTFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            return \Config\Services::response()->setJSON(['message' => 'Token tidak ditemukan'])->setStatusCode(401);
        }

        $token = explode(' ', $authHeader)[1] ?? '';
        try {
            $decoded = decodeJWT($token);
            return;
        } catch (\Exception $e) {
            return \Config\Services::response()->setJSON(['message' => 'Token tidak valid'])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
