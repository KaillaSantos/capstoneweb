<?php
// recoverPassword.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// DB connection
$conn = new mysqli("localhost", "root", "", "capstone");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

if (isset($_POST['find_email'])) {
    $email = trim($_POST['email']);

    // check existence
    $query = "SELECT userid FROM account WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // create token and expiry
        $token = bin2hex(random_bytes(32)); // 64 chars
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // save token
        $update = $conn->prepare("UPDATE account SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // build reset link (use your real domain in production)
        $resetLink = "http://localhost/capstoneweb/reset_password.php?token=" . $token;

        // email contents
        $subject = "E-Recycle Password Reset Request";
        $body = "
            <p>Hello,</p>
            <p>We received a request to reset your password for your <strong>E-Recycle</strong> account.</p>
            <p>Click the link to reset your password (valid 1 hour):</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>If you didn't request this, ignore this email.</p>
            <p>— E-Recycle Team</p>
        ";

        // send via PHPMailer + Gmail app password
$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 0; // 2 = verbose debug output
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sanchez.aquino.092@gmail.com';     // <-- your Gmail account
    $mail->Password = 'vpna fpbs kwsl unll';              // <-- your Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // --- Send to the user first ---
    $mail->setFrom('sanchez.aquino.092@gmail.com', 'E-Recycle Support');
    $mail->addAddress($email); // user’s email
    $mail->isHTML(true);
    $mail->Subject = "E-Recycle Password Reset Request";
    $mail->Body = "
        <p>Hello,</p>
        <p>We received a request to reset your password for your E-Recycle account.</p>
        <p>Please click the link below to reset your password:</p>
        <p><a href='$resetLink'>$resetLink</a></p>
        <p>This link will expire in 1 hour. If you did not request a password reset, please ignore this message.</p>
    ";
    $mail->send();

    // --- Notify all admins and superAdmin ---
    $adminQuery = $conn->query("SELECT email FROM account WHERE role IN ('admin', 'superAdmin')");
    while ($admin = $adminQuery->fetch_assoc()) {
        $mail->clearAddresses();
        $mail->addAddress($admin['email']);
        $mail->Subject = "Password Reset Requested for $email";
        $mail->Body = "
            <p>User <strong>$email</strong> requested a password reset.</p>
            <p>If this was unexpected, please verify the user activity in your admin dashboard.</p>
        ";
        $mail->send();
    }

    $message = "<div class='alert alert-success'>
                    Reset link sent to <strong>$email</strong>. Admins notified as well.
                </div>";

} catch (Exception $e) {
    $message = "<div class='alert alert-danger'>
                    Failed to send email. Mailer Error: {$mail->ErrorInfo}
                </div>";
}
    }
}
?>
<!-- (keep your existing HTML form here) -->
  <!DOCTYPE html>
  <html lang="en">
  <head>  
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Recover Password | E-Recycle</title>
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
  <div class="container mt-5" style="padding-top:250px">
    <div class="col-md-5 mx-auto">
      <div class="card shadow" style="padding:35px">
        <h3 class="mb-3" style="align-content:center;">Recover Password</h3>
        <?= $message; ?>
        <form method="post">
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required placeholder="you@example.com">
          </div>
          <button class="btn btn-success w-100" name="find_email">Send Reset Link</button>
        </form>
        <p class="mt-3"><a href="login.php">Back to login</a></p>
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