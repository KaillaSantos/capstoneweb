<?php
session_start();
require '../../conn/dbconn.php';

// Ensure user is logged in
if (!isset($_SESSION['userid'])) {
    echo "<script>alert('You must be logged in to redeem rewards.'); window.location.href='../login.php';</script>";
    exit;
}

// Check if form submitted
if (isset($_POST['reward_id'])) {
    $reward_id = intval($_POST['reward_id']); // sanitize
    $user_id = $_SESSION['userid'];

    // Check if reward exists
    $rewardCheck = $conn->query("SELECT * FROM rewards WHERE reward_id = '$reward_id'");
    if ($rewardCheck->num_rows == 0) {
        echo "<script>alert('Invalid reward.'); window.location.href='user_rewards.php';</script>";
        exit;
    }

    // Check if user already redeemed this reward
    $check = $conn->query("SELECT * FROM user_rewards WHERE user_id='$user_id' AND reward_id='$reward_id'");
    if ($check && $check->num_rows > 0) {
        echo "<script>alert('You already redeemed this reward!'); window.location.href='user_rewards.php';</script>";
        exit;
    }

    // Insert redemption record
    $insert = $conn->prepare("INSERT INTO user_rewards (user_id, reward_id, status, date_redeemed) VALUES (?, ?, 'Pending', NOW())");
    $insert->bind_param("ii", $user_id, $reward_id);
    
    if ($insert->execute()) {
        echo "<script>alert('Reward redeemed successfully!'); window.location.href='user_rewards.php';</script>";
    } else {
        echo "<script>alert('Failed to redeem reward. Please try again.'); window.location.href='user_rewards.php';</script>";
    }
}
?>
