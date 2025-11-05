<?php
// reset_password.php
$conn = new mysqli("localhost", "root", "", "capstone");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";
$token = $_GET['token'] ?? '';

if ($token) {
    $stmt = $conn->prepare("SELECT email, token_expiry FROM account WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $email = $user['email'];
        $expiry = strtotime($user['token_expiry']);

        if (time() > $expiry) {
            $message = "<div class='alert alert-danger'>This reset link has expired. Please request a new one.</div>";
        } elseif (isset($_POST['reset_password'])) {
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);

            if ($new_password !== $confirm_password) {
                $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
            } elseif (strlen($new_password) < 8) {
                $message = "<div class='alert alert-warning'>Password must be at least 8 characters.</div>";
            } else {
                $hashed = $new_password; // Keeping your original logic (not encrypted)
                $update = $conn->prepare("UPDATE account SET passWord = ?, reset_token = NULL, token_expiry = NULL WHERE email = ?");
                $update->bind_param("ss", $hashed, $email);

                if ($update->execute()) {
                    $message = "<div class='alert alert-success'>Password updated successfully! Redirecting to login...</div>";
                    header("refresh:3;url=login.php");
                    exit;
                } else {
                    $message = "<div class='alert alert-danger'>Error updating password. Try again.</div>";
                }
            }
        }
    } else {
        $message = "<div class='alert alert-danger'>Invalid or used reset link.</div>";
    }
} else {
    $message = "<div class='alert alert-danger'>Missing or invalid reset link.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Reset Password | E-Recycle</title>
  <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css">
  <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="Landing.css">
  <link rel="icon" type="image/x-icon" href="assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">  
  <style>
    .password-wrapper {
      position: relative;
    }
    .password-wrapper i {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }
    .card {
      padding: 35px;
      border-radius: 15px;
    }
  </style>
</head>

<body>
<header>
  <a href="test.php" class="logo">
    <img src="assets/logo_matimbubong.jpeg" alt=""> E-Recycle
  </a>

  <div class="menu-toggle">
    <div></div>
    <div></div>
    <div></div>
  </div>

  <nav>
    <a href="index.php#home">Home</a>
    <a href="index.php#front">Barangay</a>
    <a href="index.php#services">Services</a>
    <a href="index.php#vision-mission">Vision & Mission</a>
    <a href="index.php#contact">Contact</a>
  </nav>
</header>

<div class="container mt-5" style="padding-top:200px;">
  <div class="col-md-5 mx-auto">
    <div class="card shadow">
      <h3 class="mb-3 text-center">Reset Password</h3>
      <?= $message; ?>
      
      <?php if (isset($email) && time() <= $expiry && !isset($_POST['reset_password'])): ?>
        <form method="post">
          <div class="mb-3 password-wrapper">
            <label class="form-label">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" minlength="8" required>
            <i class="bi bi-eye-slash" id="toggleNewPassword"></i>
          </div>
          <div class="mb-3 password-wrapper">
            <label>Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="8" required>
            <i class="bi bi-eye-slash" id="toggleConfirmPassword"></i>
          </div>
          <button type="submit" name="reset_password" class="btn btn-success w-100">Update Password</button>
        </form>
      <?php endif; ?>

      <p class="mt-3 text-center"><a href="login.php">Back to login</a></p>
    </div>
  </div>
</div>

<script>
  // Eye toggle logic
  function togglePassword(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    const type = field.type === "password" ? "text" : "password";
    field.type = type;
    icon.classList.toggle("bi-eye");
    icon.classList.toggle("bi-eye-slash");
  }

  document.getElementById("toggleNewPassword").addEventListener("click", () => {
    togglePassword("new_password", "toggleNewPassword");
  });
  document.getElementById("toggleConfirmPassword").addEventListener("click", () => {
    togglePassword("confirm_password", "toggleConfirmPassword");
  });

  // Menu toggle
  const menuToggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('nav');
  menuToggle.addEventListener('click', () => {
    nav.classList.toggle('active');
    menuToggle.classList.toggle('open');
  });
  document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('active');
      menuToggle.classList.remove('open');
    });
  });
  window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (window.scrollY > 50) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });
</script>
</body>
</html>
