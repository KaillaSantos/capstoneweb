<?php
require_once __DIR__ . '/../../includes/authSession.php';
include_once __DIR__ . '/../includes/passwordVerification.php';
require_once __DIR__ . '/../../conn/dbconn.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Reward Redemption Notifications | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb\user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb\assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb\assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon"
    href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .reward-card {
      border: 1px solid #ccc;
      border-left: 6px solid #2c5e1a;
      /* Sleek green accent */
      border-radius: 8px;
      padding: 15px;
      margin: 15px 0;
      background: #fff;
      display: flex;
      align-items: flex-start;
      gap: 15px;
      transition: 0.2s ease-in-out;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .reward-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
    }

    .reward-img {
      width: 150px;
      height: 120px;
      object-fit: cover;
      border-radius: 6px;
    }

    .reward-body h5 {
      font-weight: 600;
      margin-bottom: 6px;
    }

    .reward-body p {
      margin: 0;
      font-size: 0.9rem;
    }

    .badge-status {
      font-size: 0.85rem;
      padding: 4px 10px;
      border-radius: 12px;
    }

    .reward-actions {
      margin-top: 10px;
    }

    .reward-actions .btn {
      width: 100%;
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .reward-actions .btn-success {
      background-color: #2c5e1a;
      border: none;
    }

    .reward-actions .btn-success:hover {
      background-color: #4ea42fff;
    }
  </style>

</head>

<body>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- Overlay -->
  <div class="overlay"></div>

  <!-- Main Content -->
  <div class="content" id="content">

    <!-- Header -->
    <header class="dashboard-header">
      <div class="header-left">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>Reward Redemption Notifications</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>
      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>
    <?php if (isset($_SESSION['notif_success']) || isset($_SESSION['notif_error'])): ?>
      <div id="notif-alert"
        class="alert <?= isset($_SESSION['notif_success']) ? 'alert-success' : 'alert-danger'; ?> 
       alert-dismissible fade show text-center mx-auto mt-3"
        style="max-width: 600px; z-index: 2000;">

        <?= $_SESSION['notif_success'] ?? $_SESSION['notif_error']; ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php unset($_SESSION['notif_success'], $_SESSION['notif_error']); ?>
    <?php endif; ?>

    <!-- Reward Request Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4 reward-container">
      <?php
      $records_per_page = 6;
      $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
      $offset = ($page - 1) * $records_per_page;

      $sql = "
        SELECT ur.id, ur.user_id, ur.reward_id, ur.status, ur.date_redeemed,
               a.userName, r.product_name, r.product_img
        FROM user_rewards ur
        JOIN account a ON ur.user_id = a.userid
        JOIN rewards r ON ur.reward_id = r.reward_id
        WHERE ur.status = 'pending'
        ORDER BY ur.date_redeemed DESC
        LIMIT $records_per_page OFFSET $offset
      ";

      $run = mysqli_query($conn, $sql);

      if (mysqli_num_rows($run) > 0) {
        while ($rows = mysqli_fetch_assoc($run)) {
          $rewardImage = !empty($rows['product_img'])
            ? "../../uploads/productImg/" . $rows['product_img']
            : "../../uploads/productImg/rewardPlaceholder.jpg";
      ?>
          <div class="col">
            <div class="reward-card shadow-sm">
              <img src="<?php echo $rewardImage; ?>" alt="Reward Image" class="reward-img">
              <div class="reward-body flex-grow-1">
                <h5 style="text-transform: capitalize;"><?= htmlspecialchars($rows['product_name']) ?></h5>
                <p><i class="fa fa-user text-success"></i> <?= htmlspecialchars($rows['userName']) ?></p>
                <p><i class="fa fa-calendar"></i> <?= date("F j, Y", strtotime($rows['date_redeemed'])) ?></p>
                <span class="badge bg-warning text-dark badge-status"><?= ucfirst($rows['status']) ?></span>

                <div class="reward-actions">
                  <form action="/capstoneweb/function/function.php" method="POST" class="d-inline">
                    <input type="hidden" name="reward_id" value="<?= $rows['reward_id']; ?>">
                    <input type="hidden" name="user_id" value="<?= $rows['user_id']; ?>">
                    <button type="submit" name="approve_reward" class="btn btn-success btn-sm">
                      <i class="fa fa-check"></i> Accept
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p class='text-center mt-4'>No pending reward requests yet.</p>";
      }
      ?>
    </div>

    <!-- Pagination -->
    <?php
    $countQuery = "SELECT COUNT(*) AS total FROM user_rewards WHERE status = 'Pending'";
    $countResult = mysqli_query($conn, $countQuery);
    $total_records = mysqli_fetch_assoc($countResult)['total'];
    $total_pages = ceil($total_records / $records_per_page);

    if ($total_pages > 1): ?>
      <div class="d-flex justify-content-center mt-4 mb-4">
        <nav>
          <ul class="pagination">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    <?php endif; ?>

  </div>

  <script src="\capstoneweb/assets/sidebarToggle.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Automatically fade out the alert after 3 seconds
    setTimeout(() => {
      const alert = document.getElementById('notif-alert');
      if (alert) {
        alert.classList.remove('show');
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 500); // Cleanly remove from DOM
      }
    }, 3000);
  </script>


</body>

</html>