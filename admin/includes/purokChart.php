<?php
require_once __DIR__ . '/../../conn/dbconn.php';

// Fetch total recyclables per purok
$query = "
    SELECT 
        a.purok,
        SUM(ri.quantity) AS total_quantity
    FROM record_items ri
    INNER JOIN records rec ON ri.record_id = rec.id
    INNER JOIN account a ON rec.user_id = a.userid
    WHERE a.purok IS NOT NULL AND a.purok != ''
    GROUP BY a.purok
    ORDER BY a.purok ASC
";

$result = mysqli_query($conn, $query);

$purokLabels = [];
$totalQuantities = [];

// Ensure all 7 puroks appear even if 0 kg
$allPuroks = range(1,7);
$purokData = array_fill_keys($allPuroks, 0);

while ($row = mysqli_fetch_assoc($result)) {
    $purok = intval($row['purok']);
    $purokData[$purok] = floatval($row['total_quantity']);
}

foreach ($purokData as $purok => $qty) {
    $purokLabels[] = "Purok $purok";
    $totalQuantities[] = $qty;
}
?>

<!-- Chart Canvas -->
<canvas id="purokChart"></canvas>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const ctx = document.getElementById("purokChart").getContext("2d");

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($purokLabels); ?>,
      datasets: [{
        label: 'Total Recyclables (kg)',
        data: <?php echo json_encode($totalQuantities); ?>,
        backgroundColor: [
          'rgba(255, 99, 132, 0.7)',
          'rgba(54, 162, 235, 0.7)',
          'rgba(255, 206, 86, 0.7)',
          'rgba(75, 192, 192, 0.7)',
          'rgba(153, 102, 255, 0.7)',
          'rgba(255, 159, 64, 0.7)',
          'rgba(199, 199, 199, 0.7)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)',
          'rgba(159, 159, 159, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Total Recyclables per Purok',
          font: { size: 18 }
        },
        legend: {
          display: false // <-- Legend removed
        },
        tooltip: {
          callbacks: {
            label: context => `${context.dataset.label}: ${context.formattedValue} kg`
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Quantity (kg)' }
        }
      }
    }
  });
});
</script>
