<?php
require_once __DIR__ . '/../../includes/authSession.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>E-Recycle - Add New Reward</title>
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
            min-height: 100vh;
            width: 100%;
        }

        .announce-panel {
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            background: #ffffffff;
            border-radius: 10px;
        }

        .announce-panel h2 {
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: bold;
            color: #1A4314;
        }

        .announce-panel label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            text-align: left;
            color: #333;
        }

        .announce-panel input,
        .announce-panel textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .announce-panel input:focus,
        .announce-panel textarea:focus {
            outline: none;
            border-color: #1A4314;
            box-shadow: 0 0 5px rgba(26, 67, 20, 0.3);
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
            transition: background 0.3s;
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

        .close-btn:hover {
            color: #2C5E1A;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            text-align: left;
            display: none;
        }
  </style>
</head>
<body>
    <a href="/capstoneweb/admin/pages/reward.php?userid=<?=$userid?>" class="close-btn" aria-label="Close form">✖</a>

    <div class="announce-panel">
        <h2>Add New Reward</h2>

        <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data" id="rewardForm">
            <input type="hidden" name="userid" value="<?=$userid?>">

            <label for="product_name">Product:</label>
            <input type="text" class="form-control" style="text-transform: capitalize;" id="product_name" name="product_name" placeholder="Enter reward name" required aria-label="Reward name">

            <label for="product_description">Description:</label>
            <textarea class="form-control" style="text-transform: capitalize;" id="product_description" name="product_description" placeholder="Enter reward description" rows="4" required aria-label="Reward description"></textarea>

            <label for="product_points">Points Required:</label>
            <input type="number" class="form-control" id="product_points" name="product_points" placeholder="Enter points required" min="1" required aria-label="Points required">

            <label for="product_date">Date:</label>
            <input type="date" class="form-control" id="product_date" name="product_date" required aria-label="Reward date">

            <label for="product_img">Upload Image (optional):</label>
            <input type="file" class="form-control" id="product_img" name="product_img" accept="image/*" aria-label="Upload reward image">
            <div id="fileError" class="error-message">Please upload a valid image file (max 5MB).</div>

            <button type="submit_rewards" class="save-btn" name="submit_rewards">Post Reward</button>
        </form>
    </div>

    <script>
        document.getElementById('rewardForm').addEventListener('submit', function(event) {
            const fileInput = document.getElementById('product_img');
            const fileError = document.getElementById('fileError');
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (!validImageTypes.includes(file.type)) {
                    fileError.style.display = 'block';
                    fileError.textContent = 'Please upload a valid image file (JPEG, PNG, or GIF).';
                    event.preventDefault();
                    return;
                }

                if (file.size > maxSize) {
                    fileError.style.display = 'block';
                    fileError.textContent = 'Image size exceeds 5MB limit.';
                    event.preventDefault();
                    return;
                }
            }
        });
    </script>
</body>
</html>