<?php
require_once __DIR__ . '/../../conn/dbconn.php';

// Handle AJAX request
if (isset($_GET['ajax'])) {
    $selectedRecordName = isset($_GET['record_name']) ? $_GET['record_name'] : '';

    if (!empty($selectedRecordName)) {
        // Compare selected household to total per category
        $query = "
            SELECT 
                r.RM_name,
                SUM(CASE WHEN rec.record_name = '" . mysqli_real_escape_string($conn, $selectedRecordName) . "' THEN ri.quantity ELSE 0 END) AS selected_household,
                SUM(ri.quantity) AS total_quantity
            FROM record_items ri
            INNER JOIN recyclable r ON ri.recyclable_id = r.id
            INNER JOIN records rec ON ri.record_id = rec.id
            GROUP BY r.RM_name
            ORDER BY r.RM_name ASC
        ";
    } else {
        // Default: total recyclables per category across all households
        $query = "
            SELECT 
                r.RM_name,
                SUM(ri.quantity) AS total_quantity
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
  select.innerHTML = '<option value="">Total Recyclables per Category</option>';
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
    let labels, datasets;

    if (!recordName) {
      // Case 1: Default view → total recyclables per category
      labels = data.map(item => item.RM_name);
      datasets = [{
        label: "Total Recyclables (kg)",
        data: data.map(item => item.total_quantity),
        backgroundColor: labels.map(() => `hsl(${Math.random() * 360}, 70%, 60%)`)
      }];
    } else {
      // Case 2: Specific household selected → compare to total per category
      labels = data.map(item => item.RM_name);
      const totalData = data.map(item => item.total_quantity);
      const selectedData = data.map(item => item.selected_household);

      datasets = [
        {
          label: `${recordName} (kg)`,
          data: selectedData,
          backgroundColor: "rgba(54, 162, 235, 0.7)"
        },
        {
          label: "All Households Total (kg)",
          data: totalData,
          backgroundColor: "rgba(75, 192, 192, 0.7)"
        }
      ];
    }

    const chartTitle = recordName
      ? `Recyclables per Category — ${recordName} vs Total`
      : "Total Recyclables per Category (All Households)";

    if (!chartInstance) {
      chartInstance = new Chart(chartCtx, {
        type: "bar",
        data: { labels, datasets },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: chartTitle,
              font: { size: 18 }
            },
            tooltip: {
              callbacks: {
                label: context => `${context.dataset.label}: ${context.formattedValue} kg`
              }
            },
            legend: { position: "top" }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: "Quantity (kg)" }
            }
          },
          animation: {
            duration: 1000,
            easing: "easeInOutQuart"
          }
        }
      });
    } else {
      chartInstance.data.labels = labels;
      chartInstance.data.datasets = datasets;
      chartInstance.options.plugins.title.text = chartTitle;
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
