<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('login', 'Auth::login');
$routes->group('api', ['filter' => 'jwt'], function($routes) {
    $routes->resource('pegawai');
});