<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route (halaman utama)
$routes->get('/', 'Home::index'); 

// Authentication Routes
$routes->get('/login', 'Auth::login');
$routes->post('auth/processLogin', 'Auth::processLogin'); // Menggunakan POST untuk processLogin
$routes->get('/register', 'Auth::register');
$routes->post('auth/processRegister', 'Auth::processRegister');
$routes->get('/logout', 'Auth::logout');

// Routes untuk Pertanyaan
$routes->get('/ask', 'Question::ask', ['filter' => 'authGuard']); // Halaman untuk bertanya, perlu login
$routes->post('/questions/create', 'Question::create', ['filter' => 'authGuard']); // Proses submit pertanyaan
$routes->get('/question/(:segment)', 'Question::view/$1'); // Menampilkan detail pertanyaan berdasarkan slug
$routes->get('/questions/edit/(:num)', 'Question::edit/$1', ['filter' => 'authGuard']); // Edit pertanyaan
$routes->post('/questions/update/(:num)', 'Question::update/$1', ['filter' => 'authGuard']);
$routes->get('/questions/delete/(:num)', 'Question::delete/$1', ['filter' => 'authGuard']); // Atau POST untuk delete

// Routes untuk Jawaban
$routes->post('/answer/submit/(:num)', 'Answer::submit/$1', ['filter' => 'authGuard']); // Submit jawaban untuk question ID :num

// Pastikan route default CodeIgniter (jika diperlukan untuk auto-routing) ada di akhir jika autoRoute = true
// $routes->setAutoRoute(true); // Jika kamu mau auto routing berdasarkan controller/method
// Sebaiknya definisikan semua route secara eksplisit untuk keamanan dan kejelasan.