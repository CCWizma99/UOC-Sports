<?php
session_start();

require_once '../config/config.php';
require_once '../core/autoload.php';
require_once '../core/helpers.php';
require_once '../core/Router.php';

$router = new Router();

$router->get('/', 'UserHomeController@index');
$router->get('/news', 'UserHomeController@news');
$router->get('/facility-reservation', 'UserHomeController@facilityReservation');
$router->get('/contact-us', 'UserHomeController@contactUs');
$router->get('/captain/mark-attendance', 'CaptainController@MarkAttendance');
$router->get('/equipment-manager//', 'EquipmentManagerController@index');
$router->get('/equipment-manager/equipment-report', 'EquipmentManagerController@equipmentReport');
$router->get('/sign-up', 'AuthController@showSignupForm');
$router->post('/sign-up', 'AuthController@handleSignup');
$router->get('/sport-manager//', 'SportManagerController@index');
$router->get('/sport-manager/events', 'SportManagerController@events');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
