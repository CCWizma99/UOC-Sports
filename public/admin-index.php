<?php
session_start();

require_once '../config/config.php';

require_once '../core/autoload.php';

require_once '../core/Router.php';

require_once '../core/helpers.php';

$router = new Router();

$router->get('/admin-index', 'HomeController@index');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
