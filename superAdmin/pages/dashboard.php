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
  ORDER BY product_quantity ASC
";
$lowStock = mysqli_query($conn, $lowStockQuery);
$lowStockCount = mysqli_num_rows($lowStock);

// fetch low stock rows into array for JS
$lowStockItems = [];
if ($lowStock && $lowStockCount > 0) {
  while ($rowLS = mysqli_fetch_assoc($lowStock)) {
    $lowStockItems[] = $rowLS;
  }
}

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
      grid-template-columns: repeat(5, 1fr);
      /* 5 columns for desktop/laptop */
      grid-template-rows: repeat(2, auto);
      /* 2 rows */
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
      justify-content: flex-start;
    }

    .bento-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    /* Header style */
    .bento-card h2 {
      font-size: 1.2rem;
      color: #2c5e1a;
      font-weight: 600;
      margin-bottom: 12px;
    }

    /* Special tile sizes (desktop) */
    .wide {
      grid-column: span 2;
    }

    .tall {
      grid-row: span 2;
    }

    .extra-wide {
      grid-column: span 3;
    }

    .super-wide {
      grid-column: span 5;
    }

    /* Stat cards styling */
    .stat-group {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 15px;
    }

    .stat-tile {
      flex: 1 1 200px;
      /* wider tiles */
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

    /* --- Responsive Grid --- */

    /* Laptop/Desktop 1200px+ => 5×2 grid */
    @media (min-width: 1200px) {
      .bento-grid {
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: repeat(2, auto);
        gap: 20px;
      }
    }

    /* Medium screens 901–1199px => 3 columns */
    @media (min-width: 901px) and (max-width: 1199px) {
      .bento-grid {
        grid-template-columns: repeat(3, 1fr);
        grid-template-rows: auto;
        gap: 16px;
        padding: 16px;
      }

      .bento-card.tall,
      .bento-card.wide {
        grid-column: span 3;
        grid-row: auto;
      }
    }

    /* Tablet 601–900px => 2 columns */
    @media (min-width: 601px) and (max-width: 900px) {
      .bento-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        padding: 12px;
      }

      .bento-card.tall,
      .bento-card.wide {
        grid-column: span 2;
      }
    }

    /* Mobile ≤600px => 1 column */
    @media (max-width: 600px) {
      .bento-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        padding: 10px;
      }

      .bento-card.tall,
      .bento-card.wide {
        grid-column: 1 / -1;
        grid-row: auto;
      }

      .stat-tile h3 {
        font-size: 1.3rem;
      }

      .stat-tile p {
        font-size: 0.8rem;
      }

      .bento-card h2 {
        font-size: 1.05rem;
      }
    }

    /* Notification badge for pending */
    .notify-badge {
      position: absolute;
      top: 8px;
      right: 12px;
      background: #dc3545;
      color: #fff;
      padding: 4px 7px;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 700;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    /* Chart iframe responsiveness */
    iframe {
      width: 100%;
      border: 0;
      height: 250px;
      display: block;
    }

    /* Announcement item */
    .announcement-item {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }

    .announcement-item h5 {
      margin: 0;
      font-size: 0.98rem;
      color: #2c5e1a;
    }

    .announcement-item small {
      color: #777;
    }

    /* --- Side alert (low stock) container --- */
    #sideAlerts {
      position: fixed;
      right: 16px;
      top: 80px;
      width: 320px;
      z-index: 1100;
      display: flex;
      flex-direction: column;
      gap: 10px;
      pointer-events: none;
    }

    @media (max-width: 600px) {
      #sideAlerts {
        width: calc(100% - 24px);
        right: 12px;
        left: 12px;
        top: auto;
        bottom: 16px;
      }
    }

    .side-alert {
      background: linear-gradient(90deg, #fff 0%, #fff 100%);
      border-left: 4px solid #ffc107;
      border-radius: 8px;
      padding: 10px 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      transform: translateX(110%);
      opacity: 0;
      transition: transform 360ms cubic-bezier(.2, .9, .3, 1), opacity 360ms;
      pointer-events: auto;
      display: flex;
      gap: 8px;
      align-items: flex-start;
    }

    .side-alert.show {
      transform: translateX(0);
      opacity: 1;
    }

    .side-alert .close-btn {
      margin-left: auto;
      border: none;
      background: transparent;
      color: #999;
      font-size: 1rem;
      cursor: pointer;
    }

    .side-alert strong {
      display: block;
      font-size: 0.95rem;
      color: #333;
    }

    .side-alert small {
      color: #666;
      display: block;
      margin-top: 4px;
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
    <header class="dashboard-header d-flex justify-content-between align-items-center" style="gap:12px;">
      <div class="header-left d-flex align-items-center" style="gap:12px;">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo" style="width:48px;height:48px;object-fit:cover;border-radius:50%">
        <div class="header-text">
          <h1 style="margin:0;font-size:1.05rem;">E-Recycle Dashboard Page</h1>
          <p style="margin:0;font-size:0.85rem;">Municipality of San Ildefonso</p>
        </div>
      </div>

      <div class="header-right text-end">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>


    <!-- 🌱 Bento Grid Layout -->
    <section class="bento-grid">
      <!-- Stats Tile -->
      <div class="bento-card super-wide">
        <h2>Overall Statistics
          <!-- 🔽 Universal Filter -->
          <div class="d-flex justify-content-end align-items-center px-3" style="margin-top:12px; margin-bottom:6px;">
            <label for="filterPurok" class="me-2 fw-semibold text-success">Filter by Purok:</label>
            <select id="filterPurok" class="form-select form-select-sm" style="width: 150px;">
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
        </h2>
        <div class="stat-group">
          <a href="#" class="stat-tile" style="text-decoration: none;">
            <i class="fa fa-users"></i>
            <h3><?php echo $total_households; ?></h3>
            <p>Registered Households</p>
          </a>

          <a href="notification.php" class="stat-tile" style="text-decoration: none; position:relative;">
            <i class="fa fa-bell" style="<?php echo $pending_notifications > 0 ? 'color:#dc3545;' : ''; ?>"></i>
            <h3><?php echo $pending_notifications; ?></h3>
            <p>New Notifications</p>
            <?php if ($pending_notifications > 0): ?>
              <span class="notify-badge"><?php echo $pending_notifications; ?></span>
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

      <!-- Recyclables by Purok Chart -->
      <div class="bento-card extra-wide" id="purokChartCard">
        <h2>Recyclables by Purok</h2>
        <iframe src="../includes/purokChart.php" class="w-100"></iframe>
      </div>

      <!-- Purok Users List (hidden by default) -->
      <div class="bento-card extra-wide" id="purokUsersCard" style="display:none;">
        <h2>Users in Selected Purok</h2>
        <iframe src="../includes/userChart.php?purok=1" frameborder="0" class="w-100"></iframe>
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
            <div class="announcement-item">
              <div>
                <h5><?= htmlspecialchars($a['announce_name']) ?></h5>
                <small>📅 <?= date('F j, Y', strtotime($a['announce_date'])) ?></small>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No active announcements yet.</p>
        <?php endif; ?>
      </div>

      <div class="bento-card wide">
        <h2>Recyclables by Category</h2>
        <iframe src="../includes/recyclablesChart.php" class="w-100"></iframe>
      </div>

      <!-- Rewards on Stock -->
      <div class="bento-card wide">
        <h2>Rewards on Stock</h2>
        <iframe src="../includes/rewardChart.php" class="w-100"></iframe>
      </div>

    </section>
  </div>

  <!-- Side Alerts container -->
  <div id="sideAlerts" aria-live="polite" aria-atomic="true"></div>

  <script src="/capstoneweb/assets/sidebarToggle.js"></script>
  <script>
    const filterPurok = document.getElementById('filterPurok');
    const chartFrames = document.querySelectorAll('iframe');

    // Stat tile references
    const statHouseholds = document.querySelector('.stat-tile:nth-child(1) h3');
    const statNotifications = document.querySelector('.stat-tile:nth-child(2) h3');
    const statRewards = document.querySelector('.stat-tile:nth-child(3) h3');
    const statRedeemed = document.querySelector('.stat-tile:nth-child(4) h3');

    // Ranking cards
    const rankingCards = document.querySelectorAll('.ranking');
    const usersListCard = rankingCards[0]?.querySelector('ul'); // Top users
    const purokListCard = rankingCards[1]?.querySelector('ul'); // Top Puroks (if exists)

    // Purok chart & users list cards
    const purokChartCard = document.getElementById('purokChartCard');
    const purokUsersCard = document.getElementById('purokUsersCard');
    const purokUsersList = document.getElementById('purokUsersList');

    filterPurok.addEventListener('change', () => {
      const purok = filterPurok.value;

      // ---- Toggle chart vs users list ----
      if (purok === 'all') {
        purokChartCard.style.display = 'block';
        purokUsersCard.style.display = 'none';
      } else {
        purokChartCard.style.display = 'none';
        purokUsersCard.style.display = 'block';

        // Fetch users for selected Purok
        fetch(`../includes/fetch_purok_users.php?purok=${purok}`)
          .then(res => res.json())
          .then(users => {
            purokUsersList.innerHTML = '';
            if (users.length > 0) {
              users.forEach(u => {
                purokUsersList.innerHTML += `
              <li>
                <span>${u.userName}</span>
                <span>⭐ ${parseFloat(u.total_contribution)}</span>
              </li>`;
              });
            } else {
              purokUsersList.innerHTML = '<li>No users found in this Purok</li>';
            }
          })
          .catch(err => console.error('Fetch error:', err));
      }

      // ---- Reload all other charts (with filter query param) ----
      chartFrames.forEach(frame => {
        const src = frame.getAttribute('src').split('?')[0];
        frame.src = purok === 'all' ? src : `${src}?purok=${purok}`;
      });

      // ---- Fetch dashboard stats ----
      fetch(`../includes/fetch_dashboard_data.php?purok=${purok}`)
        .then(res => res.json())
        .then(updateDashboard)
        .catch(err => console.error('Dashboard stats fetch error:', err));
    });

    // ---- Function to update stats, users, and puroks ----
    function updateDashboard(data) {
      // Update overall stats
      if (data.stats) {
        statHouseholds.textContent = data.stats.total_households;
        statNotifications.textContent = data.stats.pending_notifications;
        statRewards.textContent = data.stats.total_rewards;
        statRedeemed.textContent = data.stats.redeemed_total;

        // Notification badge
        const badge = document.querySelector('.notify-badge');
        if (data.stats.pending_notifications > 0) {
          if (!badge) {
            const span = document.createElement('span');
            span.className = 'notify-badge';
            span.textContent = data.stats.pending_notifications;
            document.querySelector('.stat-tile:nth-child(2)').appendChild(span);
          } else {
            badge.textContent = data.stats.pending_notifications;
          }
        } else if (badge) {
          badge.remove();
        }
      }

      // Update Top Users list
      if (usersListCard) {
        usersListCard.innerHTML = '';
        if (data.users && data.users.length > 0) {
          data.users.forEach(u => {
            usersListCard.innerHTML += `
          <li>
            <span>${u.userName}</span>
            <span>⭐ ${parseFloat(u.total_contribution)}</span>
          </li>`;
          });
        } else {
          usersListCard.innerHTML = '<li>No data available</li>';
        }
      }

      // Update Top Puroks list
      if (purokListCard) {
        purokListCard.innerHTML = '';
        if (data.puroks && data.puroks.length > 0) {
          data.puroks.forEach(p => {
            purokListCard.innerHTML += `
          <li>
            <span>Purok ${p.purok}</span>
            <span>⭐ ${parseFloat(p.total_contribution)}</span>
          </li>`;
          });
        } else {
          purokListCard.innerHTML = '<li>No data available</li>';
        }
      }
    }

    // ----- Side alert handling for low stock -----
    const lowStockItems = <?php echo json_encode($lowStockItems, JSON_UNESCAPED_UNICODE); ?> || [];
    const sideAlertsContainer = document.getElementById('sideAlerts');

    function createSideAlert(item, idx) {
      const id = `sideAlert-${Date.now()}-${idx}`;
      const wrapper = document.createElement('div');
      wrapper.className = 'side-alert';
      wrapper.id = id;

      wrapper.innerHTML = `
    <div style="flex:0 0 8px; margin-top:4px; width:8px; height:36px; background:#ffc107; border-radius:4px;"></div>
    <div style="flex:1">
      <strong>Low stock: ${escapeHtml(item.product_name)}</strong>
      <small>Remaining: ${escapeHtml(item.product_quantity)}</small>
    </div>
    <button class="close-btn" aria-label="Close">&times;</button>
  `;

      wrapper.querySelector('.close-btn').addEventListener('click', () => dismissAlert(wrapper));

      sideAlertsContainer.appendChild(wrapper);
      requestAnimationFrame(() => wrapper.classList.add('show'));
      wrapper.dataset.timeout = setTimeout(() => dismissAlert(wrapper), 10000);
    }

    function dismissAlert(el) {
      if (!el) return;
      el.classList.remove('show');
      setTimeout(() => {
        if (el.parentElement) el.parentElement.removeChild(el);
      }, 380);
    }

    function escapeHtml(unsafe) {
      if (unsafe === null || unsafe === undefined) return '';
      return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    // Initialize low stock alerts
    if (lowStockItems && lowStockItems.length > 0) {
      lowStockItems.forEach((it, idx) => {
        setTimeout(() => createSideAlert(it, idx), idx * 250);
      });
    }
  </script>

</body>

</html>