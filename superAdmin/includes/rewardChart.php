<?php
require_once __DIR__ . '/../../conn/dbconn.php';

$query = "SELECT product_name AS label, product_quantity AS stock FROM rewards ORDER BY product_name ASC";
$result = mysqli_query($conn, $query);

$datasets = [];
$backgroundColors = [
    'rgba(76, 175, 80, 0.7)',
    'rgba(139, 195, 74, 0.7)',
    'rgba(205, 220, 57, 0.7)',
    'rgba(255, 235, 59, 0.7)',
    'rgba(255, 193, 7, 0.7)',
    'rgba(255, 152, 0, 0.7)',
    'rgba(255, 87, 34, 0.7)'
];
$idx = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $datasets[] = [
        'label' => $row['label'],
        'data' => [(int)$row['stock']], // single value in its own dataset
        'backgroundColor' => $backgroundColors[$idx % count($backgroundColors)],
        'borderColor' => 'rgba(46, 125, 50, 1)',
        'borderWidth' => 1,
        'borderRadius' => 6
    ];
    $idx++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<style>
  body { margin: 0; padding: 0; background: transparent; }
  canvas { width: 100% !important; height: auto !important; max-height: 240px; }
</style>
</head>
<body>
<canvas id="rewardChart"></canvas>
<script>
const ctx = document.getElementById('rewardChart');
Chart.register(ChartDataLabels);

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Stock'], // just one label since each dataset is a reward
        datasets: <?php echo json_encode($datasets); ?>
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: 10 },
        animation: { duration: 1200, easing: 'easeOutQuart' },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#2c5e1a', font: { size: 12 } } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { color: '#2c5e1a', font: { size: 12 } } }
        },
        plugins: {
            datalabels: {
                anchor: 'center',
                align: 'center',
                color: '#000000ff',
                font: { size: 12, weight: 'bold' },
                formatter: (val) => val.toLocaleString()
            },
            legend: { display: true, position: 'bottom', labels: { color: '#2c5e1a', font: { size: 12 } } },
            tooltip: {
                callbacks: {
                    label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y} in stock`
                }
            }
        }
    }
});
</script>
</body>
</html>
