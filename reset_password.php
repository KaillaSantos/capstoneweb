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
            } elseif (strlen($new_password) < 6) {
                $message = "<div class='alert alert-warning'>Password must be at least 6 characters.</div>";
            } else {
                // ⚠️ Password not encrypted (plain text)
                $hashed = $new_password;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Reset Password | E-Recycle</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css">
    <link rel="icon" type="image/x-icon" href="assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">  
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="Landing.css">
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">  
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

<div class="container mt-5" style="padding-top:250px;">
    <div class="col-md-5 mx-auto">
        <div class="card shadow" style="padding:35px;">
            <h3 class="mb-3 text-center">Reset Password</h3>
            <?= $message; ?>
            
            <?php if (isset($email) && time() <= $expiry && !isset($_POST['reset_password'])): ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" minlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                    </div>
                    <button type="submit" name="reset_password" class="btn btn-success w-100">Update Password</button>
                </form>
            <?php endif; ?>

            <p class="mt-3 text-center"><a href="login.php">Back to login</a></p>
        </div>
    </div>
</div>

<script>
  const menuToggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('nav');

  // Toggle mobile nav
  menuToggle.addEventListener('click', () => {
    nav.classList.toggle('active');
    menuToggle.classList.toggle('open');
  });

  // Close menu when clicking a link
  document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('active');
      menuToggle.classList.remove('open');
    });
  });

  // Header shrink on scroll
  window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (window.scrollY > 50) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });
</script>
</body>
</html>
