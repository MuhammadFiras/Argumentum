<?php

use App\Controllers\ProfileController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route (halaman utama)
$routes->get('/', 'Home::index');

// Routes buat login dan register
$routes->get('/login', 'AuthController::login');
$routes->post('/auth/processLogin', 'AuthController::processLogin');
$routes->get('/register', 'AuthController::register');
$routes->post('/auth/processRegister', 'AuthController::processRegister');
$routes->get('/logout', 'AuthController::logout');

//Route admin
$routes->get('/admin/dashboard', 'AdminController::index', ['filter' => 'adminGuard']);
$routes->get('/admin/tables/users', 'AdminController::usersTable', ['filter' => 'adminGuard']);
$routes->get('/admin/tables/questions', 'AdminController::questionsTable', ['filter' => 'adminGuard']);
$routes->get('/admin/tables/answers', 'AdminController::answersTable', ['filter' => 'adminGuard']);
$routes->get('/admin/tables/answer-comments', 'AdminController::answerCommentsTable', ['filter' => 'adminGuard']);
$routes->get('/admin/tables/topics', 'AdminController::topicsTable', ['filter' => 'adminGuard']);
$routes->get('/admin/tables/answer-ratings', 'AdminController::answerRatingsTable', ['filter' => 'adminGuard']);
$routes->get('/admin/tables/question-topics', 'AdminController::questionTopicsTable', ['filter' => 'adminGuard']);
$routes->get('/admin/form/add-topics', 'TopicController::addForm', ['filter' => 'adminGuard']);
$routes->get('/admin/form/edit-topics/(:num)', 'TopicController::editForm/$1', ['filter' => 'adminGuard']);
$routes->post('/admin/form/topics-insert', 'TopicController::insert', ['filter' => 'adminGuard']);
$routes->post('/admin/form/topics-update/(:num)', 'TopicController::update/$1', ['filter' => 'adminGuard']);
$routes->delete('/admin/form/topics-delete/(:num)', 'TopicController::delete/$1', ['filter' => 'adminGuard']);

//Routes Pertanyaan Saya & Jawaban Saya
$routes->get('/my-questions', 'Home::myQuestions', ['filter' => 'authGuard']);
$routes->get('/my-answers', 'Home::myAnswers', ['filter' => 'authGuard']);

// Routes buat CRUD Pertanyaan
$routes->get('/ask', 'QuestionController::ask', ['filter' => 'authGuard']);
$routes->post('/questions/create', 'QuestionController::create', ['filter' => 'authGuard']);
$routes->get('/question/(:segment)', 'QuestionController::view/$1', ['filter' => 'authGuard']);
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
$routes->get('/profile/(:num)', 'ProfileController::view/$1', ['filter' => 'authGuard']);
$routes->get('/profile/edit', 'ProfileController::edit', ['filter' => 'authGuard']);
$routes->post('/profile/update', 'ProfileController::update', ['filter' => 'authGuard']);
$routes->delete('/profile/delete/(:num)', 'ProfileController::delete/$1', ['filter' => 'adminGuard']);
