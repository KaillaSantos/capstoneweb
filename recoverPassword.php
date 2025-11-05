<?php
// --- Database Connection ---
$conn = new mysqli("localhost", "root", "", "capstone");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$message = "";

// --- Step 1: Check if user submitted the email ---
if (isset($_POST['find_email'])) {
    $email = trim($_POST['email']);

    // Check if email exists in the account table
    $query = "SELECT * FROM account WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate secure token and expiry (1 hour)
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Store token and expiry in database
        $update = $conn->prepare("UPDATE account SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // Password reset link
        $resetLink = "http://localhost/capstoneweb/reset_password.php?token=" . $token;

        // Email content
        $subject = "E-Recycle Password Reset Request";
        $body = "
            <p>Hello,</p>
            <p>We received a request to reset your password for your <strong>E-Recycle</strong> account.</p>
            <p>Please click the link below to reset your password:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>This link will expire in 1 hour. If you did not request this, please ignore this email.</p>
            <br>
            <p>— E-Recycle Team</p>
        ";

        // --- Send Email using PHPMailer ---
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'yourgmail@gmail.com'; // Replace with your Gmail
            $mail->Password = 'your_app_password';   // Use Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('no-reply@erecycle.com', 'E-Recycle');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();

            // --- Optional: Notify the Admin ---
            $adminEmail = "admin@erecycle.com"; // Replace with real admin email
            $adminSubject = "Password Reset Requested";
            $adminBody = "
                <p>User with email <strong>$email</strong> requested a password reset on E-Recycle.</p>
            ";

            $mail->clearAddresses();
            $mail->addAddress($adminEmail);
            $mail->Subject = $adminSubject;
            $mail->Body = $adminBody;
            $mail->send();

            $message = "<div class='alert alert-success'>
                            Password reset link sent to <strong>$email</strong>. 
                            Please check your inbox.
                        </div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>
                            Failed to send email. Mailer Error: {$mail->ErrorInfo}
                        </div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Email not found in our system.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password | E-Recycle</title>
    <link rel="icon" type="image/x-icon" href="assets/Flag_of_San_Ildefonso_Bulacan.png">
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css">
    <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="Landing.css">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="header">
            <a href="login.php"><img src="assets/logo_circle.jpeg" alt="" style="border-radius: 50%;"></a>
            <div class="nav-text">
                <h2>E-Recycle</h2>
            </div> 
            <nav>
                <a href="/capstoneweb/index.php#home">Home</a>
                <a href="/capstoneweb/index.php#services">Services</a>
                <a href="/capstoneweb/index.php#contact">Contact</a>
            </nav>
        </div>
    </header>

    <div class="break" style="margin-top: 200px;"></div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Recover Password</h3>
                        <?= $message; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Enter your registered email</label>
                                <input type="email" id="email" name="email" class="form-control" required placeholder="example@email.com">
                            </div>
                            <button type="submit" name="find_email" class="btn btn-success w-100">Send Reset Link</button>
                        </form>

                        <p class="text-center mt-3">
                            <a href="login.php" style="text-decoration: none;">Back to Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
