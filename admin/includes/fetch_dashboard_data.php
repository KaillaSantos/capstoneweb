<?php
require_once __DIR__ . '/../../conn/dbconn.php';

$purok = $_GET['purok'] ?? 'all';
$filter = $purok !== 'all' ? "AND a.purok = '$purok'" : '';

// 🧍‍♂️ Top Users Query (filtered by purok if selected)
$queryUsers = "
    SELECT 
        a.userid,
        a.userName,
        SUM(ri.quantity) AS total_contribution
    FROM account a
    JOIN records r ON a.userid = r.user_id
    JOIN record_items ri ON r.id = ri.record_id
    WHERE a.role = 'user' $filter
    GROUP BY a.userid
    ORDER BY total_contribution DESC
    LIMIT 5
";
$resultUsers = mysqli_query($conn, $queryUsers);
$users = [];
while ($row = mysqli_fetch_assoc($resultUsers)) {
    $users[] = $row;
}

// 🏘️ Top Puroks Query (filtered as needed)
$queryPuroks = "
    SELECT 
        a.purok,
        SUM(ri.quantity) AS total_contribution
    FROM account a
    JOIN records r ON a.userid = r.user_id
    JOIN record_items ri ON r.id = ri.record_id
    WHERE a.purok IS NOT NULL AND a.purok != 0 $filter
    GROUP BY a.purok
    ORDER BY total_contribution DESC
    LIMIT 5
";
$resultPuroks = mysqli_query($conn, $queryPuroks);
$puroks = [];
while ($row = mysqli_fetch_assoc($resultPuroks)) {
    $puroks[] = $row;
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode(['users' => $users, 'puroks' => $puroks]);
?>