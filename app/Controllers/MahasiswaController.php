<?php

namespace App\Controllers;

use App\Models\MahasiswaModel; // Panggil model mahasiswa
use CodeIgniter\API\ResponseTrait; // Untuk respons API

class MahasiswaController extends BaseController
{
    use ResponseTrait;

    protected $mahasiswaModel;

    public function __construct()
    {
        $this->mahasiswaModel = new MahasiswaModel();
    }

    // --- Endpoint GET: Menampilkan Semua Data Mahasiswa ---
    public function index()
    {
        // Ambil token dari header Authorization untuk verifikasi
        $token = $this->request->getHeaderLine('Authorization');
        if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            $token = $matches[1];
            $decodedToken = \decodeJWT($token); // Menggunakan helper kita

            if ($decodedToken) {
                $data = $this->mahasiswaModel->findAll(); // Ambil semua data mahasiswa
                if ($data) {
                    return $this->respond($data);
                } else {
                    return $this->failNotFound('Tidak ada data mahasiswa.');
                }
            } else {
                return $this->failUnauthorized('Invalid or expired token.');
            }
        } else {
            return $this->failUnauthorized('Authorization header not found or malformed.');
        }
    }

    // --- Endpoint GET by ID: Menampilkan Data Mahasiswa Berdasarkan ID ---
    public function show($id = null)
    {
        $token = $this->request->getHeaderLine('Authorization');
        if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            $token = $matches[1];
            $decodedToken = \decodeJWT($token);

            if ($decodedToken) {
                $data = $this->mahasiswaModel->find($id); // Ambil data berdasarkan ID
                if ($data) {
                    return $this->respond($data);
                } else {
                    return $this->failNotFound('Mahasiswa dengan ID ' . $id . ' tidak ditemukan.');
                }
            } else {
                return $this->failUnauthorized('Invalid or expired token.');
            }
        } else {
            return $this->failUnauthorized('Authorization header not found or malformed.');
        }
    }

    // --- Endpoint POST: Menambah Data Mahasiswa Baru ---
    public function create()
    {
        $token = $this->request->getHeaderLine('Authorization');
        if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            $token = $matches[1];
            $decodedToken = \decodeJWT($token);

            if ($decodedToken) {
                $rules = [
                    'nama'    => 'required|min_length[3]|max_length[255]',
                    'nim'     => 'required|is_unique[data_mahasiswa.nim]|min_length[5]|max_length[20]',
                    'jurusan' => 'required|min_length[3]|max_length[100]',
                    'email'   => 'valid_email|is_unique[data_mahasiswa.email]|permit_empty', // permit_empty jika tidak wajib
                ];

                if (!$this->validate($rules)) {
                    return $this->failValidationErrors($this->validator->getErrors());
                }

                $data = [
                    'nama'    => $this->request->getVar('nama'),
                    'nim'     => $this->request->getVar('nim'),
                    'jurusan' => $this->request->getVar('jurusan'),
                    'email'   => $this->request->getVar('email'),
                ];

                $this->mahasiswaModel->insert($data); // Masukkan data ke database
                return $this->respondCreated(['message' => 'Data mahasiswa berhasil ditambahkan.']);
            } else {
                return $this->failUnauthorized('Invalid or expired token.');
            }
        } else {
            return $this->failUnauthorized('Authorization header not found or malformed.');
        }
    }

    // --- Endpoint PUT: Memperbarui Data Mahasiswa ---
    public function update($id = null)
    {
        $token = $this->request->getHeaderLine('Authorization');
        if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            $token = $matches[1];
            $decodedToken = \decodeJWT($token);

            if ($decodedToken) {
                // Mencari mahasiswa yang akan diupdate
                $mahasiswa = $this->mahasiswaModel->find($id);

                if (!$mahasiswa) {
                    return $this->failNotFound('Mahasiswa dengan ID ' . $id . ' tidak ditemukan.');
                }

                // 1. Ambil body HTTP mentah sebagai string
                $rawBody = $this->request->getBody();

                log_message('debug', 'DEBUG UPDATE - Raw HTTP Body String: ' . $rawBody);

                // 2. Coba decode string tersebut secara manual menjadi array PHP
                $input = json_decode($rawBody, true);

                // 3. Tambahkan pemeriksaan jika json_decode gagal
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $jsonError = json_last_error_msg();
                    log_message('error', 'DEBUG UPDATE - json_decode failed: ' . $jsonError . ' for raw body: ' . $rawBody);
                    return $this->fail("Gagal memproses data JSON: " . $jsonError, 400);
                }

                // // --- START DEBUGGING SNIPPET (PASTIKAN ADA) ---
                // log_message('debug', 'DEBUG UPDATE - Raw Input Received: ' . json_encode($input));
                // log_message('debug', 'DEBUG UPDATE - Model Allowed Fields: ' . json_encode($this->mahasiswaModel->allowedFields));

                // // Tambahkan ini untuk melihat hasil array_intersect_key
                // $testDataToUpdate = array_intersect_key($input, array_flip($this->mahasiswaModel->allowedFields));
                // log_message('debug', 'DEBUG UPDATE - Data after intersect_key: ' . json_encode($testDataToUpdate));
                // // --- END DEBUGGING SNIPPET ---

                // Aturan validasi untuk update data
                // 'permit_empty' digunakan karena tidak semua field harus diupdate
                $rules = [
                    'nama'    => 'permit_empty|min_length[3]|max_length[255]',
                    'nim'     => 'permit_empty|min_length[5]|max_length[20]',
                    'jurusan' => 'permit_empty|min_length[3]|max_length[100]',
                    'email'   => 'valid_email|permit_empty',
                ];

                // Validasi unik untuk NIM dan Email saat update
                // Hanya periksa keunikan jika NIM/Email diubah dari nilai aslinya
                if (isset($input['nim']) && $input['nim'] !== $mahasiswa['nim']) {
                    $rules['nim'] = 'is_unique[data_mahasiswa.nim]|' . $rules['nim'];
                }
                if (isset($input['email']) && $input['email'] !== $mahasiswa['email']) {
                    $rules['email'] = 'is_unique[data_mahasiswa.email]|' . $rules['email'];
                }

                // Melakukan validasi pada input
                // Penting: Lewatkan $input sebagai parameter kedua ke validate()
                if (!$this->validate($rules, $input)) {
                    // Jika validasi gagal, kembalikan error validasi
                    log_message('error', 'DEBUG UPDATE - Validation Failed: ' . json_encode($this->validator->getErrors()));
                    return $this->failValidationErrors($this->validator->getErrors());
                }

                // Memfilter input agar hanya field yang diizinkan diupdate (sesuai $allowedFields di model)
                $dataToUpdate = array_intersect_key($input, array_flip($this->mahasiswaModel->allowedFields));

                log_message('debug', 'DEBUG UPDATE - Data after filtering allowedFields: ' . json_encode($dataToUpdate));

                // Memastikan ada data yang valid untuk diupdate setelah filtering
                if (empty($dataToUpdate)) {
                     // Ini adalah pesan error yang Anda lihat
                     log_message('error', 'DEBUG UPDATE - No valid fields to update after filtering by allowedFields.');
                     return $this->fail('Tidak ada field yang valid untuk diperbarui.', 400);
                }

                // Melakukan update data di database
                $this->mahasiswaModel->update($id, $dataToUpdate);

                // Mengembalikan respons sukses
                return $this->respondUpdated(['message' => 'Data mahasiswa berhasil diperbarui.']);
            } else {
                return $this->failUnauthorized('Token tidak valid atau kadaluarsa.');
            }
        } else {
            return $this->failUnauthorized('Header Authorization tidak ditemukan atau salah format.');
        }
    }

    // --- Endpoint DELETE: Menghapus Data Mahasiswa ---
    public function delete($id = null)
    {
        $token = $this->request->getHeaderLine('Authorization');
        if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            $token = $matches[1];
            $decodedToken = \decodeJWT($token);

            if ($decodedToken) {
                $mahasiswa = $this->mahasiswaModel->find($id);

                if ($mahasiswa) {
                    $this->mahasiswaModel->delete($id); // Hapus data
                    return $this->respondDeleted(['message' => 'Data mahasiswa berhasil dihapus.']);
                } else {
                    return $this->failNotFound('Mahasiswa dengan ID ' . $id . ' tidak ditemukan.');
                }
            } else {
                return $this->failUnauthorized('Invalid or expired token.');
            }
        } else {
            return $this->failUnauthorized('Authorization header not found or malformed.');
        }
    }
}