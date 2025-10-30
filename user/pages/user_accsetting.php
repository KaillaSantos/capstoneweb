<?php
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../includes/passwordVerification.php';
include __DIR__ . '/../includes/sidebar.php';

// ✅ Check session
if (!isset($_SESSION['userid'])) {
  echo "<script>alert('Unauthorized access. Please login.');
  window.location.href='../login.php';</script>";
  exit();
}

$userid = $_SESSION['userid'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Announcement | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="\capstoneweb/user-admin1.css">
  <link rel="stylesheet" href="user_announcement.css">
</head>

<body>

  <!-- ===== SIDEBAR ===== -->
  <?php include  '../includes/sidebar.php'; ?>

  <!-- ===== TOGGLE BUTTON ===== -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>


  <!-- Content -->
  <div class="content" id="content">
    <header class="dashboard-header">
      <div class="header-left">
        <img src="\capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>E-Recycle Announcement Page</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>
      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <!-- === ANNOUNCEMENT LAYOUT === -->
    <div class="announcement-layout">

      <!-- === Latest Announcement (Top 1) === -->
      <div class="latest-announcements">
        <h4 class="section-title">Latest Announcement</h4>
        <?php
        $sql = "SELECT * FROM announcement WHERE status='Posted' ORDER BY announce_date DESC";
        $run = mysqli_query($conn, $sql);

        if (mysqli_num_rows($run) > 0) {
          $latest = [];
          $previous = [];
          $count = 0;

          while ($row = mysqli_fetch_assoc($run)) {
            if ($count < 1) {
              $latest[] = $row;
            } else {
              $previous[] = $row;
            }
            $count++;
          }

          foreach ($latest as $rows) {
            $announceImage = !empty($rows['announce_img'])
              ? "../announceImg/" . $rows['announce_img']
              : "../announceImg/announcementPlaceholder.jpg";
        ?>
        <div class="announcement-card latest-full">
          <div class="announcement-header">
            <h2><?= htmlspecialchars($rows['announce_name']) ?></h2>
          </div>
          <img src="<?php echo $announceImage; ?>" alt="Announcement Image" class="announcement-banner">
          <div class="announcement-content">
            <p class="date"><i class="bi bi-calendar-event"></i> <?= date("F d, Y", strtotime($rows['announce_date'])) ?></p>
            <p class="announcement-text-full"><?= nl2br(htmlspecialchars($rows['announce_text'])) ?></p>
          </div>
        </div>

        <?php
          }
        } else {
          echo "<p>No announcements yet.</p>";
        }
        ?>
      </div>

      <!-- === Previous Announcements === -->
      <div class="previous-announcements">
        <h4 class="section-title">Previous Announcements</h4>
        <?php
        if (!empty($previous)) {
          foreach ($previous as $rows) {
            $announceImage = !empty($rows['announce_img'])
              ? "../announceImg/" . $rows['announce_img']
              : "../announceImg/announcementPlaceholder.jpg";
        ?>
        <div class="previous-announcement-card">
          <h6><?= htmlspecialchars($rows['announce_name']) ?></h6>
          <p class="date"><?= date("F d, Y", strtotime($rows['announce_date'])) ?></p>
          <p class="text-truncate"><?= substr(htmlspecialchars($rows['announce_text']), 0, 80) ?>...</p>
          <button type="button" class="btn btn-sm btn-link read-more-btn"
            data-title="<?= htmlspecialchars($rows['announce_name']) ?>"
            data-date="<?= date("m/d/Y", strtotime($rows['announce_date'])) ?>"
            data-text="<?= htmlspecialchars($rows['announce_text']) ?>"
            data-image="<?= $announceImage ?>">Read More »</button>
        </div>
        <?php
          }
        } else {
          echo "<p>No previous announcements.</p>";
        }
        ?>
      </div>

    </div>
  </div>


  <script src="../assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/sidebarToggle.js"></script>

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

  </script>

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
