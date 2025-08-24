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
$router->get('/captain//', 'CaptainController@index');
$router->get('/captain/mark-attendance', 'CaptainController@MarkAttendance');
$router->get('/captain/add-members', 'CaptainController@AddMembers');
$router->get('/captain/schedule-practice', 'CaptainController@SchedulePractice');
$router->get('/captain/schedule-practice', 'CaptainController@Communication');
$router->get('/captain/schedule-practice', 'CoachController@TeamSchedules');
$router->get('/equipment-manager//', 'EquipmentManagerController@index');
$router->get('/sign-up', 'AuthController@showSignupForm');
$router->post('/sign-up', 'AuthController@handleSignup');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
