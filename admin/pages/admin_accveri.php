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


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Account Verification | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb\user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">  
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png"> 
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
            <h1>E-Recycle Account Verification</h1>
            <p>Municipality of San Ildefonso</p>
        </div>
        </div>

        <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
        </div>
    </header>

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
 <script src="/capstoneweb/assets/sidebarToggle.js"></script>


</body>
</html>
