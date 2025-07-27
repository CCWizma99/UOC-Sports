<?php
session_start();

// Load config
require_once '../config/config.php';

// Autoload classes
require_once '../core/autoload.php';

// Load router
require_once '../core/Router.php';

// Load view helper
require_once '../core/helpers.php';

// Setup router
$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/about', 'HomeController@about');
$router->post('/submit', 'HomeController@submit');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
