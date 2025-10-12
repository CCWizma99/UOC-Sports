<?php
session_start();

require_once '../config/config.php';

require_once '../core/autoload.php';

require_once '../core/Router.php';

require_once '../core/helpers.php';

$router = new Router();

$router->get('/admin-index', 'AdminHomeController@index');
$router->get('/admin-users', 'AdminHomeController@users');
$router->get('/admin-reservations', 'AdminHomeController@reservations');
$router->get('/admin-players', 'AdminHomeController@players');
$router->get('/admin-equipments', 'AdminHomeController@equipments');
$router->get('/admin-events', 'AdminHomeController@events');
$router->get('/admin-teams', 'AdminHomeController@teams');
$router->get('/admin-budget', 'AdminHomeController@budget');
$router->get('/admin-news', 'AdminHomeController@news');
$router->get('/admin-inquiry', 'AdminHomeController@inquiry');

$router->get('/admin-teams/search-team', 'TeamApiController@search');
$router->get('/admin-equipments/search-equipment', 'EquipmentApiController@search');
$router->get('/admin-equipments/get-sports', 'EquipmentApiController@getSports');
$router->post('/admin-equipments/add', 'EquipmentApiController@add');
$router->get('/admin-budget/search-budget', 'BudgetApiController@search');
$router->post('/admin-budget/add-budget', 'BudgetApiController@addBudget');
$router->post('/admin-post/add-post', 'PostApiController@addPost');
$router->get('/admin-post/search', 'PostApiController@search');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
