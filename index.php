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
    <link rel="icon" type="image/x-icon" href="assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
    <title>E-Recycle</title>
    <link rel="stylesheet" href="test.css" />
    <style>
        html {
            scroll-behavior: smooth;
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
    <a href="#home">Home</a>
    <a href="#front">Barangay</a>
    <a href="#services">Services</a>
    <a href="#vision-mission">Vision & Mission</a>
    <a href="#contact">Contact</a>
  </nav>
</header>


  <!-- HOME SECTION -->
  <section id="home" class="home">
    <div class="home-content">
        <h1>Welcome to <span class="highlight">E-Recycle</span></h1>
      <h3>Your Partner in Sustainable Living</h3>
      <p>We provide eco-friendly solutions that help our community thrive while protecting our planet.</p>
      <a href="login.php" class="btn">Log in</a> 
        <a href="signUp.php" class="btn">Register Now!</a> 
    </div>
    <div class="home-img">
      <img src="assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png"" alt="Home Banner" />
    </div>
  </section>

  <!-- FRONT SECTION -->
  <section id="front">
    <h2>Our Matimbubong Barangay Hall(Tanggapan)</h2>
    <div class="front-img">
      <img src="assets/brgy hall.jpeg" alt="Front" />
    </div>
  </section>

  <!-- SERVICES SECTION -->
  <section id="services">
        <?php include 'includes/ServicesPage.php'; ?>
    </section>

  <!-- VISION & MISSION SECTION -->
  <section id="vision-mission">
    <h2>Barangay Vision and Mission</h2>
    <div class="vm-container">
      <div class="vision">
        <h2>Our Vision</h2>
        <p>
            Isang baranggay na kinikilala at mga mamamayang nagkakaisa sa pagpapaunlad sa pamayanan. 
            May masigla at maunlad na kalakalang pangkabuhayan may mga pasilidad na may  kumpletong kagamitan pang kagipitan. 
            May mga lingkod barangay na laging handa sa pagtugon sa anumang uri ng sakuna. may maayos malinis at may magandang kapaligiran may programang nakahanda para sa kagalingan ng kabataan at katandaan. 
            May mga pinuno ng iba't ibang sektor na nagtutulungan sumusunod sa mga itinakdang umiiral na batas at may takot sa diyos.
        </p>
      </div>
      <div class="mission">
        <h2>Our Mission</h2>
        <p>Makapagpasagawa ng mga epektibong programa para sa kabuhayan, kalusugan at kaunlaran. 
            Suportahan ang mga namumuhunang negosyante, upang mahikayat makiisa sa mga programang pangkaunlaran ng pamayanan. 
            Makapagpagawa ng mga bagong posibilidad ng lubhang kailangan sa panahon ng pandemya at mga kalamidad, mapanatili ang maayos, malinis na mga daang tubig na may luntiang kapaligiran na magbibigay ng mabuting kalusugan.
            <br><br>
            Maging isang huwarang pinuno na may takot sa diyos sa susunod at magpapatupad na lahat ng umiiral na batas ng Republika ng Pilipinas.</p>
      </div>
    </div>
  </section>

  <!-- CONTACT SECTION -->
  <section id="contact">
        <?php include 'includes/ContactPage.php'; ?>
    </section>

  <script>
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
