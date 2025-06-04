<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route (halaman utama)
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->get('/login', 'Auth::login');
$routes->post('auth/processLogin', 'Auth::processLogin');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/processRegister', 'Auth::processRegister');
$routes->get('auth/login', 'Auth::login'); 
$routes->get('/logout', 'Auth::logout');

// Routes untuk Pertanyaan
$routes->get('/ask', 'Question::ask', ['filter' => 'authGuard']);
$routes->post('/questions/create', 'Question::create', ['filter' => 'authGuard']);
$routes->get('/question/(:segment)', 'Question::view/$1');
$routes->get('/questions/edit/(:num)', 'Question::edit/$1', ['filter' => 'authGuard']);    // GET untuk menampilkan form edit
$routes->post('/questions/update/(:num)', 'Question::update/$1', ['filter' => 'authGuard']); // POST untuk memproses update
$routes->post('/questions/delete/(:num)', 'Question::delete/$1', ['filter' => 'authGuard']); // <--- UBAH KE POST untuk delete

// Routes untuk Jawaban
$routes->post('/answer/submit/(:num)', 'Answer::submit/$1', ['filter' => 'authGuard']);
$routes->post('/answer/rate/(:num)', 'Answer::rateAnswer/$1', ['filter' => 'authGuard']);
$routes->post('/answer/toggle-best/(:num)', 'Answer::toggleBestAnswer/$1', ['filter' => 'authGuard']);

// Rute BARU untuk Edit dan Delete Jawaban
$routes->get('/answer/edit/(:num)', 'Answer::edit/$1', ['filter' => 'authGuard']);       // Menampilkan form edit jawaban
$routes->post('/answer/update/(:num)', 'Answer::update/$1', ['filter' => 'authGuard']);  // Memproses update jawaban
$routes->post('/answer/delete/(:num)', 'Answer::delete/$1', ['filter' => 'authGuard']);  // Memproses delete jawaban

// $routes->setAutoRoute(true);