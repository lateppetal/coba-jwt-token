<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    public function login()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // Sementara hardcode login
        if ($username === 'admin' && $password === 'admin123') {
            $payload = [
                'username' => $username,
                'iat' => time(),
                'exp' => time() + 3600 // 1 jam
            ];
            $token = getJWT($payload);
            return $this->respond(['token' => $token]);
        }

        return $this->failUnauthorized('Login gagal');
    }
}
