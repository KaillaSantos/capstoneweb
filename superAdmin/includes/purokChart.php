<?php
require_once __DIR__ . '/../../conn/dbconn.php';
$purok = $_GET['purok'] ?? 'all';
$filter = $purok !== 'all' ? "AND a.purok = '$purok'" : '';

$query = "
  SELECT a.purok, SUM(ri.quantity) AS total
  FROM record_items ri
  JOIN records r ON ri.record_id = r.id
  JOIN account a ON r.user_id = a.userid
  WHERE a.purok IS NOT NULL AND a.purok != 0 $filter
  GROUP BY a.purok
  ORDER BY a.purok;
";
$result = mysqli_query($conn, $query);

$labels = [];
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $labels[] = 'Purok ' . $row['purok'];
  $data[] = (int)$row['total'];
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
  <canvas id="purokChart"></canvas>
  <script>
    const ctx = document.getElementById('purokChart');
    Chart.register(ChartDataLabels);
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Total (kg)',
          data: <?php echo json_encode($data); ?>,
          backgroundColor: [
            'rgba(76, 175, 80, 0.7)',
            'rgba(129, 199, 132, 0.7)',
            'rgba(56, 142, 60, 0.7)',
            'rgba(27, 94, 32, 0.7)'
          ],
          borderColor: 'rgba(46, 125, 50, 1)',
          borderWidth: 1,
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: 10
        },
        animation: {
          duration: 1200,
          easing: 'easeOutQuart'
        },
        scales: {
          x: {
            ticks: { color: '#2c5e1a', font: { size: 12 } },
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
            font: { weight: 'bold', size: 12 },
            formatter: (value) => value.toLocaleString() + ' kg',
            clip: false
          },
          legend: { display: false },
        },
            tooltip: {
            callbacks: {
              label: (ctx) => ctx.dataset.label + ': ' + ctx.parsed.y + ' kg'
            }
          }
        }
    });
  </script>
</body>
</html>
