<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="stylesheet" href="../Landing.css">
  <link rel="stylesheet" href="../assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="icon" type="image/x-icon" href="\capstoneweb\assets\Flag_of_San_Ildefonso_Bulacan.png">
  <link rel="stylesheet" href="../assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <title>E-Recycle</title>
</head>

<body>
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

  <div class="break" style="margin-top: 120px;"></div>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">Sign Up</h2>
            <?php
            session_start();
            if (isset($_SESSION['registerError'])) {
              echo "
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    {$_SESSION['registerError']}
                 </div>";
              unset($_SESSION['registerError']);
            }
            ?>

            <form method="post" action="../function/function.php">
              <div class="mb-3">
                <label for="username" class="form-label">Full Name</label>
                <input type="text" id="userName" name="userName" class="form-control" placeholder="Username" required />
              </div>
              <div class="mb-3">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="E-mail" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="passWord" name="passWord" class="form-control" placeholder="Password" required />
                <i class="bi bi-eye-slash" id="togglePassword"></i>
              </div>
              <div class="mb-3">
                <label for="rePassword" class="form-label">Re-enter Password</label>
                <input type="password" id="rePassword" name="rePassword" class="form-control" placeholder="Re-enter Password" required />
                <i class="bi bi-eye-slash" id="toggleRePassword"></i>
              </div>
             <div class="mb-3">
              <label for="role" class="form-label d-block">Select Role</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="role" id="roleUser" value="user" checked required>
                <label class="form-check-label" for="roleUser">Household User</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin" required>
                <label class="form-check-label" for="roleAdmin">Admin</label>
              </div>
            </div>
              <button type="submit" name="signup" class="btn btn-success w-100">Register</button>
            </form>

            <p class="text-center mt-3">
              Already have an account? <a href="login.php" style="text-decoration: none;">Sign in here</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../function/js/loginEyeToggle.js"></script>

</body>

</html>