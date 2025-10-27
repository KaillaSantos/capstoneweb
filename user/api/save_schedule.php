<?php
// save_schedule.php
require __DIR__ . '/../conn/dbconn.php';
header('Content-Type: application/json');

// read JSON body
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

$userId = isset($input['userId']) ? (int)$input['userId'] : 0;
// allow date to be null explicitly
$date = array_key_exists('date', $input) ? $input['date'] : null;

if ($userId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid user']);
    exit;
}

if ($date === null || $date === '') {
    // Clear the schedule: delete the row (or you can set schedule_date = NULL)
    $sql = "DELETE FROM schedule WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok) {
        echo json_encode(['success' => true, 'cleared' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
    exit;
}

// Validate date format YYYY-MM-DD
$dt = DateTime::createFromFormat('Y-m-d', $date);
if (!$dt || $dt->format('Y-m-d') !== $date) {
    echo json_encode(['success' => false, 'error' => 'Invalid date format']);
    exit;
}

// Upsert schedule (INSERT ... ON DUPLICATE KEY UPDATE). Ensure schedule.user_id is UNIQUE/PK.
$sql = "INSERT INTO schedule (user_id, schedule_date) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE schedule_date = VALUES(schedule_date)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'is', $userId, $date);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    echo json_encode(['success' => true, 'saved' => $date]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}
mysqli_stmt_close($stmt);
