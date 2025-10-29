<?php
// print out error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// includes
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../../conn/dbconn.php';

// ✅ Check session early
if (!isset($_SESSION['userid'])) {
  echo "<script>alert('Unauthorized access. Please login.');
    window.location.href='../login.php';</script>";
  exit();
}

$userid = $_SESSION['userid']; // ✅ define first

// Fetch user info
$query = "SELECT * FROM account WHERE userid = '$userid'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// include helpers
require_once __DIR__ . '/../includes/passwordVerification.php';
require_once __DIR__ . '/../../includes/fetchData.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, maximum-scale=1.0">
  <title>Announcement | E-Recycle</title>
  <link rel="stylesheet" href="/capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="/capstoneweb/user-admin.css">
  <link rel="stylesheet" href="/capstoneweb/user-admin1.css">
</head>

<body>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle Button -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- Overlay -->
  <div class="overlay"></div>

  <!-- Content -->
  <div class="content" id="content">
    <header class="dashboard-header">
      <div class="header-left">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>E-Recycle Announcement Page</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>

      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <div style="display:flex; justify-content:flex-end; ">
      <form method="get" action="announcement.php" class="d-flex align-items-center mb-3">
        <select name="status" id="status_filter" class="form-select w-auto" onchange="this.form.submit()">
          <option value="Posted" <?= (isset($_GET['status']) && $_GET['status'] == 'Posted') ? 'selected' : '' ?>>All</option>
          <option value="Archived" <?= (isset($_GET['status']) && $_GET['status'] == 'Archived') ? 'selected' : '' ?>>Archived</option>
        </select>
      </form>

      <form method="post" action="announcement.php">
        <a href="/capstoneweb/admin/pages/newannounce.php" class="btn btn-md btn-success">
          <i class="fa fa-plus"></i> Add New Announcement
        </a>
        <button type="submit" class="btn btn-danger" name="archive_selected"><i class="fa-solid fa-box-archive"></i> Archive</button>
      </form>
    </div>

    <div class="announcement-container">
      <?php
      $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'Posted';

      if ($statusFilter === 'All') {
        $sql = "SELECT * FROM announcement ORDER BY announce_date DESC";
      } else {
        $sql = "SELECT * FROM announcement WHERE status = '$statusFilter' ORDER BY announce_date DESC";
      }
      $run = mysqli_query($conn, $sql);

      if (mysqli_num_rows($run) > 0) {
        while ($rows = mysqli_fetch_assoc($run)) {
          $announceImage = !empty($rows['announce_img'])
            ? "../announceImg/" . $rows['announce_img']
            : "../announceImg/announcementPlaceholder.jpg";
      ?>
          <div class="announcement-card">
            <input type="checkbox" name="archive_ids[]" value="<?= $rows['announce_id'] ?>">
            <img src="<?= $announceImage ?>" alt="Announcement" class="announcement-img" style="height: 150px;">
            <div class="announcement-body">
              <h2><?= htmlspecialchars($rows['announce_name']) ?></h2>
              <p class="date"><?= date("m/d/Y", strtotime($rows['announce_date'])) ?></p>
              <p class="announcement-text"><?= nl2br(htmlspecialchars($rows['announce_text'])) ?></p>

              <div class="announcement-actions">
                <button type="button" class="btn btn-link read-more-btn"
                  data-title="<?= htmlspecialchars($rows['announce_name']) ?>"
                  data-date="<?= date("m/d/Y", strtotime($rows['announce_date'])) ?>"
                  data-text="<?= htmlspecialchars($rows['announce_text']) ?>"
                  data-image="<?= $announceImage ?>">
                  Read More »
                </button>

                <a href="/capstoneweb/pages/editannouncement.php?id=<?= $rows['announce_id'] ?>"
                  class="btn btn-warning btn-sm">
                  <i class="fa fa-edit"></i> Edit
                </a>
              </div>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p>No announcements yet.</p>";
      }
      ?>
    </div>
  </div>

  <!-- 📜 Read More Modal -->
  <div class="modal fade" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="readMoreModalLabel">Announcement Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img id="modalImage" src="" class="img-fluid mb-3 rounded" alt="Announcement Image" style="height: 350px;">
          <h3 id="modalTitle"></h3>
          <p><strong>Date:</strong> <span id="modalDate"></span></p>
          <p id="modalText"></p>
        </div>
      </div>
    </div>
  </div>

  <!-- 🔐 Password Verification Modal -->
  <div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="">
          <input type="hidden" name="redirect" value="/capstoneweb/admin/pages/accsetting.php">
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

  <script src="/capstoneweb/assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
  <script src="/capstoneweb/assets/sidebarToggle.js"></script>

  <script>
    // Read More Modal handler
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

    // Dynamic redirect for password modal
    document.querySelectorAll('[data-bs-target="#verifyPasswordModal"]').forEach(btn => {
      btn.addEventListener('click', () => {
        const redirectInput = document.querySelector('#verifyPasswordModal input[name="redirect"]');
        if (btn.dataset.redirect) {
          redirectInput.value = btn.dataset.redirect;
        }
      });
    });
  </script>

</body>

</html>