<?php
require_once __DIR__ . '/../../conn/dbconn.php';

$purok = $_GET['purok'] ?? 'all';
$filter = $purok !== 'all' ? "AND a.purok = '$purok'" : '';

// Total households
$totalHouseholdsQ = "SELECT COUNT(userid) AS total_households FROM account a WHERE a.role='user' $filter";
$totalHouseholdsR = mysqli_query($conn, $totalHouseholdsQ);
$totalHouseholds = mysqli_fetch_assoc($totalHouseholdsR)['total_households'] ?? 0;

// Pending notifications
$pendingNotifQ = "SELECT COUNT(*) AS pending_notifications 
                  FROM user_rewards ur
                  JOIN account a ON ur.user_id = a.userid
                  WHERE ur.status='pending' $filter";
$pendingNotifR = mysqli_query($conn, $pendingNotifQ);
$pendingNotifications = mysqli_fetch_assoc($pendingNotifR)['pending_notifications'] ?? 0;

// Total rewards (static, not filtered)
$totalRewardsQ = "SELECT COUNT(*) AS total_rewards FROM rewards";
$totalRewardsR = mysqli_query($conn, $totalRewardsQ);
$totalRewards = mysqli_fetch_assoc($totalRewardsR)['total_rewards'] ?? 0;

// Redeemed rewards
$redeemedQ = "SELECT COUNT(*) AS redeemed_total 
              FROM user_rewards ur
              JOIN account a ON ur.user_id = a.userid
              WHERE ur.status='Approved' $filter";
$redeemedR = mysqli_query($conn, $redeemedQ);
$redeemedTotal = mysqli_fetch_assoc($redeemedR)['redeemed_total'] ?? 0;

// Top Users
$queryUsers = "
    SELECT a.userid, a.userName, SUM(ri.quantity) AS total_contribution
    FROM account a
    JOIN records r ON a.userid = r.user_id
    JOIN record_items ri ON r.id = ri.record_id
    WHERE a.role='user' $filter
    GROUP BY a.userid
    ORDER BY total_contribution DESC
    LIMIT 5
";
$resultUsers = mysqli_query($conn, $queryUsers);
$users = [];
while ($row = mysqli_fetch_assoc($resultUsers)) $users[] = $row;

// Top Puroks
$queryPuroks = "
    SELECT a.purok, SUM(ri.quantity) AS total_contribution
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
while ($row = mysqli_fetch_assoc($resultPuroks)) $puroks[] = $row;

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    'stats' => [
        'total_households' => $totalHouseholds,
        'pending_notifications' => $pendingNotifications,
        'total_rewards' => $totalRewards,
        'redeemed_total' => $redeemedTotal
    ],
    'users' => $users,
    'puroks' => $puroks
]);
exit;
?>
