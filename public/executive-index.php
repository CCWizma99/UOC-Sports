<?php
session_start();

require_once '../config/config.php';
require_once '../core/autoload.php';
require_once '../core/Router.php';
require_once '../core/helpers.php';

$router = new Router();

// Executive Dashboard Pages
$router->get('/executive-dashboard', 'ExecutiveController@index');
$router->get('/executive-dashboard/analytics', 'DashboardApiController@getExecutiveSummary');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
