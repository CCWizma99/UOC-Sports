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

// Executive Dashboard Exports
$router->get('/executive-dashboard/export/csv', 'ExecutiveController@exportCsv');
$router->get('/executive-dashboard/export/pdf', 'ExecutiveController@exportPdf');

// Phase 3: Drill-down Analytics Pages
$router->get('/executive-dashboard/drill-down/sport-performance', 'ExecutiveController@sportPerformanceView');
$router->get('/executive-dashboard/drill-down/budget-trends', 'ExecutiveController@budgetTrendsView');
$router->get('/executive-dashboard/drill-down/utilization', 'ExecutiveController@utilizationTrendsView');

// Phase 3: Drill-down API Endpoints
$router->get('/api/drill-down/sport-performance', 'DashboardApiController@getSportPerformanceDetails');
$router->get('/api/drill-down/budget-trends', 'DashboardApiController@getBudgetTrendsByDateRange');
$router->get('/api/drill-down/utilization', 'DashboardApiController@getUtilizationTrends');

// Analytics for other modules (reusing existing API controllers)
$router->get('/executive-equipments/analytics', 'EquipmentApiController@getAnalytics');
$router->get('/executive-reservations/analytics', 'ReservationApiController@getAnalytics');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
