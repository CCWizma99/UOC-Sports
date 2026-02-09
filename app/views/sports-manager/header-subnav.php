<?php
$currentPage = $_SERVER['REQUEST_URI'];
$userId = $_SESSION['user_id'] ?? null;
$managedSports = [];
$selectedSportId = $_GET['sport'] ?? null;

// Fetch managed sports for dropdown using manager_sport table
if ($userId) {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT s.sport_id, s.sport_name 
                          FROM manager_sport ms
                          JOIN sport s ON ms.sport_id = s.sport_id
                          WHERE ms.user_id = ?
                          ORDER BY s.sport_name");
    $stmt->execute([$userId]);
    $managedSports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no sport selected, use first managed sport
    if (!$selectedSportId && !empty($managedSports)) {
        $selectedSportId = $managedSports[0]['sport_id'];
    }
}
?>
<nav class="sub-nav">
  <ul>
    <li><a id="sub-expenses" href="/uoc-sports/public/sport-manager/expenses<?= $selectedSportId ? '?sport=' . urlencode($selectedSportId) : '' ?>"<?php echo strpos($currentPage, '/sport-manager/expenses') !== false ? ' class="active"' : ''; ?>>Expenses</a></li>
    <li><a id="sub-schedules" href="/uoc-sports/public/sport-manager/practicesessions<?= $selectedSportId ? '?sport=' . urlencode($selectedSportId) : '' ?>"<?php echo strpos($currentPage, '/sport-manager/practicesessions') !== false ? ' class="active"' : ''; ?>>Practice Sessions</a></li>
    <li><a id="sub-competitions" href="/uoc-sports/public/sport-manager/competitions<?= $selectedSportId ? '?sport=' . urlencode($selectedSportId) : '' ?>"<?php echo strpos($currentPage, '/sport-manager/competitions') !== false ? ' class="active"' : ''; ?>>Competitions</a></li>
    <li><a id="sub-team" href="/uoc-sports/public/sport-manager/team<?= $selectedSportId ? '?sport=' . urlencode($selectedSportId) : '' ?>"<?php echo strpos($currentPage, '/sport-manager/team') !== false ? ' class="active"' : ''; ?>>Student Achievements</a></li>
    <li><a id="sub-messages" href="/uoc-sports/public/sport-manager/messages<?= $selectedSportId ? '?sport=' . urlencode($selectedSportId) : '' ?>"<?php echo strpos($currentPage, '/sport-manager/messages') !== false ? ' class="active"' : ''; ?>>Messages</a></li>
  </ul>
  
  <?php if (!empty($managedSports)): ?>
  <div class="sport-selector-container">
    <label for="sport-selector">Sport:</label>
    <select id="sport-selector" class="sport-dropdown">
      <?php foreach ($managedSports as $sport): ?>
        <option value="<?= htmlspecialchars($sport['sport_id']) ?>" 
                <?= $sport['sport_id'] == $selectedSportId ? 'selected' : '' ?>>
          <?= htmlspecialchars($sport['sport_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php endif; ?>
</nav>

<script>
// Handle sport selection change
document.getElementById('sport-selector')?.addEventListener('change', function() {
    const selectedSport = this.value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('sport', selectedSport);
    window.location.href = currentUrl.toString();
});
</script>
