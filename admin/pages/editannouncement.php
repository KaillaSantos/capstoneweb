<?php
require_once '../../includes/authSession.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('No announcement selected.'); window.location.href='../pages/announcement.php?userid=$userid';</script>";
    exit();
  }
  
  $announce_id = intval($_GET['id']); 
  
  $sql = "SELECT * FROM announcement WHERE announce_id = $announce_id LIMIT 1";
  $result = mysqli_query($conn, $sql);
  
  if (mysqli_num_rows($result) > 0) {
    $announcement = mysqli_fetch_assoc($result);
  } else {
    echo "<script>alert('Announcement not found.'); window.location.href='../pages/announcement.php?userid=$userid';</script>";
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>E-Recycle - Edit Announcement</title>
  <link rel="stylesheet" href="../user-admin.css">
  <link rel="stylesheet" href="../assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="../assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
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
            margin-top: 50px;   /* push it down from the close button */
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: bold;
            color: black;
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
            background: #111;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .announce-panel button.save-btn:hover {
            background: #575757;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            z-index: 100; /* stays above */
            text-decoration: none;
            color: #111;
        }
  </style>
</head>
<body>
    
    <a href="\capstoneweb\admin\pages\announcement.php?userid=<?=$userid?>" class="close-btn">✖</a>

    <div class="announce-panel">
    <h2 style="color:black; display:block; font-size:28px; font-weight:bold;">
        Edit Announcement Details
        </h2>

        <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data">
            <input type="hidden" name="announce_id" value="<?= $announcement['announce_id'] ?>">

            <label for="announce_name">Announcement Title:</label>
            <input type="text" class="form-control" name="announce_name" 
                value="<?= htmlspecialchars($announcement['announce_name']) ?>" required>

            <label for="announce_text">Announcement Body:</label>
            <textarea class="form-control" name="announce_text" rows="4" required><?= htmlspecialchars($announcement['announce_text']) ?></textarea>

            <label for="announce_date">Date:</label>
            <input type="date" class="form-control" name="announce_date" 
                value="<?= htmlspecialchars($announcement['announce_date']) ?>" required>

            <label for="announce_img">Upload Image (optional):</label>
            <input type="file" class="form-control" name="announce_img">

            <!-- Show current image -->
            <?php if (!empty($announcement['announce_img'])): ?>
                <label for="current_img">Current Image:</label>
                <img src="../announceImg/<?= htmlspecialchars($announcement['announce_img']) ?>" 
                    alt="Announcement Image" style="width:150px; height:auto; margin-bottom:10px; border: 2px solid black; border-radius: 8px;">
            <?php endif; ?>

            <button type="submit" class="save-btn" name="update_announcement">Update Announcement</button>
        </form>

    </div>

</body>
</html>
