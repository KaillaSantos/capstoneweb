<?php
require_once __DIR__ . '/../../includes/authSession.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>E-Recycle - New Announcement</title>
  <link rel="stylesheet" href="../user-admin.css">
  <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
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

        .announce-panel {
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .announce-panel h2 {
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: bold;
        }

        .announce-panel label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            text-align: left;
        }

        .announce-panel input,
        .announce-panel textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .announce-panel button.save-btn {
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

        .announce-panel button.save-btn:hover {
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
    
    <a href="\capstoneweb\superAdmin\pages\announcement.php?userid=<?=$userid?>" class="close-btn">✖</a>

    <div class="announce-panel">
        <h2>New Announcement</h2>

        <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data">
            <label for="announce_name">Announcement Title:</label>
            <input type="text" class="form-control" name="announce_name" placeholder="Enter title" required>

            <label for="announce_text">Announcement Body:</label>
            <textarea class="form-control" name="announce_text" rows="4" placeholder="Enter announcement" required></textarea>

            <label for="announce_date">Date:</label>
            <input type="date" class="form-control" name="announce_date" required>

            <label for="announce_img">Upload Image (optional):</label>
            <input type="file" class="form-control" name="announce_img">

            <button type="submit" class="save-btn" name="submit_announcement">Post Announcement</button>
        </form>
    </div>

</body>
</html>
