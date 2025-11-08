<?php
require_once __DIR__ . '/../../conn/dbconn.php';

$purok = $_GET['purok'] ?? '';
if (!$purok) {
    echo "<p>No Purok selected.</p>";
    exit;
}

// 1️⃣ Get all recyclables
$recyclables = [];
$res = mysqli_query($conn, "SELECT RM_name FROM recyclable");
while ($r = mysqli_fetch_assoc($res)) $recyclables[] = $r['RM_name'];

// 2️⃣ Get contributions per user per recyclable
$query = "
    SELECT a.userName, r.RM_name, SUM(ri.quantity) AS total
    FROM account a
    JOIN records rec ON a.userid = rec.user_id
    JOIN record_items ri ON rec.id = ri.record_id
    JOIN recyclable r ON ri.recyclable_id = r.id
    WHERE a.purok = '$purok' AND a.role='user'
    GROUP BY a.userid, r.RM_name
    ORDER BY a.userName, r.RM_name
";
$result = mysqli_query($conn, $query);

// Build arrays
$dataMap = []; // ['userName' => ['Bakal' => 10, 'Plastik' => 5, ...]]
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $user = $row['userName'];
    $recyclable = $row['RM_name'];
    $total = (float)$row['total'];

    if (!isset($dataMap[$user])) {
        $dataMap[$user] = array_fill_keys($recyclables, 0);
        $users[] = $user;
    }
    $dataMap[$user][$recyclable] = $total;
}

// Prepare datasets for Chart.js
$datasets = [];
$colors = ['rgba(76,175,80,0.7)','rgba(129,199,132,0.7)','rgba(56,142,60,0.7)','rgba(27,94,32,0.7)','rgba(165,214,167,0.7)'];
foreach ($recyclables as $i => $rm) {
    $datasets[] = [
        'label' => $rm,
        'data' => array_map(fn($u) => $dataMap[$u][$rm], $users),
        'backgroundColor' => $colors[$i % count($colors)],
        'borderColor' => 'rgba(46,125,50,1)',
        'borderWidth' => 1
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<style>
body { margin: 0; padding: 0; background: transparent; }
    canvas {
      width: 100% !important;
      height: auto !important;
      max-height: 240px;
    }
</style>
</head>
<body>
<canvas id="userChart"></canvas>
<script>
const ctx = document.getElementById('userChart');
Chart.register(ChartDataLabels);

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($users); ?>,
        datasets: <?php echo json_encode($datasets); ?>
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            datalabels: {
                anchor: 'end',
                align: 'top',
                font: { weight: 'bold', size: 12 },
                color: '#1b5e20',
                formatter: val => val.toLocaleString() + ' kg'
            },
            legend: { position: 'top' }
        },
        scales: {
            x: { stacked: false, ticks: { color: '#2c5e1a' }, grid: { display:false } },
            y: { stacked: false, beginAtZero:true, ticks: { color:'#2c5e1a' } }
        }
    }
});
</script>
</body>
</html>
