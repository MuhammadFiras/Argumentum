<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route (halaman utama)
$routes->get('/', 'Home::index');

//Routes Pertanyaan Saya & Jawaban Saya
$routes->get('/my-questions', 'Home::myQuestions');
$routes->get('/my-answers', 'Home::myAnswers');

// Routes buat login dan register
$routes->get('/login', 'AuthController::login');
$routes->post('/auth/processLogin', 'AuthController::processLogin');
$routes->get('/auth/register', 'AuthController::register');
$routes->post('/auth/processRegister', 'AuthController::processRegister');
$routes->get('/auth/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// Routes buat CRUD Pertanyaan
$routes->get('/ask', 'QuestionController::ask', ['filter' => 'authGuard']);
$routes->post('/questions/create', 'QuestionController::create', ['filter' => 'authGuard']);
$routes->get('/question/(:segment)', 'QuestionController::view/$1');
$routes->get('/questions/edit/(:num)', 'QuestionController::edit/$1', ['filter' => 'authGuard']);
$routes->post('/questions/update/(:num)', 'QuestionController::update/$1', ['filter' => 'authGuard']);
$routes->post('/questions/delete/(:num)', 'QuestionController::delete/$1', ['filter' => 'authGuard']);

// Routes buat karakter Jawaban
$routes->post('/answer/submit/(:num)', 'AnswerController::submit/$1', ['filter' => 'authGuard']);
$routes->post('/answer/rate/(:num)', 'AnswerController::rateAnswer/$1', ['filter' => 'authGuard']);
$routes->post('/answer/delete-rating/(:num)', 'AnswerController::deleteRating/$1', ['filter' => 'authGuard']);
$routes->post('/answer/toggle-best/(:num)', 'AnswerController::toggleBestAnswer/$1', ['filter' => 'authGuard']);

// Routes buat CRUD komentar
$routes->post('/comment/create/(:num)', 'CommentController::create/$1', ['filter' => 'authGuard']);
$routes->post('/comment/update/(:num)', 'CommentController::update/$1', ['filter' => 'authGuard']);
$routes->post('/comment/delete/(:num)', 'CommentController::delete/$1', ['filter' => 'authGuard']);
$routes->get('/answer/edit/(:num)', 'AnswerController::edit/$1', ['filter' => 'authGuard']);
$routes->post('/answer/update/(:num)', 'AnswerController::update/$1', ['filter' => 'authGuard']);
$routes->post('/answer/delete/(:num)', 'AnswerController::delete/$1', ['filter' => 'authGuard']);

// Routes CRUD Profil Pengguna
$routes->get('/profile', 'ProfileController::view', ['filter' => 'authGuard']);
$routes->get('/profile/(:num)', 'ProfileController::view/$1');
$routes->get('/profile/edit', 'ProfileController::edit', ['filter' => 'authGuard']);
$routes->post('/profile/update', 'ProfileController::update', ['filter' => 'authGuard']);
