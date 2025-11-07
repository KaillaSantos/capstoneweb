<?php
require_once __DIR__ . '/../../conn/dbconn.php';
$purok = $_GET['purok'] ?? 'all';
$filter = $purok !== 'all' ? "AND a.purok = '$purok'" : '';

$query = "
  SELECT product_name AS label, product_quantity AS stock
  FROM rewards
  ORDER BY product_name ASC
";
$result = mysqli_query($conn, $query);

$labels = [];
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
  $labels[] = $row['label'];
  $data[] = (int)$row['stock'];
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
    canvas {
      width: 100% !important;
      height: auto !important;
      max-height: 240px;
    }
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
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Available Stock',
          data: <?php echo json_encode($data); ?>,
          backgroundColor: [
            'rgba(76, 175, 80, 0.7)',
            'rgba(139, 195, 74, 0.7)',
            'rgba(205, 220, 57, 0.7)',
            'rgba(255, 235, 59, 0.7)',
            'rgba(255, 193, 7, 0.7)'
          ],
          borderColor: 'rgba(46, 125, 50, 1)',
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: 10 },
        animation: {
          duration: 1200,
          easing: 'easeOutQuart'
        },
        scales: {
          x: {
            ticks: {
              color: '#2c5e1a',
              font: { size: 12 },
              maxRotation: 0,
              autoSkip: true
            },
            grid: { display: false }
          },
          y: {
            beginAtZero: true,
            ticks: { color: '#2c5e1a', font: { size: 12 } },
            grid: { color: 'rgba(0,0,0,0.05)' }
          }
        },
        plugins: {
          datalabels: {
            anchor: 'end',
            align: 'top',
            color: '#1b5e20',
            font: { size: 12, weight: 'bold' },
            formatter: (val) => val.toLocaleString()
          },
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: (ctx) => `${ctx.label}: ${ctx.parsed.y} in stock`
            }
          }
        }
      }
    });
  </script>
</body>
</html>
