<?php
// Only show team details if sport_id is provided
if (isset($_GET['sport_id'])) {
    $sport_id = $_GET['sport_id'];
    $pdo = Database::getConnection();
    
    try {
        // Get sport information
        $stmt = $pdo->prepare("SELECT * FROM sport WHERE sport_id = :sport_id");
        $stmt->execute(['sport_id' => $sport_id]);
        $sport = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($sport) {
            // Get team members using SportTeam model
            require_once __DIR__ . '/../../../models/SportTeam.php';
            $teamModel = new SportTeam();
            $members = $teamModel->getTeamMembers($sport_id);
            $member_count = count($members);
?>

<div class="team-details-container">
    <div class="team-details-header">
        <div class="sport-info">
            <h2><i class="fas fa-users-cog"></i> <?php echo htmlspecialchars($sport['sport_name']); ?> Team</h2>
            <p class="sport-description"><?php echo htmlspecialchars($sport['description'] ?? 'No description available'); ?></p>
            <div class="team-meta">
                <span class="meta-item">
                    <i class="fas fa-hashtag"></i>
                    Sport ID: <strong><?php echo htmlspecialchars($sport['sport_id']); ?></strong>
                </span>
                <span class="meta-item">
                    <i class="fas fa-user-friends"></i>
                    Members: <strong><?php echo $member_count; ?></strong>
                </span>
            </div>
        </div>
        <div class="team-actions">
            <a href="admin-teams" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Teams
            </a>
        </div>
    </div>
    
    <!-- Team Staff Section -->
    <div class="team-staff-section">
        <h3><i class="fas fa-user-tie"></i> Team Staff</h3>
        <div class="staff-cards">
            <!-- Coach Card -->
            <div class="staff-card">
                <div class="staff-card-header">
                    <i class="fas fa-whistle"></i>
                    <span>Coach</span>
                </div>
                <div class="staff-card-body">
                    <?php if (!empty($sport_data['coach_fname'])): ?>
                        <h4><?php echo htmlspecialchars($sport_data['coach_fname'] . ' ' . $sport_data['coach_lname']); ?></h4>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($sport_data['coach_email'] ?? 'N/A'); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($sport_data['coach_contact'] ?? 'N/A'); ?></p>
                    <?php else: ?>
                        <p class="not-assigned">Not Assigned</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Captain Card -->
            <div class="staff-card">
                <div class="staff-card-header">
                    <i class="fas fa-user-shield"></i>
                    <span>Captain</span>
                </div>
                <div class="staff-card-body">
                    <?php if (!empty($sport_data['captain_fname'])): ?>
                        <h4><?php echo htmlspecialchars($sport_data['captain_fname'] . ' ' . $sport_data['captain_lname']); ?></h4>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($sport_data['captain_email'] ?? 'N/A'); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($sport_data['captain_contact'] ?? 'N/A'); ?></p>
                    <?php else: ?>
                        <p class="not-assigned">Not Assigned</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Manager Card -->
            <div class="staff-card">
                <div class="staff-card-header">
                    <i class="fas fa-tasks"></i>
                    <span>Manager</span>
                </div>
                <div class="staff-card-body">
                    <?php if (!empty($sport_data['manager_fname'])): ?>
                        <h4><?php echo htmlspecialchars($sport_data['manager_fname'] . ' ' . $sport_data['manager_lname']); ?></h4>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($sport_data['manager_email'] ?? 'N/A'); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($sport_data['manager_contact'] ?? 'N/A'); ?></p>
                    <?php else: ?>
                        <p class="not-assigned">Not Assigned</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($member_count > 0): ?>
    <div class="members-section">
        <h3><i class="fas fa-list"></i> Team Members</h3>
        <div class="table-wrapper">
            <table class="members-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['student_id']); ?></td>
                        <td class="member-name">
                            <i class="fas fa-user-circle"></i>
                            <?php echo htmlspecialchars($member['fname'] . ' ' . $member['lname']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo htmlspecialchars($member['contact_no'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($member['joined_date'] ?? 'N/A'); ?></td>
                        <td>
                            <button class="btn-action btn-remove" 
                                    onclick="removeMember('<?php echo $sport_id; ?>', '<?php echo $member['user_id']; ?>', '<?php echo htmlspecialchars($member['fname'] . ' ' . $member['lname']); ?>')">
                                <i class="fas fa-user-minus"></i> Remove
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <h3>No Team Members</h3>
        <p>This sport currently has no team members enrolled.</p>
    </div>
    <?php endif; ?>
</div>

<script>
function removeMember(sportId, userId, userName) {
    if (confirm(`Are you sure you want to remove ${userName} from this team?`)) {
        fetch('/uoc-sports/admin-teams/remove-member', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                sport_id: sportId,
                user_id: userId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification('Member removed successfully', 'success');
                location.reload();
            } else {
                showNotification('Error: ' + (data.message || 'Failed to remove member'), 'error');
            }
        })
        .catch(err => {
            showNotification('Error removing member', 'error');
            console.error(err);
        });
    }
}
</script>

<?php
        } else {
            echo '<div class="team-details-container"><div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>Sport Not Found</h3><p>The requested sport does not exist.</p></div></div>';
        }
    } catch(PDOException $e) {
        echo '<div class="team-details-container"><div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Error</h3><p>Failed to load team details.</p></div></div>';
    }
}
?>
