<?php
require_once __DIR__ . '/../../conn/dbconn.php';
$purok = $_GET['purok'] ?? 'all';
$filter = $purok !== 'all' ? "AND a.purok = '$purok'" : '';

$query = "
  SELECT r.RM_name, SUM(ri.quantity) AS total_kg
  FROM recyclable r
  JOIN record_items ri ON ri.recyclable_id = r.id
  JOIN records rec ON rec.id = ri.record_id
  JOIN account a ON rec.user_id = a.userid
  WHERE 1 $filter
  GROUP BY r.RM_name
";

$result = mysqli_query($conn, $query);
$labels = [];
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
  $labels[] = $row['RM_name'];
  $data[] = (int)$row['total_kg'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: transparent;
    }

    canvas {
      width: 100% !important;
      height: auto !important;
      max-height: 240px;
    }
  </style>
</head>

<body>
  <canvas id="recyclablesChart"></canvas>
  <script>
    const ctx = document.getElementById('recyclablesChart');
    Chart.register(ChartDataLabels);
    new Chart(ctx, {
      type: 'pie',
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
        plugins: {
          datalabels: {
            anchor: 'center',
            align: 'center',
            color: '#fff',
            font: {
              weight: 'bold',
              size: 12
            },
            formatter: (value) => value.toLocaleString() + ' kg',
            clip: false
          },
          legend: {
            display: true,
            position: 'top',
            labels: {
              color: '#2c5e1a',
              font: {
                size: 12
              }
            }
          }
        },
        tooltip: {
          callbacks: {
            label: (ctx) => ctx.dataset.label + ': ' + ctx.parsed + ' kg'
          }
        }
      }
    });
  </script>

</body>

</html>