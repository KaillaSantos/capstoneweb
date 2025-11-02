<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="Landing.css">
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="assets/logo_matimbubong.jpeg">
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
            <a href="#home">Home</a>
            <a href="#front">Barangay</a>
            <a href="#services">Services</a>
            <a href="#vision-mission">Vision & Mission</a>
            <a href="#contact">Contact</a>
        </nav>
    </header>


    <div class="break" style="margin-top: 150px;"></div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Log In</h2>
                        <?php
                        session_start();
                        if (isset($_SESSION['login_error'])) {
                            echo "<div class='alert alert-danger'>{$_SESSION['login_error']}</div>";
                            unset($_SESSION['login_error']);
                        }
                        ?>
                        <form method="post" action="/capstoneweb/function/function.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="E-mail" required />
                            </div>
                            <div class="mb-1">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="passWord" name="passWord" class="form-control" placeholder="Password" required />
                                <i class="bi bi-eye-slash" id="togglePassword"></i>
                            </div>
                            <button type="submit" name="submit" class="btn btn-success w-100 mt-3" > Log-in </button>
                        </form>
                        <p class="text-center mt-3">
                            Don't have an account? <a href="signUp.php" style="text-decoration: none;">Sign up here</a>
                        </p>
                        <p class ="text-center mt-3">
                            <a href="recoverPassword.php" style="font-size: 12px; text-decoration: none;">Recover Password</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password toggle functionality
        const togglePassword = (fieldId, iconId) => {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
            icon.classList.toggle('bi-eye-slash');
            icon.classList.toggle('bi-eye');
        };

        document.getElementById('togglePassword').addEventListener('click', () => {
            togglePassword('passWord', 'togglePassword');
        });

        document.getElementById('toggleRePassword').addEventListener('click', () => {
            togglePassword('rePassword', 'toggleRePassword');
        });

        // Real-time password validation
        document.addEventListener('DOMContentLoaded', () => {
            const passwordField = document.getElementById('passWord');
            const rePasswordField = document.getElementById('rePassword');
            const form = document.querySelector('form');

            // Real-time matching indicator
            rePasswordField.addEventListener('input', () => {
                if (passwordField.value !== rePasswordField.value) {
                    rePasswordField.setCustomValidity('Passwords do not match');
                } else {
                    rePasswordField.setCustomValidity('');
                }
            });

            // Form submission validation
            form.addEventListener('submit', (e) => {
                if (passwordField.value !== rePasswordField.value) {
                    e.preventDefault();
                    alert('Passwords do not match');
                    rePasswordField.focus();
                }
            });
        });

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