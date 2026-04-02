<?php
session_start();

require_once '../config/config.php';

require_once '../core/autoload.php';

require_once '../core/Router.php';

require_once '../core/helpers.php';

// --- ADMIN AUTHENTICATION ENFORCEMENT ---
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $hashed = hash('sha256', $token);

    $stmt = $db->prepare("SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > ?");
    $stmt->execute([$hashed, time()]);
    $row = $stmt->fetch();

    if ($row) {
        $_SESSION['user_id'] = $row['user_id'];
        $stmtUser = $db->prepare("SELECT fname, type FROM users WHERE user_id = ?");
        $stmtUser->execute([$row['user_id']]);
        $user = $stmtUser->fetch();

        if ($user) {
            $_SESSION['user_name'] = $user['fname'];
            $_SESSION['user_type'] = $user['type'];
            $_SESSION['color'] = "green";
        }
    }
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ADMIN') {
    // Return 403 for API requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }
    // Redirect for normal requests
    header("Location: /uoc-sports/public/sign-in");
    exit;
}
// ----------------------------------------

$router = new Router();

$router->get('/admin-index', 'AdminHomeController@index');
$router->get('/admin-executive-dashboard', 'AdminHomeController@executiveDashboard');
$router->get('/admin-users', 'AdminHomeController@users');
$router->get('/admin-user-profile', 'AdminHomeController@userProfile');
$router->get('/admin-reservations', 'AdminHomeController@reservations');
$router->get('/admin-reservation', 'AdminHomeController@reservationDetails');
$router->get('/admin-reservation-analytics', 'AdminHomeController@reservationAnalytics');
$router->get('/admin-players', 'AdminHomeController@players');
$router->get('/admin-equipments', 'AdminHomeController@equipments');
$router->get('/admin-equipment-analytics', 'AdminHomeController@equipmentAnalytics');
$router->get('/admin-equipment-reports', 'AdminHomeController@equipmentReports');
$router->get('/admin-events', 'AdminHomeController@events');
$router->get('/admin-teams', 'AdminHomeController@teams');
$router->get('/admin-team-details', 'AdminHomeController@teamDetails');
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
$router->get('/admin-equipments/analytics', 'EquipmentApiController@getAnalytics');
$router->get('/admin-equipments/report/pdf', 'EquipmentReportController@generatePDF');
$router->get('/admin-equipments/report/inventory', 'EquipmentReportController@equipmentInventoryPDF');
$router->get('/admin-equipments/report/suppliers', 'EquipmentReportController@supplierDetailsPDF');
$router->get('/admin-equipments/report/snapshot', 'EquipmentReportController@allEquipmentSnapshotPDF');
$router->get('/admin-equipments/report/stock', 'EquipmentReportController@stockSnapshotPDF');
$router->get('/admin-equipments/report/period', 'EquipmentReportController@periodSnapshotPDF');
// Report Data (JSON) routes
$router->get('/admin-equipments/report-data/inventory', 'EquipmentApiController@reportInventory');
$router->get('/admin-equipments/report-data/suppliers', 'EquipmentApiController@reportSuppliers');
$router->get('/admin-equipments/report-data/snapshot', 'EquipmentApiController@reportSnapshot');
$router->get('/admin-equipments/report-data/stock', 'EquipmentApiController@reportStock');
$router->get('/admin-equipments/report-data/period', 'EquipmentApiController@reportPeriod');
$router->get('/admin-equipments/report-data/supplier-detail', 'EquipmentApiController@reportSupplierDetail');
$router->get('/admin-equipments/report/supplier-detail', 'EquipmentReportController@supplierDetailPDF');
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

// Equipment Management Form routes
$router->get('/admin-equipments/get-suppliers', 'EquipmentApiController@getSuppliers');
$router->get('/admin-equipments/get-stock-entries', 'EquipmentApiController@getStockEntries');
$router->get('/admin-equipments/get-users-by-type', 'EquipmentApiController@getUsersByType');
$router->post('/admin-equipments/add-grn', 'EquipmentApiController@addGoodReceivedNote');
$router->post('/admin-equipments/add-gin', 'EquipmentApiController@addGoodIssueNote');
$router->post('/admin-equipments/add-gcn', 'EquipmentApiController@addGoodCondemnNote');
$router->post('/admin-equipments/add-sport', 'EquipmentApiController@addSport');
$router->post('/admin-equipments/add-article', 'EquipmentApiController@addArticle');
$router->post('/admin-equipments/add-supplier', 'EquipmentApiController@addSupplier');
$router->post('/admin-budget/add-budget', 'BudgetApiController@addBudget');
$router->post('/admin-post/add-post', 'PostApiController@addPost');
$router->post('/admin-post/update', 'PostApiController@updatePost');

// User Registration Stats API
$router->get('/api/user/registration-stats', 'UserApiController@getRegistrationStats');

// User Management API
$router->post('/api/user/update', 'UserApiController@updateUser');
$router->post('/api/user/toggle-status', 'UserApiController@toggleStatus');

// Reservation Stats API
$router->get('/api/reservation/stats', 'ReservationApiController@getReservationStats');
$router->get('/admin-reservations/analytics', 'ReservationApiController@getAnalytics');

// Dashboard API
$router->get('/admin-dashboard/analytics', 'DashboardApiController@getExecutiveSummary');

// Tournament routes
$router->post('/admin-tournament/create', 'TournamentController@createTournament');
$router->post('/admin-tournament/send-invitation', 'TournamentController@sendInvitation');
$router->get('/admin-tournament/saved-recipients', 'TournamentController@getSavedRecipients');
$router->post('/admin-tournament/save-recipient', 'TournamentController@saveRecipient');
$router->get('/admin-tournament/list', 'TournamentController@getTournaments');
$router->post('/admin-tournament/add-result', 'TournamentController@addResult');
$router->post('/admin-tournament/add-match-result', 'TournamentController@addMatchResult');

// Match & Player Performance API routes
$router->get('/admin-sport/player-match-history', 'SportApiController@getPlayerMatchHistory');
$router->get('/admin-sport/search-matches', 'SportApiController@searchMatches');
$router->get('/admin-sport/match-details', 'SportApiController@getMatchDetails');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
