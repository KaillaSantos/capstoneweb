<?php
require_once __DIR__ . '/../../conn/dbconn.php';
require_once __DIR__ . '/../../includes/authSession.php';

// verify password for account settings
if (isset($_POST['verify_submit'])) {
  if (!isset($userid)) {
    die("User ID not found in session");
  }

  $password = $_POST['verify_password'];

  $query = "SELECT password FROM account WHERE userid = '$userid'";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $storedPassword = $row['password'];

    $isValid = false;

    if (preg_match('/^\$2[ayb]\$|^\$argon2i\$|^\$argon2id\$/', $storedPassword)) {
      if (password_verify($password, $storedPassword)) {
        $isValid = true;
      }
    } else {
      if ($password === $storedPassword) {
        $isValid = true;
      }
    }

    if ($isValid) {
      // ✅ correct absolute path
      $target = 'http://' . $_SERVER['HTTP_HOST'] . '/capstoneweb/pages/user_accsetting.php?userid=' . urlencode($userid);
      header("Location: $target");
      exit;
    } else {
      echo "<script>alert('Incorrect password. Please try again.');</script>";
    }
  }
}
?>
