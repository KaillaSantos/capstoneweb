<?php
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
            $message = "<div class='alert alert-danger'>This reset link has expired.</div>";
        } elseif (isset($_POST['reset_password'])) {
            $new_password = $_POST['new_password'];
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE account SET passWord = ?, reset_token = NULL, token_expiry = NULL WHERE email = ?");
            $update->bind_param("ss", $hashed, $email);
            $update->execute();

            $message = "<div class='alert alert-success'>Password updated successfully! <a href='login.php'>Log in now</a>.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Invalid or used reset link.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | E-Recycle</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Reset Password</h3>
                    <?= $message; ?>

                    <?php if (empty($message) || str_contains($message, 'expired') === false): ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" id="new_password" name="new_password" class="form-control" required>
                            </div>
                            <button type="submit" name="reset_password" class="btn btn-success w-100">Update Password</button>
                        </form>
                    <?php endif; ?>

                    <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
