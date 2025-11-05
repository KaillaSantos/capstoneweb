<?php
// --- Database Connection ---
$conn = new mysqli("localhost", "root", "", "capstone");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// --- Step 1: Check if user submitted the email ---
if (isset($_POST['find_email'])) {
    $email = trim($_POST['email']);
    $query = "SELECT * FROM account WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a secure token and expiry (1 hour)
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Store token in database
        $update = $conn->prepare("UPDATE account SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // Create password reset link
        $resetLink = "http://localhost/capstoneweb/reset_password.php?token=" . $token;

        // Email content
        $subject = "E-Recycle Password Reset Request";
        $body = "
            <p>Hello,</p>
            <p>We received a request to reset your password for your E-Recycle account.</p>
            <p>Please click the link below to reset your password:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>This link will expire in 1 hour. If you did not request a password reset, please ignore this message.</p>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: E-Recycle <no-reply@erecycle.com>" . "\r\n";

        // Send email
        if (mail($email, $subject, $body, $headers)) {
            $message = "<div class='alert alert-success'>
                            Password reset link sent to <strong>$email</strong>. 
                            Please check your inbox.
                        </div>";
        } else {
            $message = "<div class='alert alert-danger'>
                            Failed to send email. Please try again later.
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

                        <!-- Step 1: Enter Email -->
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
