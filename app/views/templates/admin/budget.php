<?php
require_once '../core/Database.php'; // Assuming you already have a PDO setup class

$year = '2025'; // You can make this dynamic using $_GET['year'] or $_POST

try {
    $db = Database::getConnection(); // or however you access the PDO instance

    $stmt = $db->prepare("
        SELECT 
            SUM(spent_amount) AS total_spent, 
            SUM(allocated_amount - spent_amount) AS total_remaining
        FROM budget
        WHERE year = ?
    ");
    $stmt->execute([$year]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<section id="budget-card" class="bg-theme">
    <h3>Budget Overview</h3>
    <div class="flex y-center graph-content-container">
        <div class="graph">
            <canvas id="pieChart" width="300" height="300"></canvas>
            <div class="red-box">
                <p><span></span> : Spent Amount</p>
            </div>
            <div class="blue-box">
                <p><span></span> : Remaining Amount</p>
            </div>
        </div>
        <div class="content">
            <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
                <tr>
                    <td>Total Budget</td>
                    <td>Rs. <?= number_format($result['total_spent'] + $result['total_remaining']) ?></td>
                </tr>
                <tr>
                    <td>Total Spent</td>
                    <td>Rs. <?= number_format($result['total_spent']) ?></td>
                </tr>
                <tr>
                    <td>Total Remaining</td>
                    <td>Rs. <?= number_format($result['total_remaining']) ?></td>
                </tr>
            </table>
            <a href="#" class="view-btn text-black">
                View More
            </a>
        </div>
    </div>
</section>

<script>
    const canvas = document.getElementById('pieChart');
    const ctx = canvas.getContext('2d');
  
    const data = [
      { label: "Spent", value: <?=$result['total_spent']?>, color: "#000" },
      { label: "Remaining", value: <?=$result['total_remaining']?>, color: "#5e2d91" }
    ];
  
    const total = data.reduce((sum, d) => sum + d.value, 0);
  
    let startAngle = 0;
  
    data.forEach((d) => {
      const sliceAngle = (d.value / total) * 2 * Math.PI;
  
      ctx.beginPath();
      ctx.moveTo(150, 150);
      ctx.arc(150, 150, 100, startAngle, startAngle + sliceAngle);
      ctx.closePath();
      ctx.fillStyle = d.color;
      ctx.fill();  
      startAngle += sliceAngle;
    });
  </script>
  