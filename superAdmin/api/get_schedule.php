<?php
require '../conn/dbconn.php';
header('Content-Type: application/json');

$userId = isset($_GET['user']) ? (int)$_GET['user'] : 0;
if ($userId <= 0) {
  echo json_encode(['date' => null]);
  exit;
}

$sql = "SELECT schedule_date FROM schedule WHERE user_id = $userId LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  echo json_encode(['date' => $row['schedule_date']]);
} else {
  echo json_encode(['date' => null]);
}
?>
