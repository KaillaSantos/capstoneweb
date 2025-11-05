<?php
// --- Database Connection ---
$conn = new mysqli("localhost", "root", "", "capstone");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$token = $_GET['token'] ?? '';

if ($token) {
    // --- Verify if token exists and is valid ---
    $stmt = $conn->prepare("SELECT email, token_expiry FROM account WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $email = $user['email'];
        $expiry = strtotime($user['token_expiry']);

        if (time() > $expiry) {
            // Token expired
            $message = "<div class='alert alert-danger text-center'>
                            This reset link has expired. Please request a new one.
                        </div>";
        } elseif (isset($_POST['reset_password'])) {
            // --- Update password securely ---
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);

            if ($new_password !== $confirm_password) {
                $message = "<div class='alert alert-danger text-center'>
                                Passwords do not match. Please try again.
                            </div>";
            } elseif (strlen($new_password) < 6) {
                $message = "<div class='alert alert-warning text-center'>
                                Password must be at least 6 characters long.
                            </div>";
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE account SET passWord = ?, reset_token = NULL, token_expiry = NULL WHERE email = ?");
                $update->bind_param("ss", $hashed, $email);

                if ($update->execute()) {
                    $message = "<div class='alert alert-success text-center'>
                                    Password updated successfully! Redirecting to login...
                                </div>";
                    // Redirect after 3 seconds
                    header("refresh:3;url=login.php");
                    exit;
                } else {
                    $message = "<div class='alert alert-danger text-center'>
                                    Something went wrong. Please try again.
                                </div>";
                }
            }
        }
    } else {
        // Invalid or used token
        $message = "<div class='alert alert-danger text-center'>
                        Invalid or used reset link.
                    </div>";
    }
} else {
    // Missing token
    $message = "<div class='alert alert-danger text-center'>
                    Missing or invalid password reset link.
                </div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | E-Recycle</title>
    <link rel="icon" type="image/x-icon" href="assets/Flag_of_San_Ildefonso_Bulacan.png">
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css">
    <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
        }
        .btn-success {
            background-color: #198754;
            border: none;
        }
        .btn-success:hover {
            background-color: #157347;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow p-4">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Reset Password</h3>
                        <?= $message; ?>

                        <?php if (isset($email) && time() <= $expiry && !isset($_POST['reset_password'])): ?>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6">
                                </div>
                                <button type="submit" name="reset_password" class="btn btn-success w-100">
                                    Update Password
                                </button>
                            </form>
                        <?php endif; ?>

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
