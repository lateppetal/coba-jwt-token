<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\Mahasiswa; // Panggil model mahasiswa

// kontrol autentikasi
class auth extends BaseController
{
    use ResponseTrait;

    public function login()
    {
        $input = $this->request->getJSON(true);

        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        $users = [
            'user1' => ['password' => 'pass123', 'role' => 'admin'],
            'user2' => ['password' => 'pass456', 'role' => 'user'],
        ];

        if (isset($users[$username]) && $users[$username]['password'] === $password) {
            $payload = [
                'user_id' => $username,
                'username' => $username,
                'role' => $users[$username]['role'],
            ];

            // UBAH BARIS INI:
            $token = encodeJWT($payload); // <-- Tambahkan backslash di sini
            return $this->respondCreated(['message' => 'Login successful', 'token' => $token]);
        } else {
            return $this->failUnauthorized('Invalid credentials');
        }
    }

    public function protectedData()
    {
        $token = $this->request->getHeaderLine('Authorization');

        if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            $token = $matches[1];
            // UBAH BARIS INI:
            $decodedToken = decodeJWT($token); // <-- Tambahkan backslash di sini

            if ($decodedToken) {
                return $this->respond([
                    'message' => 'You accessed protected data!',
                    'user_data' => $decodedToken
                ]);
            } else {
                return $this->failUnauthorized('Invalid or expired token.');
            }
        } else {
            return $this->failUnauthorized('Authorization header not found or malformed.');
        }
    }
}