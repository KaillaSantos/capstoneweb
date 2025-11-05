<?php 
session_start();
require '../../conn/dbconn.php';

// Ensure user is logged in
if (!isset($_SESSION['userid'])) {
    $_SESSION['error'] = "You must be logged in to redeem rewards.";
    header("Location: ../login.php");
    exit;
}

// Check if reward_id is submitted
if (!isset($_POST['reward_id']) || empty($_POST['reward_id'])) {
    $_SESSION['error'] = "No reward selected.";
    header("Location: user_rewards.php");
    exit;
}

$reward_id = intval($_POST['reward_id']); // sanitize
$user_id = $_SESSION['userid'];

// Check if reward exists
$rewardCheck = $conn->prepare("SELECT * FROM rewards WHERE reward_id = ?");
$rewardCheck->bind_param("i", $reward_id);
$rewardCheck->execute();
$rewardResult = $rewardCheck->get_result();

if ($rewardResult->num_rows === 0) {
    $_SESSION['error'] = "Invalid reward selected.";
    header("Location: user_rewards.php");
    exit;
}

// Check if user already redeemed this reward
$check = $conn->prepare("SELECT * FROM user_rewards WHERE user_id = ? AND reward_id = ?");
$check->bind_param("ii", $user_id, $reward_id);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    $_SESSION['error'] = "You already redeemed this reward!";
    header("Location: user_rewards.php");
    exit;
}

// Insert redemption record
$insert = $conn->prepare("INSERT INTO user_rewards (user_id, reward_id, status, date_redeemed) VALUES (?, ?, 'Pending', NOW())");
$insert->bind_param("ii", $user_id, $reward_id);

if ($insert->execute()) {
    $_SESSION['success'] = "Reward redeemed successfully!";
    header("Location: user_rewards.php");
    exit;
} else {
    // Log the error for debugging (do not expose raw SQL errors to users)
    error_log("Redeem reward failed: " . $conn->error);
    $_SESSION['error'] = "Failed to redeem reward. Please try again later.";
    header("Location: user_rewards.php");
    exit;
}
?>
