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
$router->get('/admin-reservation', 'AdminHomeController@reservationDetails');
$router->get('/admin-players', 'AdminHomeController@players');
$router->get('/admin-equipments', 'AdminHomeController@equipments');
$router->get('/admin-events', 'AdminHomeController@events');
$router->get('/admin-teams', 'AdminHomeController@teams');
$router->get('/admin-budget', 'AdminHomeController@budget');
$router->get('/admin-news', 'AdminHomeController@news');
$router->get('/admin-inquiry', 'AdminHomeController@inquiry');
$router->get('/admin-inquiry/search', 'InquiryController@search');
$router->get('/admin-inquiry/all', 'InquiryController@getAll');
$router->get('/admin-inquiry/details/{id}', 'InquiryController@getDetails');
$router->post('/admin-inquiry/update-status', 'InquiryController@updateStatus');
$router->post('/admin-inquiry/delete', 'InquiryController@delete');
$router->get('/admin-teams/search-team', 'TeamApiController@search');
$router->post('/admin-teams/remove-member', 'TeamApiController@removeMember');
$router->get('/admin-equipments/search-equipment', 'EquipmentApiController@searchEquipment');
$router->get('/admin-equipments/get-equipments', 'EquipmentApiController@getEquipments');
$router->get('/admin-equipments/get-sports', 'EquipmentApiController@getSports');
$router->get('/admin-budget/search-budget', 'BudgetApiController@search');
$router->get('/admin-post/search', 'PostApiController@search');
$router->get('/admin-sport/get-sport-fields', 'SportApiController@getSportFields');
$router->get('/admin-sport/get-students', 'SportApiController@getStudents');
$router->get('/admin-sport/get-sports', 'SportApiController@getSports');
$router->get('/admin-sport/get-tournaments', 'SportApiController@getTournaments');


$router->post('/admin-users/add-internal-user', 'AuthController@addUser');
$router->post('/admin-equipments/add', 'EquipmentApiController@add');
$router->post('/admin-equipments/add-equipment-type', 'EquipmentApiController@addEquipmentType');
$router->post('/admin-equipments/add-stock', 'EquipmentApiController@addStock');
$router->post('/admin-budget/add-budget', 'BudgetApiController@addBudget');
$router->post('/admin-post/add-post', 'PostApiController@addPost');

// Tournament routes
$router->post('/admin-tournament/create', 'TournamentController@createTournament');
$router->post('/admin-tournament/send-invitation', 'TournamentController@sendInvitation');
$router->get('/admin-tournament/saved-recipients', 'TournamentController@getSavedRecipients');
$router->post('/admin-tournament/save-recipient', 'TournamentController@saveRecipient');
$router->get('/admin-tournament/list', 'TournamentController@getTournaments');


$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
