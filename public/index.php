<?php
session_start();

require_once '../config/config.php';
require_once '../core/autoload.php';
require_once '../core/helpers.php';
require_once '../core/Router.php';

$router = new Router();

$router->get('/', 'UserHomeController@index');
$router->get('/', 'UserHomeController@index');
$router->get('/news', 'UserHomeController@news');
$router->get('/facility-reservation', 'UserHomeController@facilityReservation');
$router->get('/contact-us', 'UserHomeController@contactUs');
$router->get('/profile', 'UserHomeController@profile');
$router->get('/post/{id}', 'PostController@viewPost');
$router->post('/post/add-comment', 'PostController@addComment');
$router->get('/search-post', 'PostApiController@search');
$router->get('/get-faculties', 'UserHomeController@getFaculties');
$router->get('/reserve-facilities/view-reservations', 'FacilityApiController@viewMyReservations');
$router->get('/get-reserved-slots', 'FacilityApiController@getReservedSlots');
$router->get('/reserve-facilities/chart', 'FacilityApiController@getReservationChart');
$router->post('/create-facility-booking', 'FacilityApiController@createBooking');
$router->get('/payment', 'UserHomeController@payment');
$router->get('/payment/success', 'PaymentController@success');
$router->get('/payment/cancel', 'PaymentController@cancel');
$router->post('/payment/notify', 'PaymentController@notify');

// User Registration Stats API
$router->get('/api/user/registration-stats', 'UserApiController@getRegistrationStats');

// Reservation Stats API
$router->get('/api/reservation/stats', 'ReservationApiController@getReservationStats');

$router->get('/student', 'StudentController@index');
$router->get('/student/available-sports', 'StudentController@getAvailableSports');
$router->get('/student/enrolled-sports', 'StudentController@getEnrolledSports');
$router->post('/student/enroll-sport', 'StudentController@enrollSport');
$router->post('/student/unenroll-sport', 'StudentController@unenrollSport');

$router->get('/captain//', 'CaptainController@index');
$router->get('/captain/mark-attendance', 'CaptainController@MarkAttendance');
$router->get('/captain/add-members', 'CaptainController@AddMembers');
$router->get('/captain/schedule-practice', 'CaptainController@SchedulePractice');
$router->post('/captain/schedule-practice', 'CaptainController@SchedulePractice');
$router->get('/captain/communication', 'CaptainController@Communication');
$router->get('/captain/team-schedules', 'CoachController@TeamSchedules');

// Attendance API routes
$router->post('/api/attendance/save', 'AttendanceApiController@saveAttendance');
$router->get('/api/attendance/session/{id}', 'AttendanceApiController@getAttendanceBySession');
$router->get('/api/attendance/team-members/{sport_id}', 'AttendanceApiController@getTeamMembersWithPercentages');
$router->get('/api/attendance/history/{sport_id}', 'AttendanceApiController@getAttendanceHistory');
$router->get('/api/attendance/last-session/{sport_id}', 'AttendanceApiController@getLastSessionAttendance');
$router->get('/api/attendance/upcoming-sessions/{sport_id}', 'AttendanceApiController@getUpcomingSessions');
$router->get('/api/attendance/exists/{practice_id}', 'AttendanceApiController@checkAttendanceExists');



// Captain Message API routes
$router->get('/api/captain/message/recipients', 'MessageApiController@getRecipients');
$router->post('/api/captain/message/send', 'MessageApiController@sendMessage');
$router->get('/api/captain/message/list', 'MessageApiController@getMessages');
$router->post('/api/captain/message/delete', 'MessageApiController@deleteMessage');

// Coach/Manager Inbox API routes
$router->get('/api/inbox/messages', 'MessageApiController@getInbox');
$router->post('/api/inbox/mark-read', 'MessageApiController@markRead');

// Coach Message API routes
$router->get('/api/coach/message/recipients', 'MessageApiController@getCoachRecipients');
$router->post('/api/coach/message/send', 'MessageApiController@sendCoachMessage');
$router->get('/api/coach/message/list', 'MessageApiController@getMessages');

$router->get('/coach//', 'CoachController@TeamSchedules');
$router->get('/coach/coach-communicate', 'CoachController@CoachCommunicate');
$router->get('/coach/report-injury', 'CoachController@ReportInjury');

$router->get('/registrar//', 'RegistrarController@RegistrarDashboard');
$router->get('/registrar/verify-students', 'RegistrarController@VerifyStudents');
$router->get('/registrar/verify-staff', 'RegistrarController@VerifyStaff');
$router->get('/registrar/verify-bookings', 'RegistrarController@VerifyBookings');

// Registrar Verification API
$router->get('/api/registrar/student-details', 'RegistrarController@getStudentDetails');
$router->post('/api/registrar/verify-student', 'RegistrarController@verifyStudent');
$router->get('/api/registrar/pending-count', 'RegistrarController@getPendingCount');

// Sport Manager Verification Request API
$router->post('/api/verification/create-request', 'VerificationApiController@createRequest');
$router->get('/api/verification/unverified-students', 'VerificationApiController@getUnverifiedStudents');
$router->get('/api/verification/my-requests', 'VerificationApiController@getMyRequests');

$router->get('/reserve-equipments/search', 'EquipmentApiController@minimalSearch');
$router->get('/reserve-equipments/get-times', 'EquipmentApiController@getTimes');
$router->post('/reserve-equipments/add', 'EquipmentApiController@addReservation');
$router->post('/reserve-equipments/cancel', 'EquipmentApiController@cancelReservation');
$router->get('/reserve-equipments/view', 'EquipmentApiController@getReservedItems');
$router->get('/reserve-equipments/add-lostitem', 'EquipmentApiController@addLostItem');
$router->get('/equipment-manager//', 'EquipmentManagerController@index');
$router->get('/equipment-manager/equipment-reservations', 'EquipmentManagerController@equipmentReport');
$router->get('/equipment-manager/equipments', 'EquipmentManagerController@equipments');
$router->get('/equipment-manager/practiceschedule', 'EquipmentManagerController@practiceschedule');
$router->get('/equipment-manager/lostitem', 'LostItemController@index');
$router->get('/equipment-manager/add-lostitem', 'LostItemController@create');
$router->post('/equipment-manager/add-lostitem', 'LostItemController@store');
$router->post('/equipment-manager/update-lostitem-status', 'LostItemController@updateStatus');
$router->post('/equipment-manager/delete-lostitem', 'LostItemController@delete');
$router->get('/equipment-manager/add-booking', 'EquipmentManagerController@addBooking');
$router->post('/equipment-manager/save-booking', 'EquipmentManagerController@saveBooking');
$router->post('/post/delete-comment', 'PostController@deleteComment');
$router->get('/equipment-manager/bookingrequests', 'EquipmentBookingRequestController@index');
$router->get('/equipment-manager/booking-request-details', 'EquipmentBookingRequestController@getDetails');
$router->post('/equipment-manager/create-booking-request', 'EquipmentBookingRequestController@create');
$router->post('/equipment-manager/update-booking-request', 'EquipmentBookingRequestController@update');
$router->post('/equipment-manager/update-booking-status', 'EquipmentBookingRequestController@updateStatus');
$router->post('/equipment-manager/delete-booking-request', 'EquipmentBookingRequestController@delete');
$router->post('/equipment-manager/approve-booking', 'EquipmentBookingRequestController@approve');
$router->post('/equipment-manager/reject-booking', 'EquipmentBookingRequestController@reject');
$router->post('/equipment-manager/complete-booking', 'EquipmentBookingRequestController@complete');
$router->get('/equipment-manager/manage-equipment', 'EquipmentManagerController@manageEquipment');
$router->post('/equipment-manager/update-equipment', 'SportEquipmentController@update');
$router->post('/equipment-manager/delete-equipment', 'SportEquipmentController@delete');
$router->get('/equipment-manager/equipment-details', 'SportEquipmentController@getDetails');

// Student equipment requests
$router->get('/student/equipment-requests', 'EquipmentBookingRequestController@myRequests');

$router->get('/sport-manager//', 'SportManagerController@index');
$router->get('/sport-manager/schedule', 'SportManagerController@schedule');
$router->get('/sport-manager/expenses', 'SportManagerController@expenses');
$router->post('/sport-manager/add-expenses', 'BudgetController@addTransaction');
$router->get('/sport-manager/messages', 'SportManagerController@messages');
$router->get('/sport-manager/schedules', 'SportManagerController@schedules');
$router->get('/sport-manager/budget/remaining', 'BudgetController@remaining');
$router->get('/sport-manager/update-transaction', 'BudgetController@updateTransaction');
$router->post('/sport-manager/update-transaction', 'BudgetController@handleUpdateTransaction');
$router->get('/sport-manager/remaining-budget', 'BudgetController@remainingBudget');
$router->get('/sport-manager/practicesessions', 'SportPracticeSessionController@index');
$router->get('/sport-manager/competitions', 'SportCompetitionsController@index');
$router->get('/sport-manager/add-practice', 'SportPracticeSessionController@create');
$router->post('/sport-manager/store-practice', 'SportPracticeSessionController@store');
$router->get('/sport-manager/edit-practice', 'SportPracticeSessionController@edit');
$router->post('/sport-manager/update-practice', 'SportPracticeSessionController@update');
$router->post('/sport-manager/update-practice-status', 'SportPracticeSessionController@updateStatus');
$router->post('/sport-manager/delete-practice', 'SportPracticeSessionController@delete');
$router->get('/sport-manager/add-participants', 'SportCompetitionsController@create');
$router->post('/sport-manager/store-competition', 'SportCompetitionsController@store');
$router->post('/sport-manager/delete-competition', 'SportCompetitionsController@delete');
$router->get('/sport-manager/add-expense', 'SportManagerController@addExpense');
$router->post('/sport-manager/add-expense', 'SportExpensesController@store');
$router->get('/sport-manager/team', 'SportManagerController@team');



$router->get('/api/injury/past-sessions', 'InjuryApiController@getPastSessions');
$router->get('/api/injury/reports/{id}', 'InjuryApiController@getReports');
$router->post('/api/injury/report', 'InjuryApiController@createReport');
$router->post('/api/injury/delete', 'InjuryApiController@deleteReport');
$router->post('/api/injury/update', 'InjuryApiController@updateReport');

$router->post('/profile/upload-image', 'ProfileController@uploadProfileImage');

$router->get('/sign-up', 'AuthController@showSignupForm');
$router->get('/sign-in', 'AuthController@showSigninForm');
$router->get('/student-sign-up', 'AuthController@showStudentSignupForm');
$router->post('/sign-up', 'AuthController@handleSignup');
$router->post('/sign-in', 'AuthController@handleSignin');
$router->post('sign-up-student', 'AuthController@handleStudentSignup');
$router->get('/logout', 'AuthController@handleLogout');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
