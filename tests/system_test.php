<?php
/**
 * ============================================================================
 *  UOC Sports Management System — Automated System Test Script (BROWSER + CLI)
 * ============================================================================
 */

define('IS_CLI', PHP_SAPI === 'cli');

// ─── Configuration ──────────────────────────────────────────────────────────
$BASE_URL = 'http://localhost/uoc-sports/public';
$TEST_PASSWORD = 'chama@1234';

$TEST_USERS = [
    'ADMIN' => ['email' => 'chamal.admin@uocs.com', 'user_id' => 'H4J1OHSX', 'name' => 'Chamal Chamuditha'],
    'STUDENT' => ['email' => 'chamal2@gmail.com', 'user_id' => 'L3NCL2J4', 'name' => 'Chamal Hettiarachchi'],
    'CAPTAIN' => ['email' => 'jansibalakrish@gmail.com', 'user_id' => '5Q1XZO2Y', 'name' => 'Jansika Balakrishnan'],
    'COACH' => ['email' => 'chamal1@gmail.com', 'user_id' => 'NPM8O9RE', 'name' => 'Chamal Chamuditha'],
    'SPT' => ['email' => 'chamlaanil99@gmail.com', 'user_id' => 'usr_694d89fa', 'name' => 'Amal Shantha'],
    'EQP' => ['email' => 'ccwrecker99@gmail.com', 'user_id' => 'usr_68f82fe0', 'name' => 'Shashini Malsha'],
    'REG' => ['email' => 'kasun.silva@ucsc.uoc.lk', 'user_id' => 'REG003', 'name' => 'Kasun Silva'],
    'EXECUTIVE' => ['email' => 'nuwan.karunaratne@example.com', 'user_id' => '202', 'name' => 'Nuwan Karunaratne'],
];

$ROLE_PAGES = [
    'ADMIN' => ['/admin-index', '/admin-users', '/admin-reservations', '/admin-players', '/admin-equipments', '/admin-events', '/admin-results', '/admin-teams', '/admin-budget', '/admin-news', '/admin-inquiry', '/admin-executive-dashboard', '/admin-equipment-analytics', '/admin-equipment-reports', '/admin-event-permissions'],
    'STUDENT' => ['/student', '/student/sports', '/student/equipment', '/student/facilities', '/student/bookings'],
    'CAPTAIN' => ['/captain', '/captain/mark-attendance', '/captain/add-members', '/captain/schedule-practice', '/captain/communication', '/captain/team-schedules', '/captain/add-result'],
    'COACH' => ['/coach', '/coach/coach-communicate', '/coach/report-injury'],
    'SPT' => ['/sport-manager', '/sport-manager/schedule', '/sport-manager/expenses', '/sport-manager/messages', '/sport-manager/practicesessions', '/sport-manager/competitions', '/sport-manager/team'],
    'EQP' => ['/equipment-manager', '/equipment-manager/equipment-reservations', '/equipment-manager/equipments', '/equipment-manager/practiceschedule', '/equipment-manager/lostitem', '/equipment-manager/bookingrequests', '/equipment-manager/manage-equipment'],
    'REG' => ['/registrar', '/registrar/verify-students', '/registrar/verify-staff', '/registrar/verify-bookings'],
    'EXECUTIVE' => ['/executive-dashboard'],
];

$PUBLIC_PAGES = ['/', '/news', '/facility-reservation', '/contact-us', '/services', '/stories', '/results', '/sign-in', '/sign-up', '/student-sign-up'];

$API_ENDPOINTS = [
    ['GET', '/public/match-results-api', null, 'Public Match Results'],
    ['GET', '/get-faculties', null, 'Get Faculties'],
    ['GET', '/student/dashboard-stats', 'STUDENT', 'Student Dashboard Stats'],
    ['GET', '/student/available-sports', 'STUDENT', 'Available Sports'],
    ['GET', '/student/enrolled-sports', 'STUDENT', 'Enrolled Sports'],
    ['GET', '/captain/get-permitted-tournaments', 'CAPTAIN', 'Captain Permitted Tournaments'],
    ['GET', '/captain/get-sport-fields', 'CAPTAIN', 'Captain Sport Fields'],
    ['GET', '/admin-equipments/get-sports', 'ADMIN', 'Admin Get Sports'],
    ['GET', '/admin-equipments/analytics', 'ADMIN', 'Admin Equipment Analytics'],
    ['GET', '/admin-inquiry/all', 'ADMIN', 'Admin All Inquiries'],
    ['GET', '/admin-results/get-all', 'ADMIN', 'Admin All Results'],
    ['GET', '/admin-dashboard/analytics', 'ADMIN', 'Admin Dashboard Analytics'],
    ['GET', '/admin-tournament/list', 'ADMIN', 'Admin Tournament List'],
    ['GET', '/sport-manager/budget/remaining', 'SPT', 'Sport Manager Budget Remaining'],
    ['GET', '/sport-manager/remaining-budget', 'SPT', 'Sport Manager Remaining Budget'],
    ['GET', '/api/registrar/pending-count', 'REG', 'Registrar Pending Count'],
    ['GET', '/executive-dashboard/analytics', 'EXECUTIVE', 'Executive Dashboard Analytics'],
    ['GET', '/api/user/registration-stats', null, 'User Registration Stats'],
];

// ─── Browser Header ──────────────────────────────────────────────────────────
if (!IS_CLI) {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>UOC Sports System Test</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --dim: #94a3b8;
                --green: #22c55e; --red: #ef4444; --yellow: #eab308; --cyan: #06b6d4;
            }
            body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; line-height: 1.5; }
            .container { max-width: 1000px; margin: 0 auto; }
            .header-box { background: var(--card); padding: 15px 25px; border-radius: 12px; border-left: 4px solid var(--cyan); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 10px; z-index: 100; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5); }
            h1 { font-size: 1.25rem; margin: 0; color: var(--cyan); }
            .health-badge { padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 1rem; }
            .phase-card { background: var(--card); border-radius: 12px; margin-bottom: 20px; overflow: hidden; border: 1px solid #334155; }
            .phase-header { background: #334155; padding: 12px 20px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
            .test-row { padding: 10px 20px; border-bottom: 1px solid #334155; display: flex; flex-direction: column; }
            .test-main { display: flex; align-items: center; gap: 12px; }
            .status-tag { padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
            .pass-tag { background: rgba(34, 197, 94, 0.2); color: var(--green); }
            .fail-tag { background: rgba(239, 68, 68, 0.2); color: var(--red); }
            .skip-tag { background: rgba(234, 179, 8, 0.2); color: var(--yellow); }
            .detail { font-size: 0.85rem; color: var(--dim); margin-left: 32px; font-family: monospace; margin-top: 4px; }
            .summary-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            .summary-table td { padding: 8px 0; border-bottom: 1px solid #334155; }
            .summary-table td:last-child { text-align: right; font-weight: 600; }
            .error-list { color: var(--red); list-style: none; padding: 0; font-size: 0.85rem; }
            .error-list li { margin-bottom: 5px; background: rgba(239, 68, 68, 0.1); padding: 5px 10px; border-radius: 4px; border-left: 3px solid var(--red); }
            @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
            .running { color: var(--cyan); font-size: 0.75rem; animation: pulse 1s infinite; }
        </style>
    </head>
    <body>
    <div class="container">
    <?php
}

// ─── CLI Argument Parsing ───────────────────────────────────────────────────
$args = IS_CLI ? getopt('', ['role::', 'verbose', 'api-only', 'help']) : [];
if (isset($args['help'])) {
    echo "\n  UOC Sports System Test Script\n  ─────────────────────────────\n  Usage:\n    php tests/system_test.php                     Run all tests\n    php tests/system_test.php --role=ADMIN        Test only ADMIN role\n    php tests/system_test.php --verbose           Show full response details\n    php tests/system_test.php --api-only          Run only API health checks\n\n";
    exit(0);
}

$filterRole = isset($args['role']) ? strtoupper($args['role']) : (isset($_GET['role']) ? strtoupper($_GET['role']) : null);
$verbose = isset($args['verbose']) || isset($_GET['verbose']);
$apiOnly = isset($args['api-only']) || isset($_GET['api-only']);

// ─── Output Helpers ─────────────────────────────────────────────────────────
function colorize(string $text, string $color): string {
    if (!IS_CLI) {
        $colors = ['green' => '#22c55e', 'red' => '#ef4444', 'yellow' => '#eab308', 'cyan' => '#06b6d4', 'magenta' => '#d946ef', 'dim' => '#94a3b8'];
        return "<span style='color: " . ($colors[$color] ?? '#fff') . "'>$text</span>";
    }
    if (PHP_OS_FAMILY === 'Windows' && !getenv('ANSICON') && !getenv('WT_SESSION')) return $text;
    $colors = ['green' => "\033[32m", 'red' => "\033[31m", 'yellow' => "\033[33m", 'blue' => "\033[34m", 'cyan' => "\033[36m", 'bold' => "\033[1m", 'reset' => "\033[0m", 'dim' => "\033[2m", 'magenta' => "\033[35m", 'bg_green' => "\033[42m\033[30m", 'bg_red' => "\033[41m\033[37m"];
    return ($colors[$color] ?? '') . $text . ($colors['reset'] ?? '');
}

function printHeader(string $title): void {
    if (!IS_CLI) {
        echo "</div><div class='phase-card'><div class='phase-header'>$title</div>";
        return;
    }
    $width = 70; $padding = floor(($width - strlen($title) - 6) / 2);
    echo "\n" . colorize("╔" . str_repeat('═', $width - 2) . "╗", 'cyan') . "\n";
    echo colorize("║", 'cyan') . str_repeat(' ', $padding) . "  " . colorize($title, 'bold') . "  " . str_repeat(' ', $width - strlen($title) - $padding - 6) . colorize("║", 'cyan') . "\n";
    echo colorize("╚" . str_repeat('═', $width - 2) . "╝", 'cyan') . "\n";
}

function printResult(string $label, bool $pass, string $detail = ''): void {
    if (!IS_CLI) {
        $statusClass = $pass ? 'pass-tag' : 'fail-tag';
        $statusText = $pass ? 'PASS' : 'FAIL';
        $icon = $pass ? '✔' : '✘';
        echo "<div class='test-row'><div class='test-main'><span class='status-tag $statusClass'>$statusText</span> <span>$label</span></div>";
        if ($detail) echo "<div class='detail'>↳ $detail</div>";
        echo "</div>";
        flush(); ob_flush(); // Force output to browser
        return;
    }
    $icon = $pass ? colorize('✔', 'green') : colorize('✘', 'red');
    $status = $pass ? colorize(' PASS ', 'bg_green') : colorize(' FAIL ', 'bg_red');
    echo sprintf("  %s %s %-60s", $icon, $status, (strlen($label) > 60 ? substr($label, 0, 57) . '...' : $label));
    if ($detail) echo "\n" . colorize("      ↳  $detail", 'dim');
    echo "\n";
}

class TestReporter {
    public $stats = ['total' => 0, 'passed' => 0, 'failed' => 0, 'skipped' => 0];
    public $categories = []; public $errors = [];

    public function record(string $cat, bool $pass, string $label, string $detail = ''): void {
        if (!isset($this->categories[$cat])) $this->categories[$cat] = ['total' => 0, 'passed' => 0, 'failed' => 0];
        $this->stats['total']++; $this->categories[$cat]['total']++;
        if ($pass) { $this->stats['passed']++; $this->categories[$cat]['passed']++; } 
        else { $this->stats['failed']++; $this->categories[$cat]['failed']++; $this->errors[] = "[$cat] $label" . ($detail ? ": $detail" : ""); }
        printResult($label, $pass, $detail);
    }

    public function skip(string $label): void {
        $this->stats['skipped']++;
        if (!IS_CLI) echo "<div class='test-row'><div class='test-main'><span class='status-tag skip-tag'>SKIP</span> <span style='color: #64748b'>$label</span></div></div>";
        else echo colorize("  ⊖ [SKIP] $label\n", 'yellow');
    }

    public function saveReport(string $path): void {
        $passRate = $this->stats['total'] > 0 ? round(($this->stats['passed'] / $this->stats['total']) * 100, 1) : 0;
        $md = "# 📋 System Health Report\n*Generated: " . date('Y-m-d H:i:s') . "*\n\n";
        $md .= "## Health Score: " . ($passRate > 90 ? "✅" : ($passRate > 70 ? "⚠️" : "❌")) . " $passRate%\n\n";
        $md .= "| Category | Total | Passed | Failed | Score |\n| :--- | :---: | :---: | :---: | :---: |\n";
        foreach ($this->categories as $name => $c) {
            $rate = round(($c['passed'] / $c['total']) * 100);
            $md .= "| $name | {$c['total']} | {$c['passed']} | {$c['failed']} | " . ($rate == 100 ? "🟢" : ($rate > 80 ? "🟡" : "🔴")) . " $rate% |\n";
        }
        $md .= "| **TOTAL** | **{$this->stats['total']}** | **{$this->stats['passed']}** | **{$this->stats['failed']}** | **$passRate%** |\n\n";
        if (!empty($this->errors)) { $md .= "## ❌ Issues Found\n"; foreach ($this->errors as $err) $md .= "- $err\n"; }
        file_put_contents($path, $md);
    }
}
$reporter = new TestReporter();

// ─── HTTP Client ────────────────────────────────────────────────────────────
class TestHttpClient {
    private $baseUrl; private $cookieFile;
    public function __construct($url) { $this->baseUrl = rtrim($url, '/'); }
    public function login($email, $pass) {
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'uoc_test_');
        $ch = curl_init($this->baseUrl . '/sign-in');
        curl_setopt_array($ch, [CURLOPT_POST => 1, CURLOPT_POSTFIELDS => http_build_query(['email' => $email, 'password' => $pass]), CURLOPT_RETURNTRANSFER => 1, CURLOPT_HEADER => 1, CURLOPT_COOKIEJAR => $this->cookieFile, CURLOPT_TIMEOUT => 15]);
        $resp = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $loc = preg_match('/Location:\s*(.+)/i', $resp, $m) ? trim($m[1]) : '';
        $ok = ($code === 302 || $code === 301) && !str_contains($loc, 'sign-in');
        curl_close($ch); return ['success' => $ok, 'code' => $code, 'redirect' => $loc];
    }
    public function get($path) {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => 1, CURLOPT_FOLLOWLOCATION => 1, CURLOPT_COOKIEFILE => $this->cookieFile, CURLOPT_TIMEOUT => 20]);
        $body = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch); return ['code' => $code, 'body' => $body ?: '', 'redirected_login' => str_contains($url, 'sign-in') && !str_contains($path, 'sign-in')];
    }
    public function cleanup() { if ($this->cookieFile && file_exists($this->cookieFile)) @unlink($this->cookieFile); }
}

// ─── EXECUTION ──────────────────────────────────────────────────────────────
ob_start(); // Start output buffering
if (!IS_CLI) echo "<div class='header-box'><h1>UOC Sports: Automated System Audit</h1><span class='health-badge' id='health-header' style='background: var(--cyan); color: #fff'>RUNNING...</span></div>";

printHeader('Phase 0: Environment Health');
$reporter->record('ENV', function_exists('curl_init'), 'cURL Extension');
$client = new TestHttpClient($BASE_URL);
$res = $client->get('/sign-in');
$reporter->record('ENV', $res['code'] === 200, "Connectivity: $BASE_URL", "HTTP {$res['code']}");
if ($res['code'] !== 200) { if (!IS_CLI) echo "</div><div style='color: var(--red); font-weight: bold; margin-top:20px;'>FATAL: Server unreachable at $BASE_URL</div></body></html>"; die("\n FATAL: Server unreachable.\n"); }
$client->cleanup();

if (!$apiOnly) {
    printHeader('Phase 1: Public Access Verification');
    $pub = new TestHttpClient($BASE_URL);
    foreach ($PUBLIC_PAGES as $page) {
        $res = $pub->get($page);
        $reporter->record('PUBLIC', $res['code'] === 200 && strlen($res['body']) > 200, "View: $page", "HTTP {$res['code']} | " . strlen($res['body']) . " bytes");
    }
    $pub->cleanup();
}

printHeader('Phase 2: Authentication (Role Login)');
$sessionClients = [];
foreach ($TEST_USERS as $role => $user) {
    if ($filterRole && $filterRole !== $role) { $reporter->skip("Login: $role"); continue; }
    $c = new TestHttpClient($BASE_URL);
    $login = $c->login($user['email'], $TEST_PASSWORD);
    $reporter->record('AUTH', $login['success'], "Login: $role ({$user['email']})", "HTTP {$login['code']}" . ($login['redirect'] ? " → {$login['redirect']}" : ""));
    if ($login['success']) $sessionClients[$role] = $c; else $c->cleanup();
}

if (!$apiOnly) {
    printHeader('Phase 3: Role-Specific Routing');
    foreach ($ROLE_PAGES as $role => $pages) {
        if ($filterRole && $filterRole !== $role) continue;
        if (!IS_CLI) echo "<div class='test-row' style='background: rgba(217, 70, 239, 0.05); font-weight: 500;'>&nbsp;&nbsp;➲ $role Modules</div>";
        else echo "\n  " . colorize("➲ $role Modules", 'magenta') . "\n";
        
        if (!isset($sessionClients[$role])) { foreach($pages as $p) $reporter->skip("$role: $p"); continue; }
        foreach ($pages as $page) {
            $res = $sessionClients[$role]->get($page);
            $hasErr = stripos($res['body'], 'Fatal error') !== false || stripos($res['body'], 'Uncaught') !== false;
            $pass = $res['code'] === 200 && !$res['redirected_login'] && !$hasErr;
            $reporter->record('PAGES', $pass, "Page: $page", "HTTP {$res['code']}" . ($hasErr ? " | ⚠ PHP_ERROR" : ""));
        }
    }
}

printHeader('Phase 4: API Endpoint Integrity');
foreach ($API_ENDPOINTS as [$method, $path, $role, $label]) {
    if ($filterRole && $role && $filterRole !== $role) { $reporter->skip("API: $label"); continue; }
    $c = ($role && isset($sessionClients[$role])) ? $sessionClients[$role] : new TestHttpClient($BASE_URL);
    $res = $c->get($path);
    $isJson = json_decode($res['body']) !== null;
    $pass = $res['code'] === 200 && !$res['redirected_login'] && strlen($res['body']) > 2;
    $reporter->record('API', $pass, "API: $label", "HTTP {$res['code']} | " . ($isJson ? "JSON ✓" : "RAW"));
}

printHeader('Phase 5: Persistence Smoke Test');
$dbClient = new TestHttpClient($BASE_URL);
$res = $dbClient->get('/api/user/registration-stats');
$reporter->record('DB', $res['code'] === 200 && json_decode($res['body']) !== null, 'Query Integrity', "Stats API check");
$dbClient->cleanup();

echo "</div>"; // Close final card

// Final Summary calculations
foreach ($sessionClients as $c) $c->cleanup();
$passRate = $reporter->stats['total'] > 0 ? round(($reporter->stats['passed'] / $reporter->stats['total']) * 100) : 0;
$scoreColor = $passRate > 90 ? 'var(--green)' : ($passRate > 70 ? 'var(--yellow)' : 'var(--red)');

// Browser Final Summary
if (!IS_CLI) {
    ?>
    <div class="phase-card">
        <div class="phase-header">Final Audit Summary</div>
        <div style="padding: 20px;">
            <div style="display: flex; gap: 40px; align-items: flex-start;">
                <div style="flex: 1;">
                    <table class="summary-table">
                        <tr><td>Checks Run</td><td><?php echo $reporter->stats['total']; ?></td></tr>
                        <tr><td>Passed</td><td style="color: var(--green)">✔ <?php echo $reporter->stats['passed']; ?></td></tr>
                        <tr><td>Failed</td><td style="color: var(--red)">✘ <?php echo $reporter->stats['failed']; ?></td></tr>
                        <tr><td>Skipped</td><td style="color: var(--yellow)">⊖ <?php echo $reporter->stats['skipped']; ?></td></tr>
                        <tr style="border-top: 2px solid #334155; height: 50px;">
                            <td style="font-size: 1.1rem;">HEALTH SCORE</td>
                            <td style="font-size: 1.5rem; color: <?php echo $scoreColor; ?>"><?php echo $passRate; ?>%</td>
                        </tr>
                    </table>
                </div>
                <div style="flex: 1.5;">
                    <?php if (!empty($reporter->errors)): ?>
                        <h3 style="font-size: 1rem; margin-top: 0;">Critical Faults</h3>
                        <ul class="error-list">
                            <?php foreach (array_slice($reporter->errors, 0, 15) as $err): ?>
                                <li><?php echo htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px; color: var(--green);">
                            <div style="font-size: 3rem;">🚀</div>
                            <h2 style="margin: 10px 0;">All Systems Nominal</h2>
                            <p style="color: var(--dim);">System is operating perfectly.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('health-header').innerText = '<?php echo $passRate; ?>% HEALTH';
        document.getElementById('health-header').style.background = '<?php echo $scoreColor; ?>';
    </script>
    </div></body></html>
    <?php
}

// CLI Summary
if (IS_CLI) {
    printHeader('System Health Overview');
    $cliColor = $passRate > 90 ? 'green' : ($passRate > 70 ? 'yellow' : 'red');
    echo "\n  " . colorize("┌" . str_repeat('─', 38) . "┐", 'cyan') . "\n";
    echo sprintf("  " . colorize("│", 'cyan') . "  %-20s %15s  " . colorize("│", 'cyan') . "\n", "Checks Run:", $reporter->stats['total']);
    echo sprintf("  " . colorize("│", 'cyan') . "  %-20s " . colorize("%15s", 'green') . "  " . colorize("│", 'cyan') . "\n", "Passed:", "✔ " . $reporter->stats['passed']);
    echo sprintf("  " . colorize("│", 'cyan') . "  %-20s " . colorize("%15s", $reporter->stats['failed'] > 0 ? 'red' : 'green') . "  " . colorize("│", 'cyan') . "\n", "Failed:", "✘ " . $reporter->stats['failed']);
    echo sprintf("  " . colorize("│", 'cyan') . "  %-20s " . colorize("%15s", 'yellow') . "  " . colorize("│", 'cyan') . "\n", "Skipped:", "⊖ " . $reporter->stats['skipped']);
    echo "  " . colorize("├" . str_repeat('─', 38) . "┤", 'cyan') . "\n";
    echo sprintf("  " . colorize("│", 'cyan') . "  %-20s " . colorize("%15s", $cliColor) . "  " . colorize("│", 'cyan') . "\n", "HEALTH SCORE:", "$passRate%");
    echo "  " . colorize("└" . str_repeat('─', 38) . "┘", 'cyan') . "\n\n";

    if (!empty($reporter->errors)) {
        echo colorize("  ❌ Critical Faults Found:\n", 'red');
        foreach (array_slice($reporter->errors, 0, 10) as $err) echo "     - $err\n";
    } else echo colorize("  🚀 ALL SYSTEMS NOMINAL. GREAT JOB!\n", 'green');
}

$reporter->saveReport(dirname(__FILE__) . '/test_report.md');
if (IS_CLI) echo colorize("\n  📄 Detailed report saved to: tests/test_report.md\n\n", 'dim');
exit($reporter->stats['failed'] === 0 ? 0 : 1);
