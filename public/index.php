<?php
session_start();

require_once '../config/config.php';
require_once '../core/autoload.php';
require_once '../core/helpers.php';
require_once '../core/Router.php';

$router = new Router();

$router->get('/', 'UserHomeController@index');
$router->get('/captain/mark-attendance', 'CaptainController@MarkAttendance');
$router->get('/sign-up', 'AuthController@showSignupForm');
$router->post('/sign-up', 'AuthController@handleSignup');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
