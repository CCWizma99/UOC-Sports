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
$router->get('/post/{id}', 'PostController@viewPost');
$router->post('/post/add-comment', 'PostController@addComment');
$router->get('/search-post', 'PostApiController@search');
$router->get('/get-faculties', 'UserHomeController@getFaculties');
$router->post('/create-facility-booking', 'FacilityApiController@createBooking');

$router->get('/student', 'StudentController@index');

$router->get('/captain//', 'CaptainController@index');
$router->get('/captain/mark-attendance', 'CaptainController@MarkAttendance');
$router->get('/captain/add-members', 'CaptainController@AddMembers');
$router->get('/captain/schedule-practice', 'CaptainController@SchedulePractice');
$router->get('/captain/communication', 'CaptainController@Communication');
$router->get('/captain/team-schedules', 'CoachController@TeamSchedules');

$router->get('/reserve-equipments/search', 'EquipmentApiController@minimalSearch');
$router->get('/reserve-equipments/get-times', 'EquipmentApiController@getTimes');
$router->post('/reserve-equipments/add', 'EquipmentApiController@addReservation');
$router->post('/reserve-equipments/cancel', 'EquipmentApiController@cancelReservation');
$router->get('/reserve-equipments/view', 'EquipmentApiController@getReservedItems');

$router->get('/equipment-manager//', 'EquipmentManagerController@index');
$router->get('/equipment-manager/equipment-report', 'EquipmentManagerController@equipmentReport');
$router->get('/equipment-manager/equipments', 'EquipmentManagerController@equipments');

$router->get('/sport-manager//', 'SportManagerController@index');
$router->get('/sport-manager/schedule', 'SportManagerController@schedule');
$router->get('/sport-manager/add-expenses', 'SportManagerController@addExpenses');
$router->get('/sport-manager/messages', 'SportManagerController@messages');
$router->get('/sport-manager/schedules', 'SportManagerController@schedules');

$router->get('/sign-up', 'AuthController@showSignupForm');
$router->get('/sign-in', 'AuthController@showSigninForm');
$router->get('/student-sign-up', 'AuthController@showStudentSignupForm');
$router->post('/sign-up', 'AuthController@handleSignup');
$router->post('/sign-in', 'AuthController@handleSignin');
$router->post('sign-up-student', 'AuthController@handleStudentSignup');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
