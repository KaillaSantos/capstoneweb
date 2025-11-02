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

           <form method="post" action="/capstoneweb/function/function.php">
  <div class="mb-3">
    <label for="userName" class="form-label">Full Name</label>
    <input type="text" id="userName" name="userName" class="form-control" placeholder="ex: Juan Dela Cruz" required />
  </div>

  <div class="mb-3">
    <label for="email">E-mail</label>
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
    <!-- Hidden field fallback for Admin -->
    <input type="hidden" name="purok_hidden" id="purokHidden" value="">
  </div>

  <div class="mb-3">
    <label for="passWord" class="form-label">Password</label>
    <input type="password" id="passWord" name="passWord" class="form-control" placeholder="Password" required />
  </div>

  <div class="mb-3">
    <label for="rePassword" class="form-label">Re-enter Password</label>
    <input type="password" id="rePassword" name="rePassword" class="form-control" placeholder="Re-enter Password" required />
  </div>

  <button type="submit" name="signup" class="btn btn-success w-100">Register</button>
</form>

<script>
const roleUser = document.getElementById('roleUser');
const roleAdmin = document.getElementById('roleAdmin');
const purokContainer = document.getElementById('purokContainer');
const purokSelect = document.getElementById('purok');
const purokHidden = document.getElementById('purokHidden');

function togglePurok() {
    if (roleAdmin.checked) {
        purokContainer.style.display = 'none';
        purokSelect.removeAttribute('required');
        purokHidden.value = ""; // ensures PHP receives a value
    } else {
        purokContainer.style.display = 'block';
        purokSelect.setAttribute('required', 'required');
        purokHidden.value = ""; // clear hidden fallback
    }
}

document.addEventListener('DOMContentLoaded', togglePurok);
roleUser.addEventListener('change', togglePurok);
roleAdmin.addEventListener('change', togglePurok);

 const menuToggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('nav');

  // Toggle mobile nav
  menuToggle.addEventListener('click', () => {
    nav.classList.toggle('active');
    menuToggle.classList.toggle('open');
  });

  // Close menu when clicking a link
  document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('active');
      menuToggle.classList.remove('open');
    });
  });

  // Header shrink on scroll
  window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (window.scrollY > 50) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });
</script>


</body>
</html>
