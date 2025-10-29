<?php
session_start();
require '../../conn/dbconn.php';

if (isset($_POST['reward_id'])) {
    $reward_id = $_POST['reward_id'];
    $user_id = $_SESSION['userid'];

    // Check if already redeemed
    $check = $conn->query("SELECT * FROM user_rewards WHERE user_id='$user_id' AND reward_id='$reward_id'");
    if ($check->num_rows > 0) {
        echo "<script>alert('You already redeemed this reward!'); window.location.href='user_rewards.php';</script>";
        exit;
    }

    // Insert redemption record
    $conn->query("INSERT INTO user_rewards (user_id, reward_id, status) VALUES ('$user_id', '$reward_id', 'Pending')");
    echo "<script>alert('Reward redeemed successfully!'); window.location.href='user_rewards.php';</script>";
}
?>
