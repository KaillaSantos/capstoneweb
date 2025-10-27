<?php
require_once __DIR__ . '/../../conn/dbconn.php';
require_once __DIR__ . '/../../includes/authSession.php';

if (isset($_POST['verify_submit'])) {
  $password = $_POST['verify_password'];

  $query = "SELECT password FROM account WHERE userid = '$userid'";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $storedPassword = $row['password'];

    $isValid = false;

    // Case 1: Hashed password
    if (preg_match('/^\$2[ayb]\$|^\$argon2i\$|^\$argon2id\$/', $storedPassword)) {
      if (password_verify($password, $storedPassword)) {
        $isValid = true;
      }
    } else {
      // Case 2: Plain text
      if ($password === $storedPassword) {
        $isValid = true;
      }
    }

    if ($isValid) {
      echo "<script>
        window.location.href = '/capstoneweb/admin/pages/accsetting.php?userid={$userid}';
      </script>";
      exit();
    } else {
      echo "<script>alert('Incorrect password. Please try again.');</script>";
    }
  }
}
?>
