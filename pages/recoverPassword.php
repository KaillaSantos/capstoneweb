<?php
// --- Database Connection ---
$conn = new mysqli("localhost", "root", "", "capstone");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Step 1: Check if user submitted the email ---
$message = "";
if (isset($_POST['find_email'])) {
    $email = trim($_POST['email']);
    $query = "SELECT * FROM account WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-success'>Account found! You can now set a new password below.</div>";
        $show_reset_form = true;
    } else {
        $message = "<div class='alert alert-danger'>Email not found in our system.</div>";
    }
}

// --- Step 2: If user submitted new password ---
if (isset($_POST['reset_password'])) {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $hashed = $new_password; // optional: you can add password_hash($new_password, PASSWORD_DEFAULT)

    $query = "UPDATE account SET passWord = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $hashed, $email);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Password successfully updated! You can now <a href='login.php'>log in</a>.</div>";
        $show_reset_form = false;
    } else {
        $message = "<div class='alert alert-danger'>Error updating password. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password | E-Recycle</title>
    <link rel="icon" type="image/x-icon" href="../assets/Flag_of_San_Ildefonso_Bulacan.png">
    <link rel="stylesheet" href="../assets/bootstrap-5.3.7-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="/capstoneweb/Landing.css">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="header">
            <a href="login.php"><img src="../assets/logo_circle.jpeg" alt="" style="border-radius: 50%;"></a>
            <div class="nav-text">
                <h2>E-Recycle</h2>
            </div> 
            <nav>
                <a href="/capstoneweb/pages/LandingPage.php#home">Home</a>
                <a href="/capstoneweb/pages/LandingPage.php#services">Services</a>
                <a href="/capstoneweb/pages/LandingPage.php#contact">Contact</a>
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

                        <?php if (!isset($show_reset_form)) : ?>
                            <!-- Step 1: Enter Email -->
                            <form method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Enter your registered email</label>
                                    <input type="email" id="email" name="email" class="form-control" required placeholder="example@email.com">
                                </div>
                                <button type="submit" name="find_email" class="btn btn-success w-100">Find Account</button>
                            </form>
                        <?php endif; ?>

                        <?php if (isset($show_reset_form) && $show_reset_form): ?>
                            <!-- Step 2: Reset Password -->
                            <form method="post">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                                </div>
                                <button type="submit" name="reset_password" class="btn btn-success w-100">Update Password</button>
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
