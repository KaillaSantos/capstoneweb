<?php
require_once __DIR__ . '/../../includes/authSession.php';

// etch the user's full name based on logged-in session
$userQuery = "SELECT fullname FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $userQuery);
mysqli_stmt_bind_param($stmt, "i", $userid);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fullname);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Fetch recyclable materials
$query = "SELECT id, RM_name FROM recyclable ORDER BY id ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>E-Recycle - Redeem Materials</title>
    <link rel="stylesheet" href="../user-admin.css">
    <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="\capstoneweb\assets\Flag_of_San_Ildefonso_Bulacan.png">
    <link rel="stylesheet" href="../assets/fontawesome-free-7.0.1-web/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .redeem_panel {
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .redeem_panel h2 {
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: bold;
        }
        .redeem_panel label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            text-align: left;
        }
        .redeem_panel input,
        .redeem_panel textarea,
        .redeem_panel select {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .redeem_panel select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: #fff url('https://cdn-icons-png.flaticon.com/512/32/32195.png') no-repeat right 12px center;
            background-size: 16px;
            padding-right: 35px;
            cursor: pointer;
        }
        .redeem_panel button.save-btn {
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
        .redeem_panel button.save-btn:hover {
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
    <a href="/capstoneweb/admin/pages/record.php?userid=<?= $userid ?>" class="close-btn">✖</a>
    <div class="redeem_panel">
        <h2>Redeem Materials</h2>
        <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="record_name" class="form-label">Household Name:</label>
                <input type="text" name="record_name" class="form-control" placeholder="Juan Dela Cruz" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date:</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <?php
            $query = "SELECT * FROM recyclable ORDER BY id ASC";
            $res = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($res)) {
                $rid = $row['id'];
                $name = htmlspecialchars($row['RM_name']);
            ?>
                <div class="material-row">
                    <label style="flex:1;"><?= $name ?>:</label>
                    <input type="number" name="materials[<?= $rid ?>][quantity]" class="form-control d-inline" style="max-width:200px;" min="0" placeholder="0">
                    <select name="materials[<?= $rid ?>][unit]" class="form-control d-inline" style="max-width:200px;">
                        <option value="kg">kg</option>
                        <option value="pcs">pcs</option>
                    </select>
                </div>
            <?php } ?>
            <div class="mb-3">
                <label for="rec img" class="form-label">Upload Image (optional):</label>
                <input type="file" class="form-control" name="rec_img" accept="image/*">
            </div>
            <button type="submit" class="save-btn" name="submit_redeem">Submit</button>
        </form>
    </div>
</body>
</html>