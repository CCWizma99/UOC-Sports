
<div class="container">
	<h1>Achievements - <?= htmlspecialchars($sport_name) ?></h1>
	<p>Manage team and individual achievements for your sport.</p>

	<!-- Team Achievements Section -->

	<section style="margin-bottom: 40px;">
		<h2 style="font-size: 1.5em; color: var(--primary-color, #5e2d91); margin-bottom: 16px;">Team Achievements</h2>
		<?php if (empty($team_achievements)) { ?>
			<p>No team achievements recorded for this sport.</p>
		<?php } else { ?>
			<ul style="background: #faf9fc; border-radius: 10px; padding: 18px 24px; box-shadow: 0 2px 8px #eee;">
				<?php foreach ($team_achievements as $ach): ?>
					<?php $tid = $ach['tournament_id']; ?>
					<li style="margin-bottom: 10px;">
						<div style="display: flex; align-items: center; justify-content: space-between;">
							<span>
								<b><?= htmlspecialchars($ach['achievement']) ?></b>
								<?php if (!empty($ach['tournament_name'])): ?>
									- <?= htmlspecialchars($ach['tournament_name']) ?> (<?= htmlspecialchars($ach['tournament_date']) ?>)
								<?php endif; ?>
							</span>
							<button onclick="toggleTeamDetails('team-<?= $tid ?>')" style="background: #2d1457; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer;">View Details</button>
						</div>
						<div id="team-<?= $tid ?>" class="team-details" style="display:none; margin-top: 16px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 16px;">
							<?php if (!empty($team_details[$tid]['players'])): ?>
								<div style="font-weight: bold; margin-bottom: 8px;">Players:</div>
								<ul style="margin-bottom: 12px;">
									<?php foreach ($team_details[$tid]['players'] as $player): ?>
										<li><?= htmlspecialchars($player['fname'] . ' ' . $player['lname']) ?> (<?= htmlspecialchars($player['email']) ?>)</li>
									<?php endforeach; ?>
								</ul>
							<?php else: ?>
								<div>No players found for this tournament.</div>
							<?php endif; ?>
							<div style="font-weight: bold; margin-bottom: 8px;">Individual Achievements in this Tournament:</div>
							<?php if (!empty($team_details[$tid]['individual_achievements'])): ?>
								<ul>
									<?php foreach ($team_details[$tid]['individual_achievements'] as $ind): ?>
										<li><b><?= htmlspecialchars($ind['achievement']) ?></b> - <?= htmlspecialchars($ind['fname'] . ' ' . $ind['lname']) ?> (<?= $ind['points'] ?> pts)</li>
									<?php endforeach; ?>
								</ul>
							<?php else: ?>
								<div>No individual achievements for this tournament.</div>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
			<script>
			function toggleTeamDetails(id) {
				var el = document.getElementById(id);
				if (el.style.display === 'none') {
					el.style.display = 'block';
				} else {
					el.style.display = 'none';
				}
			}
			</script>
		<?php } ?>
	</section>

	<!-- Individual Achievements Section -->
	<section>
		<h2 style="font-size: 1.5em; color: var(--primary-color, #5e2d91); margin-bottom: 16px;">Individual Achievements</h2>
		<div style="margin: 20px 0;">
			<input type="text" id="searchStudent" placeholder="Search Student..." style="padding: 8px; width: 250px;">
		</div>
		<div class="achievements-list">
		<?php
		if (empty($individual_achievements)) {
			echo '<p>No individual achievements found for this sport/team.</p>';
		} else {
			// Group achievements by user
			$students = [];
			foreach ($individual_achievements as $ach) {
				$uid = $ach['user_id'];
				if (!isset($students[$uid])) {
					$students[$uid] = [
						'fname' => $ach['fname'],
						'lname' => $ach['lname'],
						'email' => $ach['email'],
						'points' => 0,
						'achievements' => []
					];
				}
				$students[$uid]['points'] += (int)($ach['points'] ?? 0);
				$students[$uid]['achievements'][] = $ach;
			}
		?>
		<div class="students-grid" style="display: flex; flex-wrap: wrap; gap: 24px;">
			<?php foreach ($students as $uid => $student): ?>
				<div class="student-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px #eee; padding: 24px; min-width: 260px; max-width: 320px; flex: 1 1 260px;">
					<div style="display: flex; align-items: center; margin-bottom: 12px;">
						<div style="background: #5e2d91; color: #fff; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.5em; font-weight: bold; margin-right: 16px;">
							<?= strtoupper(substr($student['fname'],0,1).substr($student['lname'],0,1)) ?>
						</div>
						<div>
							<div style="font-weight: bold; font-size: 1.1em;"> <?= htmlspecialchars($student['fname'].' '.$student['lname']) ?> </div>
							<div style="font-size: 0.95em; color: #666;"> <?= htmlspecialchars($student['email']) ?> </div>
						</div>
					</div>
					<div style="margin-bottom: 8px;"><b>Points:</b> <?= $student['points'] ?></div>
					<div style="margin-bottom: 8px;"><b><?= count($student['achievements']) ?> Achievements recorded</b></div>
					<button onclick="toggleAchievements('ach-<?= $uid ?>')" style="background: #2d1457; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">View Achievements</button>
					<div id="ach-<?= $uid ?>" class="achievements-details" style="display:none; margin-top: 12px;">
						<ul style="padding-left: 18px;">
						<?php foreach ($student['achievements'] as $ach): ?>
							<li>
								<b><?= htmlspecialchars($ach['achievement']) ?></b> (<?= $ach['points'] ?> pts)
								<?php if (!empty($ach['tournament_name'])): ?>
									- <?= htmlspecialchars($ach['tournament_name']) ?> (<?= htmlspecialchars($ach['tournament_date']) ?>)
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<script>
		function toggleAchievements(id) {
			var el = document.getElementById(id);
			if (el.style.display === 'none') {
				el.style.display = 'block';
			} else {
				el.style.display = 'none';
			}
		}
		// Simple search filter
		document.getElementById('searchStudent').addEventListener('input', function() {
			var val = this.value.toLowerCase();
			document.querySelectorAll('.student-card').forEach(function(card) {
				var name = card.querySelector('div[style*="font-weight: bold"]').innerText.toLowerCase();
				card.style.display = name.includes(val) ? '' : 'none';
			});
		});
		</script>
		<?php } ?>
		</div>
	</section>
</div>
