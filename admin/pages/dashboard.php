<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Includes
require_once __DIR__ . '/../../conn/dbconn.php';
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../includes/passwordVerification.php';
require_once __DIR__ . '/../../includes/fetchData.php';
include __DIR__ . '/../includes/sidebar.php';

// ✅ Define user ID
$userid = $_SESSION['userid'] ?? null;
if (!$userid) {
  die("User not logged in");
}

// Fetch user info
$query = "SELECT * FROM account WHERE userid = '$userid'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);


$query1 = "SELECT COUNT(role) AS total FROM account WHERE role = 'user'";
$result1 = mysqli_query($conn, $query1);

if ($result1) {
  $row = mysqli_fetch_assoc($result1);
  $total_households = $row['total'];
} else {
  $total_households = 0;
}

$query2 = "SELECT SUM(quantity) AS total_quantity FROM record_items;";
$result2 = mysqli_query($conn, $query2);

if ($result2) {
  $row1 = mysqli_fetch_assoc($result2);
  $total_recyclables = $row1['total_quantity'];
} else {
  $total_recyclables = 0;
}

$query3 = "SELECT COUNT(status) AS pending_notif FROM user_rewards WHERE status = 'pending';";
$result3 = mysqli_query($conn, $query3);

if ($result3) {
  $row2 = mysqli_fetch_assoc($result3);
  $pending_notifications = $row2['pending_notif'];
} else {
  $pending_notifications = 0;
}

// Reward supply count
$rewardSupply = mysqli_query($conn, "SELECT COUNT(*) AS total FROM rewards");
$row = mysqli_fetch_assoc($rewardSupply);
$total_rewards = $row['total'] ?? 0;

// Redeemed rewards
$redeemed = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_rewards WHERE status = 'Approved'");
$row2 = mysqli_fetch_assoc($redeemed);
$redeemed_total = $row2['total'] ?? 0;

// Top Performing Users
$queryTopUsers = "
SELECT 
a.userid,
a.userName,
        SUM(ri.quantity) AS total_contribution
        FROM account a
    JOIN records r ON a.userid = r.user_id
    JOIN record_items ri ON r.id = ri.record_id
    WHERE a.role = 'user'
    GROUP BY a.userid
    ORDER BY total_contribution DESC
    LIMIT 5
    ";
$resultTopUsers = mysqli_query($conn, $queryTopUsers);

// Top Performing Puroks
$queryTopPuroks = "
SELECT 
        a.purok,
        SUM(ri.quantity) AS total_contribution
        FROM account a
    JOIN records r ON a.userid = r.user_id
    JOIN record_items ri ON r.id = ri.record_id
    GROUP BY a.purok
    ORDER BY total_contribution DESC
    LIMIT 5
    ";
$resultTopPuroks = mysqli_query($conn, $queryTopPuroks);


// 📰 Fetch latest active announcements
$queryAnnounce = "
  SELECT announce_name, announce_date
  FROM announcement
  WHERE status = 'Posted'
  ORDER BY announce_date DESC
  LIMIT 3
";
$resultAnnounce = mysqli_query($conn, $queryAnnounce);

if ($resultAnnounce) {
  $announcements = mysqli_fetch_all($resultAnnounce, MYSQLI_ASSOC);
} else {
  $announcements = [];
}

// ✅ Check for low stock rewards
$lowStockQuery = "
SELECT product_name, product_quantity
  FROM rewards
  WHERE product_quantity < 5
";
$lowStock = mysqli_query($conn, $lowStockQuery);
$lowStockCount = mysqli_num_rows($lowStock);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | E-Recycle</title>
  <link rel="stylesheet" href="/capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css" />
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css" />
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png" />
  <link rel="stylesheet" href="/capstoneweb/user-admin.css" />
  <link rel="stylesheet" href="/capstoneweb/user-admin1.css" />
  <style>
    /* 🌿 BENTO GRID DASHBOARD */
    .bento-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      padding: 20px;
    }

    .bento-card {
      background: #fff;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      transition: all 0.2s ease;
      display: flex;
      flex-direction: column;
      justify-content: first baseline;
    }

    .bento-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    /* Emphasize header style */
    .bento-card h2 {
      font-size: 1.2rem;
      color: #2c5e1a;
      font-weight: 600;
      margin-bottom: 12px;
    }

    /* Special tile sizes (for large screens) */
    @media (min-width: 900px) {
      .wide {
        grid-column: span 2;
      }

      .tall {
        grid-row: span 2;
      }
    }

    /* Stat cards styling */
    .stat-group {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 15px;
    }

    .stat-tile {
      flex: 1 1 150px;
      background: #f2f8f2;
      border-radius: 5px;
      padding: 10px;
      text-align: center;
      border: 1px solid #d4ecd4;
      transition: 0.2s;
    }

    .stat-tile:hover {
      background: #e3f6e3;
    }

    .stat-tile i {
      font-size: 1.8rem;
      color: #2c5e1a;
      margin-bottom: 8px;
    }

    .stat-tile h3 {
      margin: 0;
      font-size: 1.5rem;
      color: #2c5e1a;
    }

    .stat-tile p {
      font-size: 0.9rem;
      color: #555;
    }

    /* Charts and lists */
    canvas {
      width: 100% !important;
      max-height: 250px;
    }

    .ranking ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .ranking li {
      display: flex;
      justify-content: space-between;
      background: #f8f8f8;
      padding: 8px 12px;
      border-radius: 6px;
      margin-bottom: 6px;
      font-size: 0.9rem;
    }

    .ranking li span:first-child {
      font-weight: 500;
      color: #333;
    }

    .ranking li span:last-child {
      color: #2c5e1a;
      font-weight: 600;
    }

    /* Responsive tweaks */
    @media (max-width: 600px) {
      .bento-grid {
        gap: 15px;
        padding: 10px;
      }

      .bento-card {
        padding: 15px;
      }

      .stat-tile h3 {
        font-size: 1.2rem;
      }
    }
  </style>
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

    <!-- 🌱 Bento Grid Layout -->
    <section class="bento-grid">
      <!-- Stats Tile -->
      <div class="bento-card tall">
        <h2>Overall Statistics</h2>
        <!-- 🔽 Universal Filter -->
        <div class="d-flex justify-content-end align-items-center px-3" style="margin-bottom: 5px;">
          <label for="filterPurok" class="me-2 fw-semibold text-success">Filter by Purok:</label>
          <select id="filterPurok" class="form-select form-select-sm" style="width: 200px;">
            <option value="all">All Puroks</option>
            <?php
            $purokList = mysqli_query($conn, "
          SELECT DISTINCT purok FROM account 
          WHERE purok IS NOT NULL AND purok != 0
          ORDER BY purok
        ");
            while ($p = mysqli_fetch_assoc($purokList)) {
              echo "<option value='{$p['purok']}'>Purok {$p['purok']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="stat-group">
          <a href="#" class="stat-tile" style="text-decoration: none;">
            <i class="fa fa-users"></i>
            <h3><?php echo $total_households; ?></h3>
            <p>Registered Households</p>
          </a>

          <a href="notification.php" class="stat-tile" style="text-decoration: none;">
            <i class="fa fa-bell"></i>
            <h3><?php echo $pending_notifications; ?></h3>
            <p>New Notifications</p>
            <?php if ($lowStockCount > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                <span class="visually-hidden">Low stock alert</span>
              </span>
            <?php endif; ?>
          </a>

          <a href="rewards.php" class="stat-tile" style="text-decoration: none;">
            <i class="fa fa-gift"></i>
            <h3><?php echo $total_rewards; ?></h3>
            <p>Rewards Available</p>
          </a>

          <a href="redeemed.php" class="stat-tile" style="text-decoration: none;">
            <i class="fa fa-check-circle"></i>
            <h3><?php echo $redeemed_total; ?></h3>
            <p>Rewards Redeemed</p>
          </a>

        </div>
      </div>

      <!-- Recyclables by Purok -->
      <div class="bento-card wide">
        <h2>Recyclables by Purok</h2>
        <iframe src="../includes/purokChart.php" frameborder="0" class="w-100" style="height: 250px;"></iframe>
      </div>

      <!-- Top Users -->
      <div class="bento-card ranking">
        <h2>Top Performing Users</h2>
        <ul>
          <?php
          if ($resultTopUsers && mysqli_num_rows($resultTopUsers) > 0) {
            while ($user = mysqli_fetch_assoc($resultTopUsers)) {
              echo "<li><span>{$user['userName']}</span><span>⭐ {$user['total_contribution']}</span></li>";
            }
          } else {
            echo "<li>No data available</li>";
          }
          ?>
        </ul>
      </div>

      <!-- Latest Announcements -->
      <div class="bento-card">
        <h2>Latest Announcements</h2>
        <?php if (count($announcements) > 0): ?>
          <?php foreach ($announcements as $a): ?>
            <div class="announcement-item" style="display: flex; align-items: flex-start; gap: 15px; border-bottom: 1px solid #e0e0e0; padding: 10px 0;">
              <div>
                <h5 style="margin: 0; color: #2c5e1a;"><?= htmlspecialchars($a['announce_name']) ?></h5>
                <small style="color: gray;">📅 <?= date('F j, Y', strtotime($a['announce_date'])) ?></small>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No active announcements yet.</p>
        <?php endif; ?>
      </div>

      <div class="bento-card wide">
        <h2>Recyclables by Category</h2>
        <iframe src="../includes/recyclablesChart.php" frameborder="0" class="w-100" style="height: 250px;"></iframe>
      </div>

      <!-- Rewards on Stock -->
      <div class="bento-card wide">
        <h2>Rewards on Stock</h2>
        <iframe src="../includes/rewardChart.php" frameborder="0" class="w-100" style="height: 250px;"></iframe>
      </div>

    </section>
  </div>

  <script src="/capstoneweb/assets/sidebarToggle.js"></script>
  <script>
    const filterPurok = document.getElementById('filterPurok');
    const chartFrames = document.querySelectorAll('iframe');

    filterPurok.addEventListener('change', () => {
      const purok = filterPurok.value;

      // 🔄 Reload all charts
      chartFrames.forEach(frame => {
        const src = frame.getAttribute('src').split('?')[0];
        frame.src = purok === 'all' ? src : `${src}?purok=${purok}`;
      });

      // 🔄 Fetch and update Top Users + Puroks
      fetch(`../includes/fetch_dashboard_data.php?purok=${purok}`)
        .then(res => res.json())
        .then(data => {
          const rankingCards = document.querySelectorAll('.ranking');
          const usersList = rankingCards[0]?.querySelector('ul');
          const purokList = rankingCards[1]?.querySelector('ul');

          if (!usersList || !purokList) {
            console.error('Ranking card structure not found.');
            return;
          }

          // 🧍 Top Users
          usersList.innerHTML = '';
          if (data.users.length > 0) {
            data.users.forEach(u => {
              usersList.innerHTML += `
              <li>
                <span>${u.userName}</span>
                <span>⭐ ${parseFloat(u.total_contribution)}</span>
              </li>`;
            });
          } else {
            usersList.innerHTML = '<li>No data available</li>';
          }

          // 🏘️ Top Puroks
          purokList.innerHTML = '';
          if (data.puroks.length > 0) {
            data.puroks.forEach(p => {
              purokList.innerHTML += `
              <li>
                <span>Purok ${p.purok}</span>
                <span>⭐ ${parseFloat(p.total_contribution)}</span>
              </li>`;
            });
          } else {
            purokList.innerHTML = '<li>No data available</li>';
          }
        })
        .catch(err => console.error('Fetch error:', err));
    });
  </script>

  </script>

</body>

</html>