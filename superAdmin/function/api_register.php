<?php
header("Content-Type: application/json");
require '../conn/dbconn.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = mysqli_real_escape_string($conn, $_POST['userName'] ?? '');
    $email    = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = mysqli_real_escape_string($conn, $_POST['passWord'] ?? '');
    $confirm  = mysqli_real_escape_string($conn, $_POST['confirmPassword'] ?? '');

    if (empty($fullName) || empty($email) || empty($password) || empty($confirm)) {
        $response['success'] = false;
        $response['message'] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $response['success'] = false;
        $response['message'] = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM account WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $response['success'] = false;
            $response['message'] = "Email already registered.";
        } else {
            $insert = "INSERT INTO account (userName, email, passWord) VALUES ('$fullName', '$email', '$password')";
            if (mysqli_query($conn, $insert)) {
                $response['success'] = true;
                $response['message'] = "Account created successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = "Database error: " . mysqli_error($conn);
            }
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
