<?php
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../../conn/dbconn.php';

$user_id = $_SESSION['userid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reward_id'])) {
    $reward_id = $_POST['reward_id'];

    // ✅ Check total recyclables collected
    $kgQuery = "SELECT SUM(weight) AS total_kg FROM recyclable WHERE user_id = '$user_id'";
    $kgResult = $conn->query($kgQuery);
    $kgData = $kgResult->fetch_assoc();
    $totalKg = $kgData['total_kg'] ?? 0;

    // ✅ Get required points for the reward
    $rewardQuery = "SELECT product_points FROM rewards WHERE reward_id = '$reward_id'";
    $rewardResult = $conn->query($rewardQuery);
    $reward = $rewardResult->fetch_assoc();

    if ($reward && $totalKg >= $reward['product_points']) {
        // ✅ Add record to user_rewards
        $insert = "INSERT INTO user_rewards (user_id, reward_id, status, date_redeemed)
                   VALUES ('$user_id', '$reward_id', 'Pending', NOW())";

        if ($conn->query($insert)) {
            echo "<script>alert('Reward redeemed successfully! MENRO will verify it soon.');
                  window.location.href='user_rewards.php';</script>";
        } else {
            echo "<script>alert('Error redeeming reward.'); window.location.href='user_rewards.php';</script>";
        }
    } else {
        echo "<script>alert('Not enough recyclables to redeem this reward.');
              window.location.href='user_rewards.php';</script>";
    }
} else {
    header("Location: user_rewards.php");
    exit();
}
?>