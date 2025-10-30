<?php
require_once __DIR__ . '/../../conn/dbconn.php';

// Handle AJAX request for data
if (isset($_GET['ajax'])) {
  $selectedRecordName = isset($_GET['record_name']) ? $_GET['record_name'] : '';

  // If specific household is selected
  if (!empty($selectedRecordName)) {
    $query = "
      SELECT r.RM_name, SUM(ri.quantity) AS total_quantity
      FROM record_items ri
      INNER JOIN recyclable r ON ri.recyclable_id = r.id
      INNER JOIN records rec ON ri.record_id = rec.id
      WHERE rec.record_name = '" . mysqli_real_escape_string($conn, $selectedRecordName) . "'
      GROUP BY r.RM_name
      ORDER BY r.RM_name ASC
    ";
  } else {
    // All households → total recycled across all
    $query = "
      SELECT r.RM_name, SUM(ri.quantity) AS total_quantity
      FROM record_items ri
      INNER JOIN recyclable r ON ri.recyclable_id = r.id
      INNER JOIN records rec ON ri.record_id = rec.id
      GROUP BY r.RM_name
      ORDER BY r.RM_name ASC
    ";
  }

  $result = mysqli_query($conn, $query);
  $data = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
  }

  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

// Fetch all distinct households for dropdown
$households = [];
$householdQuery = "SELECT DISTINCT record_name FROM records ORDER BY record_name ASC";
$householdResult = mysqli_query($conn, $householdQuery);
while ($row = mysqli_fetch_assoc($householdResult)) {
  $households[] = $row['record_name'];
}
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const chartCtx = document.getElementById("recordChart").getContext("2d");
    const select = document.getElementById("householdSelect");
    let chartInstance = null;
    const households = <?php echo json_encode($households); ?>;

    // Populate dropdown
    select.innerHTML = '<option value="">Total Recycled</option>';
    households.forEach(h => {
      const opt = document.createElement("option");
      opt.value = h;
      opt.textContent = h;
      select.appendChild(opt);
    });

    // Fetch and render chart
    function fetchData(recordName = "") {
      fetch(`../includes/recordsChart.php?ajax=1&record_name=${encodeURIComponent(recordName)}`)
        .then(res => res.json())
        .then(data => updateChart(data, recordName))
        .catch(err => console.error("Fetch error:", err));
    }

    // Create or update chart
    function updateChart(data, recordName) {
      const labels = data.map(item => item.RM_name);
      const values = data.map(item => item.total_quantity);
      const colors = labels.map(() => `hsl(${Math.random() * 360}, 70%, 60%)`);

      if (!chartInstance) {
        chartInstance = new Chart(chartCtx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [{
              data: values,
              backgroundColor: colors
            }]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: recordName ?
                  `Recycled Breakdown for ${recordName}` : "Total Recycled (All Households)",
                font: {
                  size: 18
                }
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return `${context.label}: ${context.formattedValue} kg`;
                  }
                }
              }
            },
            animation: {
              animateRotate: true,
              animateScale: true,
              duration: 1000,
              easing: "easeInOutQuart"
            }
          }
        });
      } else {
        chartInstance.data.labels = labels;
        chartInstance.data.datasets[0].data = values;
        chartInstance.data.datasets[0].backgroundColor = colors;
        chartInstance.options.plugins.title.text = recordName ?
          `Recycled Breakdown for ${recordName}` :
          "Total Recycled (All Households)";
        chartInstance.update();
      }
    }

    // Initial load
    fetchData();

    // On dropdown change
    select.addEventListener("change", function() {
      fetchData(this.value);
    });
  });
</script>