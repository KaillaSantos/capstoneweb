<?php
header("Content-Type: application/json");
require '../conn/dbconn.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = mysqli_real_escape_string($conn, $_POST['passWord'] ?? '');

    if (empty($email) || empty($password)) {
        $response['success'] = false;
        $response['message'] = "All fields are required.";
    } else {
        $query = "SELECT userId, userName, email FROM account WHERE email='$email' AND passWord='$password'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            $response['success'] = true;
            $response['message'] = "Login successful!";
            $response['userId'] = $user['userId'];     // ✅ send back userId
            $response['userName'] = $user['userName']; // optional
            $response['email'] = $user['email'];       // optional
        } else {
            $response['success'] = false;
            $response['message'] = "Invalid email or password.";
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
?>
