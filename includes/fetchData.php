<?php
// ✅ Fetch all recyclable categories
$categories = [];
$catQuery = "SELECT id, RM_name FROM recyclable ORDER BY id ASC";
$catResult = mysqli_query($conn, $catQuery);
while ($catRow = mysqli_fetch_assoc($catResult)) {
  $categories[$catRow['id']] = $catRow['RM_name'];
}

// ✅ Fetch records + their items with units
$sql = "SELECT r.id, r.date, r.record_name, r.rec_img,
                ri.recyclable_id, ri.quantity, ri.unit
          FROM records r
          LEFT JOIN record_items ri ON r.id = ri.record_id
          ORDER BY r.date DESC, r.id DESC";
$result = mysqli_query($conn, $sql);

$records = [];
while ($row = mysqli_fetch_assoc($result)) {
  $id = $row['id'];

  // Initialize record if first time
  if (!isset($records[$id])) {
    $records[$id] = [
      'date' => $row['date'],
      'name' => $row['record_name'],
      'items' => array_fill_keys(array_keys($categories), ['qty' => 0, 'unit' => ''])
    ];
  }

  // Fill in quantity + unit if exists
  if ($row['recyclable_id']) {
    $records[$id]['items'][$row['recyclable_id']] = [
      'qty' => $row['quantity'],
      'unit' => $row['unit']
    ];
  }
}

//Fetch recent notifications for dashboard
$notifQuery = "SELECT n.*, a.userName 
                FROM notifications n
                JOIN account a ON n.user_id = a.userid
                ORDER BY n.created_at DESC
                LIMIT 10"; // only show recent 10
$notifResult = mysqli_query($conn, $notifQuery);

$notifications = [];
if ($notifResult && mysqli_num_rows($notifResult) > 0) {
  while ($row = mysqli_fetch_assoc($notifResult)) {
    $notifications[] = $row;
  }
}


?>