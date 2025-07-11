<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('api/login', 'auth::login');
$routes->get('api/protected', 'auth::protectedData');

// Rute untuk Mahasiswa API (dilindungi JWT)
$routes->get('api/mahasiswa', 'MahasiswaController::index'); // Mendapatkan semua data
$routes->get('api/mahasiswa/(:num)', 'MahasiswaController::show/$1'); // Mendapatkan data by ID
$routes->post('api/mahasiswa', 'MahasiswaController::create'); // Menambah data baru
$routes->put('api/mahasiswa/(:num)', 'MahasiswaController::update/$1'); // Memperbarui data by ID
$routes->delete('api/mahasiswa/(:num)', 'MahasiswaController::delete/$1'); // Menghapus data by ID

