<?php
require_once __DIR__ . '/../../conn/dbconn.php';

if (isset($_POST['verify_submit'])) {
  $password = $_POST['verify_password'];
  $userid = $_SESSION['userid'] ?? null;
  $redirect = $_POST['redirect'] ?? '/capstoneweb/superAdmin/pages/accsetting.php';

  if (!$userid) {
    echo "<script>alert('Session expired. Please login again.');
    window.location.href='/capstoneweb/admin/pages/login.php';</script>";
    exit();
  }

  $query = "SELECT password FROM account WHERE userid = '$userid'";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $storedPassword = $row['password'];
    $isValid = false;

    // Check hashed or plain
    if (preg_match('/^\$2[ayb]\$|^\$argon2i\$|^\$argon2id\$/', $storedPassword)) {
      $isValid = password_verify($password, $storedPassword);
    } else {
      $isValid = ($password === $storedPassword);
    }

    if ($isValid) {
      echo "<script>
        window.location.href = '{$redirect}?userid={$userid}';
      </script>";
      exit();
    } else {
      echo "<script>alert('Incorrect password. Please try again.');</script>";
    }
  } else {
    echo "<script>alert('User not found.');</script>";
  }
}
?>
