
<?php

require_once __DIR__ . '/../../conn/dbconn.php';
require __DIR__ . '/../../function/function.php';
require_once __DIR__ . '/../includes/passwordVerification.php';



// ✅ Check session
if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Unauthorized access. Please login.');
    window.location.href='../login.php';</script>";
    exit();
}

$userid = $_SESSION['userid'];

// ✅ Fetch user info (profile)
$query = "SELECT * FROM account WHERE userid = '$userid'";
$run = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($run);

// ✅ Handle Approve/Reject request
if (isset($_POST['update_status'])) {
    $notif_id = intval($_POST['notif_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);

    $updateQuery = "UPDATE notifications SET status='$new_status' WHERE id='$notif_id'";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Status updated to $new_status');window.location.href='notification.php';</script>";
    } else {
        echo "<script>alert('Error updating status');</script>";
    }
}

// ✅ Fetch notifications
$notifications = getNotifications($conn, $userid);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Notification | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb\user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">  
  <link rel="icon" type="image/x-icon" href="assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png"> 
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
    <?php include '../includes/header.php'; ?>

    <?php if (!empty($notifications)): ?>
      <?php foreach ($notifications as $notif): ?>
        <div class="notification-card">
          <p><strong>Request Date:</strong> <?= htmlspecialchars($notif['pickup_date']) ?> at <?= htmlspecialchars($notif['pickup_time']) ?></p>
          <?php if (!empty($notif['image_path'])): ?>
            <p><strong>Proof Image:</strong></p>
            <img src="/capstoneweb/assets/pickups/<?= htmlspecialchars($notif['image_path']) ?>" alt="pickup image">
          <?php endif; ?>

          <p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($notif['status']) ?></span></p>
          <p><em>Submitted: <?= htmlspecialchars($notif['created_at']) ?></em></p>

          <!-- Approve/Reject buttons -->
          <form method="POST" style="margin-top:10px;">
            <input type="hidden" name="notif_id" value="<?= $notif['id'] ?>">
            <button type="submit" name="update_status" value="1" class="btn btn-success btn-sm btn-action" 
              onclick="this.form.new_status.value='Approved'">Approve</button>
            <button type="submit" name="update_status" value="1" class="btn btn-danger btn-sm" 
              onclick="this.form.new_status.value='Rejected'">Reject</button>
            <input type="hidden" name="new_status" value="">
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No notifications yet.</p>
    <?php endif; ?>
  </div>

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

<script src="../assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>

<!-- toggle -->
<script src="../assets/sidebarToggle.js"></script>

</body>
</html>
