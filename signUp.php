<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="Landing.css">
  <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="icon" type="image/x-icon" href="assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <title>E-Recycle</title>

  <style>
    .password-strength {
      font-size: 0.9rem;
      margin-top: 5px;
    }

    .strength-weak {
      color: red;
    }

    .strength-medium {
      color: orange;
    }

    .strength-strong {
      color: green;
    }

    /* 👁️ Password visibility toggle styles */
    .password-container {
      position: relative;
    }

    .password-container i {
      position: absolute;
      right: 15px;
      padding-top: 20px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }

    .password-container i:hover {
      color: #198754;
    }
  </style>
</head>

<body>
  <header>
    <a href="test.php" class="logo">
      <img src="assets/logo_matimbubong.jpeg" alt=""> E-Recycle
    </a>

    <div class="menu-toggle">
      <div></div>
      <div></div>
      <div></div>
    </div>

    <nav>
      <a href="index.php#home">Home</a>
      <a href="index.php#front">Barangay</a>
      <a href="index.php#services">Services</a>
      <a href="index.php#vision-mission">Vision & Mission</a>
      <a href="index.php#contact">Contact</a>
    </nav>
  </header>

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

            <form method="post" action="/capstoneweb/function/function.php" id="signupForm" novalidate>
              <div class="mb-3">
                <label for="userName" class="form-label">Full Name</label>
                <input type="text" id="userName" name="userName" class="form-control" placeholder="ex: Juan Dela Cruz" required />
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="ex: juandelacruz@gmail.com" required>
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

              <div class="mb-3" id="purokContainer">
                <label for="purok" class="form-label d-block">Purok Number:</label>
                <select name="purok" id="purok" class="form-select">
                  <option value="" disabled selected>Select Purok</option>
                  <option value="1">Purok 1</option>
                  <option value="2">Purok 2</option>
                  <option value="3">Purok 3</option>
                  <option value="4">Purok 4</option>
                  <option value="5">Purok 5</option>
                  <option value="6">Purok 6</option>
                  <option value="7">Purok 7</option>
                </select>
                <input type="hidden" name="purok_hidden" id="purokHidden" value="">
              </div>

              <div class="mb-3 password-container">
                <label for="passWord" class="form-label">Password</label>
                <input type="password" id="passWord" name="passWord" class="form-control" placeholder="Password" required />
                <i class="bi bi-eye-slash" id="togglePassword"></i>
                <div class="password-strength" id="passwordStrength"></div>
                <div class="text-danger mt-1 d-none" id="passwordError">
                  Password must be at least 8 characters long, include one uppercase letter and one number.
                </div>
              </div>

              <div class="mb-3 password-container">
                <label for="rePassword" class="form-label">Re-enter Password</label>
                <input type="password" id="rePassword" name="rePassword" class="form-control" placeholder="Re-enter Password" required />
                <i class="bi bi-eye-slash" id="toggleRePassword"></i>
                <div class="text-danger mt-1 d-none" id="matchError">Passwords do not match.</div>
              </div>

              <button type="submit" name="signup" class="btn btn-success w-100">Register</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Role toggle logic
    const roleUser = document.getElementById('roleUser');
    const roleAdmin = document.getElementById('roleAdmin');
    const purokContainer = document.getElementById('purokContainer');
    const purokSelect = document.getElementById('purok');
    const purokHidden = document.getElementById('purokHidden');

    function togglePurok() {
      if (roleAdmin.checked) {
        purokContainer.style.display = 'none';
        purokSelect.removeAttribute('required');
        purokHidden.value = "";
      } else {
        purokContainer.style.display = 'block';
        purokSelect.setAttribute('required', 'required');
        purokHidden.value = "";
      }
    }

    document.addEventListener('DOMContentLoaded', togglePurok);
    roleUser.addEventListener('change', togglePurok);
    roleAdmin.addEventListener('change', togglePurok);

    // Password strength + validation
    const passwordInput = document.getElementById('passWord');
    const rePassword = document.getElementById('rePassword');
    const strengthText = document.getElementById('passwordStrength');
    const passwordError = document.getElementById('passwordError');
    const matchError = document.getElementById('matchError');
    const signupForm = document.getElementById('signupForm');

    passwordInput.addEventListener('input', () => {
      const val = passwordInput.value;
      let strength = 0;

      if (val.length >= 8) strength++;
      if (/[A-Z]/.test(val)) strength++;
      if (/[0-9]/.test(val)) strength++;

      if (strength === 0) {
        strengthText.textContent = '';
      } else if (strength === 1) {
        strengthText.textContent = 'Weak password';
        strengthText.className = 'password-strength strength-weak';
      } else if (strength === 2) {
        strengthText.textContent = 'Medium password';
        strengthText.className = 'password-strength strength-medium';
      } else {
        strengthText.textContent = 'Strong password';
        strengthText.className = 'password-strength strength-strong';
      }
    });

    signupForm.addEventListener('submit', (e) => {
      const password = passwordInput.value.trim();
      const confirm = rePassword.value.trim();
      const role = document.querySelector('input[name="role"]:checked').value;
      const passwordPattern = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
      let valid = true;

      if (!passwordPattern.test(password)) {
        passwordError.classList.remove('d-none');
        valid = false;
      } else {
        passwordError.classList.add('d-none');
      }

      if (password !== confirm) {
        matchError.classList.remove('d-none');
        valid = false;
      } else {
        matchError.classList.add('d-none');
      }

      if (role === 'user' && purokSelect.value === "") {
        alert("Please select a Purok.");
        valid = false;
      }

      if (!valid) e.preventDefault();
    });

    // Password visibility toggle
    function togglePasswordVisibility(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
      input.setAttribute('type', type);
      icon.classList.toggle('bi-eye');
      icon.classList.toggle('bi-eye-slash');
    }

    document.getElementById('togglePassword').addEventListener('click', () => {
      togglePasswordVisibility('passWord', 'togglePassword');
    });

    document.getElementById('toggleRePassword').addEventListener('click', () => {
      togglePasswordVisibility('rePassword', 'toggleRePassword');
    });

    // ✅ Navbar behavior
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('nav');
    menuToggle.addEventListener('click', () => {
      nav.classList.toggle('active');
      menuToggle.classList.toggle('open');
    });
    document.querySelectorAll('nav a').forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('active');
        menuToggle.classList.remove('open');
      });
    });
    window.addEventListener('scroll', () => {
      const header = document.querySelector('header');
      if (window.scrollY > 50) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
    });
  </script>
</body>

</html>
