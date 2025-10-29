<?php
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../includes/passwordVerification.php';
require_once __DIR__ . '/../../includes/fetchData.php';
require_once __DIR__ . '/../includes/recordsChart.php';
include __DIR__ . '/../includes/sidebar.php';

$query = "SELECT * FROM account WHERE userid = '$userid'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$query2 = "SELECT COUNT(role) AS total FROM account WHERE role = 'user'";
$result1 = mysqli_query($conn, $query2);

if ($result1) {
    $row = mysqli_fetch_assoc($result1);
    $total_households = $row['total'];
} else {
    $total_households = 0; 
}

$query3 = "SELECT SUM(quantity) AS total_quantity FROM record_items;";
$result2 = mysqli_query($conn, $query3);
if($result2) {
  $row1 = mysqli_fetch_assoc($result2);
  $total_recyclables = $row['total_quantity'];
} else {
  $total_recyclables = 0 ; 
}

// $pending_notifications = 3;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Dashboard | E-Recycle</title>
  <link rel="stylesheet" href="/capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="/capstoneweb/user-admin.css">
  <link rel="stylesheet" href="/capstoneweb/user-admin1.css">
</head>

<body>

  <!-- ===== SIDEBAR ===== -->
  <?php include  '../includes/sidebar.php'; ?>

  <!-- ===== TOGGLE BUTTON ===== -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- ===== CONTENT AREA ===== -->
  <div class="content" id="content">

    <!-- content header -->
      <header class="dashboard-header">
        <div class="header-left">
          <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
          <div class="header-text">
              <h1>E-Recycle Dashboard Page</h1>
              <p>Municipality of San Ildefonso</p>
          </div>
        </div>

        <div class="header-right">
          <span class="date-display"><?php echo date("F j, Y"); ?></span>
        </div>
      </header>

    <!-- ===== STAT CARDS ===== -->
    <section class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon"><i class="fa fa-users"></i></div>
        <div class="stat-info">
          <h3><?php echo $total_households; ?></h3>
          <p>Registered Households</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="fa fa-recycle"></i></div>
        <div class="stat-info">
          <h3><?php echo $total_recyclables; ?></h3>
          <p>Total Recyclables Gathered</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="fa fa-bell"></i></div>
        <div class="stat-info">
          <h3><?php echo $pending_notifications; ?></h3>
          <p>New Notifications</p>
        </div>
      </div>
    </section>

    <!-- ===== MAIN DASHBOARD ===== -->
    <section class="dashboard-grid">
      <div class="card chart">
        <h2>Recycling Overview</h2>

        <!-- Dropdown -->
        <div style="margin-bottom: 20px;">
          <select id="householdSelect" class="form-select" style="max-width: 300px;">
            <option value="">Total Recycled</option>
          </select>
        </div>
        
        <!-- Chart -->
        <div class="chart-wrapper" style="margin-top: 20px;">
          <canvas id="recordChart"></canvas>
        </div>
      </div>
      
      <div class="card ranking">
        <h2>Top Performing Users</h2>
        <ul>
          <li><span>User 1</span><span>⭐ 231</span></li>
          <li><span>User 3</span><span>⭐ 192</span></li>
        </ul>
      </div>
      
      
      <div class="card chart">
        <h2>Recycling Overview</h2>
        
        <!-- Dropdown -->
        <div style="margin-bottom: 20px;">
          <select id="householdSelect" class="form-select" style="max-width: 300px;">
            <option value="">Total Recycled</option>
          </select>
        </div>
        
        <!-- Chart -->
        <div class="chart-wrapper" style="margin-top: 20px;">
          <canvas id="recordChart"></canvas>
        </div>
      </div>

      <div class="card ranking">
        <h2>Top Performing Puroks</h2>
        <ul>
          <li><span>Purok 1</span><span>⭐ 320</span></li>
          <li><span>Purok 3</span><span>⭐ 295</span></li>
          <li><span>Purok 4</span><span>⭐ 270</span></li>
          <li><span>Purok 2</span><span>⭐ 255</span></li>
          <li><span>Purok 5</span><span>⭐ 245</span></li>
        </ul>
      </div>

    </section>

  </div>

  <script src="/capstoneweb/assets/sidebarToggle.js"></script>

  <!-- Verify Password Modal -->
  <div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="">
          <div class="modal-header">
            <h5 class="modal-title" id="verifyPasswordModalLabel">Verify Your Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="verifyPassword" class="form-label">Enter Password</label>
              <input type="password" class="form-control" name="verify_password" id="verifyPassword" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="verify_submit" class="btn btn-primary">Verify</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>

</html>