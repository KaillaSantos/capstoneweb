<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Markazi+Text:wght@400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Landing.css">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="assets\assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
    <title>E-Recycle</title>
    <style>

        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body>
    <div class="header">
        <a href="login.php"><img src="assets/logo_matimbubong.jpeg" alt="" style="border-radius: 50%;"></a>
        <div class="nav-text">
            <h2>E-Recycle</h2>
        </div> 
        
        <nav>   
            <a href="#uphome">Home</a>
            <a href="#services">Services</a>
            <a href="#contact">Contact</a>
        </nav>
    </div>



    <!-- HOME SECTION -->
    <section id="#uphome" class="home">
        <div class="home-img">
            <img src="assets/E-Recycle Logo with Green and Blue Palette.png" alt="">
        </div>
        <div class="home-content">
            <h1>Welcome to <span style="color: green;">E-Recycle</span></h1>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Minus labore dolores esse. Odit similique doloribus tenetur doloremque, sunt commodi in ipsa repudiandae debitis deleniti blanditiis quibusdam quaerat neque asperiores ea.</p>
            <div class="social-icons">
                <a href="https://www.facebook.com/profile.php?id=61579544608124"><i class="fa-brands fa-facebook"></i></a>
            </div>
            <a href="login.php" class="btn">Log in</a> 
            <a href="signUp.php" class="btn">Register Now!</a> 
        </div>
    </section>

    <!-- SERVICES SECTION (included from PHP file) -->
    <section id="services">
        <?php include 'includes/ServicesPage.php'; ?>
    </section>

    <!-- CONTACT SECTION (included from PHP file) -->
    <section id="contact">
        <?php include 'includes/ContactPage.php'; ?>
    </section>

</body>
</html>
