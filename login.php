<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="Landing.css">
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">    
    <title>E-Recycle</title>

    <style>
        /* 👁️ Eye icon styling */
        .password-container {
            position: relative;
        }
        #togglePassword {
            position: absolute;
            right: 15px;
            padding-top: 20px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        #togglePassword:hover {
            color: #198754; /* Bootstrap green hover color */
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

    <div class="break" style="margin-top: 150px;"></div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
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
                            <div class="mb-1 password-container">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="passWord" name="passWord" class="form-control" placeholder="Password" required />
                                <i class="bi bi-eye-slash" id="togglePassword"></i>
                            </div>
                            <button type="submit" name="submit" class="btn btn-success w-100 mt-3">Log-in</button>
                        </form>
                        <p class="text-center mt-3">
                            Don't have an account? <a href="signUp.php" style="text-decoration: none;">Sign up here</a>
                        </p>
                        <p class="text-center mt-3">
                            <a href="recoverPassword.php" style="font-size: 12px; text-decoration: none;">Forget Password?</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fixed password toggle script -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('passWord');

        togglePassword.addEventListener('click', () => {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            togglePassword.classList.toggle('bi-eye');
            togglePassword.classList.toggle('bi-eye-slash');
        });
    </script>

    <!-- Responsive navigation -->
    <script>
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
