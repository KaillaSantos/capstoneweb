<?php
require_once __DIR__ . '/../../conn/dbconn.php';

if (!isset($_GET['userid'])) {
    die("❌ Missing user ID.");
}

$userid = intval($_GET['user']);

// Fetch user info
$userQuery = "SELECT name, email FROM account WHERE userid = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userid);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

if (!$user) {
    die("❌ User not found.");
}

// Fetch all records for that user
$recordsQuery = "
    SELECT r.userid, r.record_name, r.date, r.rec_img
    FROM records r
    WHERE r.user_id = ?
    ORDER BY r.date DESC
";
$stmt2 = $conn->prepare($recordsQuery);
if (!$stmt2) {
    die("❌ SQL Prepare failed: " . $conn->error);
}
$stmt2->bind_param("i", $userid);
$stmt2->execute();
$records = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Records</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f6f6f6; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #4CAF50; color: white; }
        tr:hover { background: #f1f1f1; }
    </style>
</head>
<body>
    <h1>Records for <?= htmlspecialchars($user['name']) ?></h1>
    <p>Email: <?= htmlspecialchars($user['email']) ?></p>

    <?php if ($records->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Record Name</th>
                    <th>Date</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $records->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['record_name']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td>
                            <?php if (!empty($row['rec_img'])): ?>
                                <img src="/capstoneweb/uploads/<?= htmlspecialchars($row['rec_img']) ?>" width="80">
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No records found for this user.</p>
    <?php endif; ?>
</body>
</html>
