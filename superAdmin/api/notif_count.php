<?php
require '../conn/dbconn.php';
header('Content-Type: application/json');

$userId = isset($_GET['user']) ? (int)$_GET['user'] : 0;

if ($userId <= 0) {
  echo json_encode(['count' => 0]);
  exit;
}

// Count unread notifications for this user
$sql = "SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = $userId AND is_read = 0";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

echo json_encode(['count' => (int)$row['cnt']]);
?>
