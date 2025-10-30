<?php
require_once __DIR__ . '/../../conn/dbconn.php';

if (!isset($_GET['userid'])) {
    die("<h3 style='color:red;'>❌ Invalid QR Code.</h3>");
}

$userid = intval($_GET['userid']);

// Fetch user info
$userQuery = "SELECT userName, email, purok FROM account WHERE userid = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userid);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows == 0) {
    die("<h3 style='color:red;'>❌ User not found.</h3>");
}

$user = $userResult->fetch_assoc();

// Fetch user records
$recordsQuery = "
    SELECT r.record_name, r.date, ri.quantity, ri.unit, re.RM_name
    FROM records r
    LEFT JOIN record_items ri ON r.id = ri.record_id
    LEFT JOIN recyclable re ON ri.recyclable_id = re.recyclable_id
    WHERE r.user_id = ?
    ORDER BY r.date DESC
";
$stmt2 = $conn->prepare($recordsQuery);
$stmt2->bind_param("i", $userid);
$stmt2->execute();
$records = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Recycling Record</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f6f7fb;
        padding: 30px;
        color: #333;
    }
    .card {
        background: white;
        padding: 20px 30px;
        border-radius: 15px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        max-width: 600px;
        margin: auto;
    }
    h2 { text-align: center; color: #2d6a4f; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    table, th, td {
        border: 1px solid #ccc;
    }
    th {
        background: #2d6a4f;
        color: white;
        padding: 8px;
    }
    td {
        padding: 8px;
        text-align: center;
    }
</style>
</head>
<body>
<div class="card">
    <h2>E-Recycle Record</h2>
    <p><b>Name:</b> <?= htmlspecialchars($user['userName']) ?></p>
    <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
    <p><b>Purok:</b> <?= htmlspecialchars($user['purok']) ?></p>
    <hr>

    <h3>Recycling Records:</h3>
    <?php if ($records->num_rows > 0): ?>
    <table>
        <tr>
            <th>Date</th>
            <th>Record Name</th>
            <th>Material</th>
            <th>Quantity</th>
            <th>Unit</th>
        </tr>
        <?php while ($row = $records->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['record_name']) ?></td>
            <td><?= htmlspecialchars($row['RM_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['quantity'] ?? '0') ?></td>
            <td><?= htmlspecialchars($row['unit'] ?? 'kg') ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>No recycling records yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
