<?php
require_once __DIR__ . '/../../includes/authSession.php';
include_once __DIR__ . '/../includes/passwordVerification.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Rewards | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb\user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb\assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb\assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="stylesheet" href="\capstoneweb\assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* ✅ Unified Reward Card Styling */
    .reward-card {
      border: 1px solid #ccc;
      border-left: 6px solid #2c5e1a;
      border-radius: 8px;
      padding: 15px;
      margin: 15px 0;
      background: #fff;
      display: flex;
      align-items: flex-start;
      gap: 15px;
      transition: 0.2s ease-in-out;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      height: 100%;
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
      text-transform: capitalize;
    }

    .reward-body p {
      margin: 0;
      font-size: 0.9rem;
    }

    .reward-actions .btn {
      flex: 1;
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .reward-actions .btn-warning {
      background-color: #ffc107;
      border: none;
    }

    .reward-actions .btn-warning:hover {
      background-color: #e0a800;
    }

    .reward-actions .btn-link {
      color: #2c5e1a;
      text-decoration: none;
      font-weight: 500;
      padding-left: 0;
    }

    .reward-actions .btn-link:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .reward-card {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .reward-img {
        width: 100%;
        height: 200px;
      }

      .reward-actions {
        flex-direction: column;
        width: 100%;
      }
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle Button -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- Overlay -->
  <div class="overlay"></div>

  <!-- Page Content -->
  <div class="content" id="content">

    <!-- Header -->
    <header class="dashboard-header">
      <div class="header-left">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>E-Recycle Rewards Page</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>
      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <!-- Add / Reset Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="ps-4"></h4>
      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <a href="newreward.php" class="btn btn-success">
          <i class="fa fa-plus"></i> Add New
        </a>
        <form action="/capstoneweb/function/function.php" method="post" class="d-inline">
          <button type="submit" name="reset_rewards" class="btn btn-danger"
            onclick="return confirm('Are you sure you want to reset all data? This cannot be undone.');">
            <i class="fa-solid fa-trash-can-arrow-up"></i> Reset
          </button>
        </form>
      </div>
    </div>

    <!-- Reward Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4 reward-container">
      <?php
      global $conn;
      $sql = "SELECT * FROM rewards ORDER BY product_date DESC";
      $run = mysqli_query($conn, $sql);

      if (mysqli_num_rows($run) > 0) {
        while ($rows = mysqli_fetch_assoc($run)) {
          $rewardImage = !empty($rows['product_img'])
            ? "/capstoneweb/uploads/productImg/" . $rows['product_img']
            : "/capstoneweb/uploads/productImg/rewardPlaceholder.jpg";
      ?>
          <div class="col">
            <div class="reward-card shadow-sm">
              <img src="<?php echo $rewardImage; ?>" alt="Reward" class="reward-img">
              <div class="reward-body flex-grow-1">
                <h5><?= htmlspecialchars($rows['product_name']) ?></h5>
                <p><i class="fa-solid fa-star text-warning"></i> <?= htmlspecialchars($rows['product_points']) ?> points</p>
                <p><i class="fa fa-calendar"></i> <?= date("F j, Y", strtotime($rows['product_date'])) ?></p>
                <p><i class="fa-regular fa-note-sticky"></i> <?= nl2br(htmlspecialchars($rows['product_description'])) ?></p>
                <div class="reward-actions">
                  <button type="button" class="btn btn-link read-more-btn"
                    data-title="<?= htmlspecialchars($rows['product_name']) ?>"
                    data-points="<?= htmlspecialchars($rows['product_points']) ?>"
                    data-date="<?= date("F j, Y", strtotime($rows['product_date'])) ?>"
                    data-text="<?= htmlspecialchars($rows['product_description']) ?>"
                    data-image="<?= $rewardImage ?>">
                    Read More »
                  </button>
                </div>
                <a href="/capstoneweb/superAdmin/pages/editreward.php?id=<?= $rows['reward_id'] ?>" class="btn btn-warning btn-sm">
                  <i class="fa fa-edit"></i> Edit
                </a>
              </div>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p class='text-center mt-4'>No rewards yet.</p>";
      }
      ?>
    </div>
  </div>

  <!-- Read More Modal -->
  <div class="modal fade" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="readMoreModalLabel">Reward Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img id="modalImage" src="" class="img-fluid mb-3 rounded" alt="Reward Image" style="height: 350px;">
          <h3 id="modalTitle"></h3>
          <p><strong>Date:</strong> <span id="modalDate"></span></p>
          <p id="modalText"></p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="\capstoneweb/assets/sidebarToggle.js"></script>

  <script>
    document.querySelectorAll('.read-more-btn').forEach(button => {
      button.addEventListener('click', function() {
        document.getElementById('modalTitle').textContent = this.getAttribute('data-title');
        document.getElementById('modalDate').textContent = this.getAttribute('data-date');
        document.getElementById('modalText').textContent = this.getAttribute('data-text');
        document.getElementById('modalImage').src = this.getAttribute('data-image');

        const modal = new bootstrap.Modal(document.getElementById('readMoreModal'));
        modal.show();
      });
    });
  </script>

</body>
</html>
