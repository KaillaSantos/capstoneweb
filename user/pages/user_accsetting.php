<?php
require_once __DIR__ . '/../../conn/dbconn.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Account Settings | E-Recycle</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="stylesheet" href="../../user-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="\capstoneweb\assets\Flag_of_San_Ildefonso_Bulacan.png">
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width: 100%;
        }

        .settings-panel {
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .settings-panel h2 {
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: bold;
        }

        .settings-panel label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            text-align: left;
        }

        .settings-panel input {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .settings-panel button.save-btn {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            border: none;
            background: #1A4314;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .settings-panel button.save-btn:hover {
            background: #2C5E1A;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 22px;
            background: none;
            border: none;
            cursor: pointer;
            color: #000;
            text-decoration: none;
        }
    </style>
</head>
<body>
<?php
       if(isset($_GET['userid'])) {
           $dataID = mysqli_real_escape_string($conn,$_GET['userid']);
           $query = "SELECT * FROM account WHERE userid = '$dataID'";
           $run = mysqli_query($conn,$query);
       
           $data = mysqli_num_rows($run);
           $rows = mysqli_fetch_assoc($run);
       ?>


  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle Button (visible on all screens) -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- Overlay (for mobile view) -->
  <div class="overlay"></div>

           <!-- Updated Close Button -->
           <button type="button" class="close-btn" onclick="window.history.back();">Cancel</button>
           
    <div class="settings-panel">
        <h2>Account Settings</h2>

        <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data">

            <input type="hidden" name="userid" value="<?= $rows['userid'] ?>">

            <label for="userimg">Upload Profile:</label>
            <input class="form-control" type="file" name="userimg">

            <label for="userName">Name:</label>
            <input type="text" name="userName" placeholder="Juan Dela Cruz" value="<?= $rows['userName'] ?>">

            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="juandelacruz@gmail.com" value="<?= $rows['email'] ?>">

            <label for="password">Password:</label>
            <div class="input-group mb-3">
                <input type="password" id="password" name="passWord" class="form-control" placeholder="Enter new password" value="<?= $rows['passWord'] ?>">
                <span class="input-group-text">
                    <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                </span>
            </div>

            <label for="repassword">Re-Enter your new Password:</label>
            <div class="input-group mb-3">
                <input type="password" id="repassword" name="rePassword" class="form-control" placeholder="Re-Enter your new password">
                <span class="input-group-text">
                    <i class="fa fa-eye" id="toggleRePassword" style="cursor: pointer;"></i>
                </span>
            </div>

            <button type="submit" class="save-btn" name="submitsetting">Save Changes</button>
        </form>
        <?php }  ?>
    </div>

    <script>
    // Toggle first password
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");
    togglePassword.addEventListener("click", function () {
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        this.classList.toggle("fa-eye-slash");
    });

    // Toggle re-enter password
    const toggleRePassword = document.querySelector("#toggleRePassword");
    const repassword = document.querySelector("#repassword");
    toggleRePassword.addEventListener("click", function () {
        const type = repassword.getAttribute("type") === "password" ? "text" : "password";
        repassword.setAttribute("type", type);
        this.classList.toggle("fa-eye-slash");
    });
</script>

 <script src="../assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>

 <!-- toggle -->
  <script src="../../assets/sidebarToggle.js"></script>

  <!-- 🔐 Password Verification Modal -->
  <div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="">
          <input type="hidden" name="redirect" value="/capstoneweb/admin/pages/accsetting.php">
          <div class="modal-header">
            <h5 class="modal-title" id="verifyPasswordModalLabel">Verify Your Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="verifyPassword" class="form-label">Enter Password</label>
              <input type="password" class="form-control" name="verify_password" id="verifyPassword" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="verify_submit" class="btn btn-primary">Verify</button>
          </div>
        </form>
      </div>
    </div>
  </div>


</body>
</html>
