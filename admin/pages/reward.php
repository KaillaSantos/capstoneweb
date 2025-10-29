<?php
require_once __DIR__ . '/../../includes/authSession.php';
include_once __DIR__ . '/../includes/passwordVerification.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Reward | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb\user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb\assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb\assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* keeping these in the same file cause for some reason it just wont work when in style.css */
    .reward-card {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 15px;
      margin: 15px 0;
      background: #fff;
      display: flex;
      align-items: flex-start;
      gap: 15px;
      height: 100%;
    }

    .reward-img {
      width: 150px;
      height: 120px;
      object-fit: cover;
      border-radius: 6px;
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle Button (visible on all screens) -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- Overlay (for mobile view) -->
  <div class="overlay"></div>
    
  <!-- Page Content -->
  <div class="content" id="content">
    
    <!-- content header -->
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

    <!-- 🔹 Add New, Add, Reset Button -->
    <div class="d-flex justify-content-between align-items-center">
      <h4 style="padding-left: 50px;">  </h4>
      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <form method="post" action="announcement.php">
          <a href="newreward.php" class="btn btn-success"> <i class="fa fa-plus"></i> Add New</a>
        </form>
        <form action="/capstoneweb/function/function.php" method="post" class="d-inline">
          <button type="submit" name="reset_rewards" class="btn btn-danger" onclick="return confirm('Are you sure you want to reset all data? This cannot be undone.');"><i class="fa-solid fa-trash-can-arrow-up"></i> Reset </button>
        </form>
      </div>
    </div>

    <!-- Reward Container -->
    <div class="row row-cols-1 row-cols-md-3 g-4 reward-container">
      <?php
      global $conn; // Assuming $conn is available from authSession.php
      $sql = "SELECT * FROM rewards ORDER BY product_date DESC";
      $run = mysqli_query($conn, $sql);

      if (mysqli_num_rows($run) > 0) {
        while ($rows = mysqli_fetch_assoc($run)) {
          $rewardImage = !empty($rows['product_img'])
            ? "../productImg/" . $rows['product_img']
            : "../productImg/rewardPlaceholder.jpg"; // Placeholder image path
      ?>
          <div class="col">
            <div class="reward-card">
              <img src="<?php echo $rewardImage; ?>" alt="Reward" class="reward-img">
              <div class="reward-body">
                <h5><?= htmlspecialchars($rows['product_name']) ?></h5>
                <p><?= htmlspecialchars($rows['product_points']) ?></p>
                <p class="date"><?= date("m/d/Y", strtotime($rows['product_date'])) ?></p>
                <p><?= nl2br(htmlspecialchars($rows['product_description'])) ?></p>
                <div class="reward-actions">
                  <button type="button" class="btn btn-link read-more-btn"
                    data-title="<?= htmlspecialchars($rows['product_name']) ?>"
                    data-points="<?= htmlspecialchars($rows['product_points']) ?>"
                    data-date="<?= date("m/d/Y", strtotime($rows['product_date'])) ?>"
                    data-text="<?= htmlspecialchars($rows['product_description']) ?>"
                    data-image="<?= $rewardImage ?>">
                    Read More »
                  </button>
                  <a href="/capstoneweb/admin/pages/editreward.php?id=<?= $rows['reward_id'] ?>" class="btn btn-warning btn-sm" style="margin-left: auto;">
                    <i class="fa fa-edit"></i> Edit
                  </a>
                </div>
              </div>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p class='text-center'>No rewards yet.</p>";
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
  <script>
    // Handle Read More modal
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

    // Only show Read More button if text is truncated
    document.querySelectorAll('.reward-body p').forEach(textBlock => {
      const readMoreBtn = textBlock.closest('.reward-body').querySelector('.read-more-btn');
      if (readMoreBtn) {
        if (textBlock.scrollHeight > textBlock.offsetHeight) {
          readMoreBtn.style.display = "inline-block";
        } else {
          readMoreBtn.style.display = "none";
        }
      }
    });
  </script>

  <!-- Verify Password Modal -->
  <div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="">
          <input type="hidden" name="userid" value="<?= htmlspecialchars($_SESSION['userid']); ?>">
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

  <!-- toggle -->
   <script src="\capstoneweb/assets/sidebarToggle.js"></script>


</body>

</html>