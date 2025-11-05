<?php
require_once __DIR__ . '/../../includes/authSession.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('No reward selected.'); window.location.href='../pages/reward.php?userid=$userid';</script>";
    exit();
}

$reward_id = intval($_GET['id']);

$sql = "SELECT * FROM rewards WHERE reward_id = $reward_id LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $reward = mysqli_fetch_assoc($result);
} else {
    echo "<script>alert('Reward not found.'); window.location.href='../pages/reward.php?userid=$userid';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>E-Recycle - Edit Reward</title>
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

        .reward-panel {
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .reward-panel h2 {
            margin-top: 50px;   /* push it down from the close button */
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: bold;
            color: black;
        }

        .reward-panel label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            text-align: left;
        }

        .reward-panel input,
        .reward-panel textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .reward-panel button.save-btn {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            border: none;
            background: #1A4314;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .reward-panel button.save-btn:hover {
            background: #2C5E1A;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 22px;
            z-index: 100; /* stays above */
            text-decoration: none;
            color: #111;
        }
  </style>
</head>
<body>
    
    <a href="\capstoneweb\superAdmin\pages\reward.php?userid=<?=$userid?>" class="close-btn">✖</a>

    <div class="reward-panel">
    <h2 style="color:black; display:block; font-size:28px; font-weight:bold;">
        Edit Reward Details
        </h2>

        <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data">
            <input type="hidden" name="reward_id" value="<?= $reward['reward_id'] ?>">

            <label for="product_name">Reward Title:</label>
            <input type="text" class="form-control" style="text-transform: capitalize;" name="product_name" 
                value="<?= htmlspecialchars($reward['product_name']) ?>" required>

            <label for="product_description">Reward Description:</label>
            <textarea class="form-control" style="text-transform: capitalize;" name="product_description" rows="2" required><?= htmlspecialchars($reward['product_description']) ?></textarea>

            <label for="product_points">Points:</label>
            <input type="number" class="form-control" name="product_points" 
                value="<?= htmlspecialchars($reward['product_points']) ?>" required>

            <label for="product_date">Date:</label>
            <input type="date" class="form-control" name="product_date" 
                value="<?= htmlspecialchars($reward['product_date']) ?>" required>

            <label for="product_img">Upload Image (optional):</label>
            <input type="file" class="form-control" name="product_img">

            <!-- Show current image -->
            <?php if (!empty($reward['product_img'])): ?>
                <label for="current_img">Current Image:</label>
                <img src="/capstoneweb/uploads/productImg/<?= htmlspecialchars($reward['product_img']) ?>" 
                    alt="Reward Image" style="width:150px; height:auto; margin-bottom:10px; border: 2px solid black; border-radius: 8px;">
            <?php endif; ?>

            <button type="submit" class="save-btn" name="update_reward">Update Reward</button>
        </form>

    </div>

</body>
</html>