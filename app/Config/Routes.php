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
$routes->get('/questions/edit/(:num)', 'Question::edit/$1', ['filter' => 'authGuard']);
$routes->post('/questions/update/(:num)', 'Question::update/$1', ['filter' => 'authGuard']);
$routes->get('/questions/delete/(:num)', 'Question::delete/$1', ['filter' => 'authGuard']);

// Routes untuk Jawaban
$routes->post('/answer/submit/(:num)', 'Answer::submit/$1', ['filter' => 'authGuard']);
$routes->post('/answer/rate/(:num)', 'Answer::rateAnswer/$1', ['filter' => 'authGuard']); // <--- TAMBAHKAN RUTE INI

// $routes->setAutoRoute(true);