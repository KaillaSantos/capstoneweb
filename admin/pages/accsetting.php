    <?php
    require_once __DIR__ . '/../../conn/dbconn.php';
    require_once __DIR__ . '/../../includes/authSession.php';
    require_once __DIR__ . '/../includes/passwordVerification.php';

    // ✅ Check session
    if (!isset($_SESSION['userid'])) {
      echo "<script>alert('Unauthorized access. Please login.');
        window.location.href='../login.php';</script>";
      exit();
    }

    $userid = $_SESSION['userid'];
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
      <title>Account Settings | E-Recycle</title>
      <link rel="stylesheet" href="\capstoneweb\user-admin.css">
      <link rel="stylesheet" href="\capstoneweb/user-admin1.css">
      <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
      <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
      <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
      <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
/* === Profile Container === */
.profile-container {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f5f6f7;
  padding: 40px 20px;
}

/* === Profile Card === */
.profile-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  padding: 40px;
  width: 100%;
  max-width: 850px;
  display: flex;
  align-items: flex-start;
  gap: 50px;
}

/* === Left Section (Profile Image) === */
.profile-left {
  flex: 1;
  text-align: center;
}

.profile-img-wrapper {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  overflow: hidden;
  margin: 0 auto 10px;
  border: 3px solid #2c5e1a;
}

.profile-img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-left input[type="file"] {
  display: block;
  margin: 10px auto;
  font-size: 14px;
}

.profile-left small {
  color: #777;
  font-size: 12px;
}

/* === Right Section (Form Fields) === */
.profile-right {
  flex: 1.5;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 6px;
  display: block;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

/* === Eye Icon for Password Toggle === */
.toggle-password {
  position: absolute;
  right: 15px;
  top: 38px;
  cursor: pointer;
  color: #555;
}

/* === Buttons === */
.form-buttons {
  display: flex;
  gap: 15px;
  margin-top: 25px;
}

.save-btn {
  background-color: #1a4314;
  color: white;
  border: none;
  padding: 12px 28px;
  border-radius: 6px;
  font-size: 15px;
  font-weight: 600;
  transition: background 0.3s;
}

.save-btn:hover {
  background-color: #2c5e1a;
}

.cancel-btn {
  background-color: #ccc;
  color: #333;
  border: none;
  padding: 12px 28px;
  border-radius: 6px;
  font-size: 15px;
  font-weight: 600;
  transition: background 0.3s;
}

.cancel-btn:hover {
  background-color: #999;
  color: white;
}
      </style>

    </head>

    <body>
      <?php
      if (isset($_GET['userid'])) {
        $dataID = mysqli_real_escape_string($conn, $_GET['userid']);
        $query = "SELECT * FROM account WHERE userid = '$dataID'";
        $run = mysqli_query($conn, $query);

        $data = mysqli_num_rows($run);
        $rows = mysqli_fetch_assoc($run);

        $userImage = !empty($rows['userimg'])
          ? "/capstoneweb/image/" . $rows['userimg']
          : "/capstoneweb/image/placeholder.jpg";

      ?>


        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Sidebar Toggle Button (visible on all screens) -->
        <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

        <!-- Overlay (for mobile view) -->
        <div class="overlay"></div>

        <div class="content">
          <header class="dashboard-header">
            <div class="header-left">
              <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
              <div class="header-text">
                <h1>E-Recycle Account Settings</h1>
                <p>Municipality of San Ildefonso</p>
              </div>
            </div>

            <div class="header-right">
              <span class="date-display"><?php echo date("F j, Y"); ?></span>
            </div>
          </header>

          <div class="settings-panel">

            <!-- Profile Card -->
            <div class="profile-container">
              <div class="profile-card">
                <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data">
                  
                  <!-- Profile Image Section -->
                  <div class="profile-left">
                    <div class="profile-img-wrapper">
                      <img id="profilePreview" src="<?= $userImage ?>" alt="Profile Image">
                    </div>
                    <input type="file" id="uploadProfile" name="userimg" accept="image/*" onchange="previewImage(event)">
                    <small>Allowed types: jpg, png | Max: 5MB</small>
                  </div>
                
                
                  <!-- Form Section -->
                  <div class="profile-right">
                      <input type="hidden" name="userid" value="<?= $rows['userid'] ?>">

                      <div class="form-group">
                        <label for="userName">Full Name:</label>
                        <input type="text" name="userName" value="<?= $rows['userName'] ?>" placeholder="Enter full name">
                      </div>

                      <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?= $rows['email'] ?>" placeholder="Enter email">
                      </div>

                      <div class="form-group position-relative">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="passWord" value="<?= $rows['passWord'] ?>" placeholder="Enter password">
                        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                      </div>

                      <div class="form-group position-relative">
                        <label for="repassword">Re-enter Password:</label>
                        <input type="password" id="repassword" name="rePassword" placeholder="Re-enter password">
                        <i class="fa-solid fa-eye toggle-password" id="toggleRePassword"></i>
                      </div>

                      <div class="form-buttons">
                        <button type="submit" class="save-btn" name="adminsetting">Save Changes</button>
                        <button type="button" class="cancel-btn" onclick="cancelChanges()">Cancel</button>
                      </div>
                  </div>
              </form>
            </div>
          </div>
        <?php }  ?>
        </div>
        </div>

        <script>
          // Handle all toggle-password icons
          document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', () => {
              const input = icon.previousElementSibling;
              const isPassword = input.type === 'password';
              input.type = isPassword ? 'text' : 'password';
              icon.classList.toggle('fa-eye-slash', isPassword);
            });
          });

          //Profile Image Preview
          function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
              const output = document.getElementById('profilePreview');
              output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
          }

          // Cancel button → confirm and redirect or reset
          function cancelChanges() {
            const confirmCancel = confirm("Are you sure you want to cancel your changes?");
            if (confirmCancel) {
              window.history.back();
            }
          }
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