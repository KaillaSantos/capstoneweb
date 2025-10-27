<?php
require '../conn/dbconn.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="../Landing.css">
  <title>E-Recycle | Services</title>
  <style>
    /* --- Extra adjustments (minimal) --- */
    .services {
      background: #e8e5e4;
      color: black;
      padding: 6rem 9%;
      min-height: 100vh;
      text-align: center;
    }

    .services h1 {
      font-size: 4rem;
      font-weight: 700;
      color: green;
      margin-bottom: 3rem;
    }

    .services-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 3rem;
      align-items: stretch;
    }

    .service-box {
      background: #f8f8f8;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      transition: 0.3s ease;
    }

    .service-box:hover {
      transform: scale(1.03);
      box-shadow: 0 0 25px rgba(0,0,0,0.2);
    }

    .service-box i {
      font-size: 4rem;
      color: green;
      margin-bottom: 1rem;
    }

    .service-box h3 {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: black;
    }

    .service-box p {
      font-size: 1.4rem;
      color: #333;
    }

    .btn {
      margin-top: 2rem;
    }
  </style>
</head>

<body>
 

  <section class="services" id="services">
    <h1>Our Services</h1>

    <div class="services-container">
      <div class="service-box">
        <i class="fa-solid fa-recycle"></i>
        <h3>E-Waste Recycling</h3>
        <p>We collect and recycle your old electronics in an eco-friendly manner.</p>
        <a href="#" class="btn">Learn More</a>
      </div>

      <div class="service-box">
        <i class="fa-solid fa-leaf"></i>
        <h3>Community Clean-Up</h3>
        <p>Join our monthly clean-up drives and earn eco-points for participation.</p>
        <a href="#" class="btn">Join Us</a>
      </div>

      <div class="service-box">
        <i class="fa-solid fa-award"></i>
        <h3>Rewards</h3>
        <p>Get grocery goods as your reward for participating.</p>
        <a href="#" class="btn">Donate Now</a>
      </div>
    </div>
  </section>
</body>
</html>
