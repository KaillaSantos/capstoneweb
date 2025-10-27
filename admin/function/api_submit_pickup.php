<?php
require '../conn/dbconn.php';
session_start();

// For Android requests, we won’t rely on session user — we expect a `user_id` field
$userid     = $_POST['user_id'] ?? 0;
$address    = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
$pickupDate = mysqli_real_escape_string($conn, $_POST['pickup_date'] ?? '');
$pickupTime = mysqli_real_escape_string($conn, $_POST['pickup_time'] ?? '');
$imagePath  = "";

// ✅ Handle image upload
if (!empty($_FILES['pickup_img']['name'])) {
    $filename = time() . "_" . basename($_FILES['pickup_img']['name']);
    $tempname = $_FILES['pickup_img']['tmp_name'];
    $folder   = "../assets/pickups/" . $filename;

    if (move_uploaded_file($tempname, $folder)) {
        $imagePath = $filename;
    }
}

$insert = "INSERT INTO notifications (user_id, address, image_path, pickup_date, pickup_time, status) 
           VALUES ('$userid', '$imagePath', '$pickupDate', '$pickupTime', 'Pending')";

if (mysqli_query($conn, $insert)) {
    echo json_encode(["success" => true, "message" => "Pickup request submitted"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to submit pickup"]);
}
