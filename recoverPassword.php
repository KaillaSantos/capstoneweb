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
            $mail->SMTPDebug = 0; // set 2 for troubleshooting
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sanchez.aquino.092@gmail.com';            // <-- replace
            $mail->Password = 'vpna fpbs kwsl unll';      // <-- replace with app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('yourgmail@gmail.com', 'E-Recycle');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();

            // optional admin notification
            try {
                $adminEmail = 'erecyclematimbubong@gmail.com'; // replace with real admin email
                $mail->clearAddresses();
                $mail->addAddress($adminEmail);
                $mail->Subject = "Password reset requested for $email";
                $mail->Body = "<p>User <strong>$email</strong> requested a password reset.</p>";
                $mail->send();
            } catch (Exception $ex) {
                // admin notification failed — nonfatal
            }

            $message = "<div class='alert alert-success'>Reset link sent to <strong>$email</strong>. Check your inbox.</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>Failed to send email. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Email not found in our system.</div>";
    }
}
?>
<!-- (keep your existing HTML form here) -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Recover Password | E-Recycle</title>
<link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css">
</head>
<body>
<div class="container mt-5">
  <div class="col-md-5 mx-auto">
    <div class="card p-4">
      <h3 class="mb-3">Recover Password</h3>
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
</body>
</html>
