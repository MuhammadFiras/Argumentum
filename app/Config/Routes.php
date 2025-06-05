<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route (halaman utama)
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->get('/login', 'AuthController::login');
$routes->post('auth/processLogin', 'AuthController::processLogin');
$routes->get('auth/register', 'AuthController::register');
$routes->post('auth/processRegister', 'AuthController::processRegister');
$routes->get('auth/login', 'AuthController::login'); 
$routes->get('/logout', 'AuthController::logout');

// Routes untuk Pertanyaan
$routes->get('/ask', 'QuestionController::ask', ['filter' => 'authGuard']);
$routes->post('/questions/create', 'QuestionController::create', ['filter' => 'authGuard']);
$routes->get('/question/(:segment)', 'QuestionController::view/$1');
$routes->get('/questions/edit/(:num)', 'QuestionController::edit/$1', ['filter' => 'authGuard']);    // GET untuk menampilkan form edit
$routes->post('/questions/update/(:num)', 'QuestionController::update/$1', ['filter' => 'authGuard']); // POST untuk memproses update
$routes->post('/questions/delete/(:num)', 'QuestionController::delete/$1', ['filter' => 'authGuard']); // <--- UBAH KE POST untuk delete

// Routes untuk Jawaban
$routes->post('/answer/submit/(:num)', 'AnswerController::submit/$1', ['filter' => 'authGuard']);
$routes->post('/answer/rate/(:num)', 'AnswerController::rateAnswer/$1', ['filter' => 'authGuard']);
$routes->post('/answer/toggle-best/(:num)', 'AnswerController::toggleBestAnswer/$1', ['filter' => 'authGuard']);

// Rute BARU untuk Edit dan Delete Jawaban
$routes->get('/answer/edit/(:num)', 'AnswerController::edit/$1', ['filter' => 'authGuard']);       // Menampilkan form edit jawaban
$routes->post('/answer/update/(:num)', 'AnswerController::update/$1', ['filter' => 'authGuard']);  // Memproses update jawaban
$routes->post('/answer/delete/(:num)', 'AnswerController::delete/$1', ['filter' => 'authGuard']);  // Memproses delete jawaban

// Routes untuk Profil Pengguna
$routes->get('/profile', 'ProfileController::view', ['filter' => 'authGuard']); // Profil user yang login
$routes->get('/profile/(:num)', 'ProfileController::view/$1'); // Profil user lain berdasarkan ID
$routes->get('/profile/edit', 'ProfileController::edit', ['filter' => 'authGuard']);    // Form edit profil
$routes->post('/profile/update', 'ProfileController::update', ['filter' => 'authGuard']); // Proses update profil

// $routes->setAutoRoute(true);