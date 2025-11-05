<?php
require_once __DIR__ . '/../../conn/dbconn.php'; 

// ✅ Get user ID from URL (?id=18 or ?userid=18)
$userid = null;
if (isset($_GET['id'])) {
    $userid = intval($_GET['id']);
} elseif (isset($_GET['userid'])) {
    $userid = intval($_GET['userid']);
}
if (!$userid) {
    die("❌ Missing user ID in URL. Provide ?id= or ?userid=");
}

// ✅ Fetch user info
$userQuery = "SELECT userid, userName AS name, email, purok FROM account WHERE userid = ?";
$stmt = $conn->prepare($userQuery);
if (!$stmt) die("❌ SQL prepare (userQuery) failed: " . $conn->error);
$stmt->bind_param("i", $userid);
$stmt->execute();   
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();
if (!$user) die("❌ User not found for userid = {$userid}");

// ✅ Fetch recyclable categories (Plastic, Cardboard, Tin Can, Bakal, etc.)
$catQuery = "SELECT id AS recyclable_id, RM_name FROM recyclable ORDER BY id ASC";
$catRes = $conn->query($catQuery);

if (!$catRes) {
    die("❌ Error fetching recyclable categories: " . $conn->error);
}

$categories = [];
while ($cat = $catRes->fetch_assoc()) {
    $categories[$cat['recyclable_id']] = $cat['RM_name'];
}



// ✅ Fetch all records for that user + recyclable items per record
$recordsQuery = "
    SELECT 
        r.id AS record_id, r.record_name, r.date, r.rec_img,
        ri.recyclable_id, ri.quantity, ri.unit
    FROM records r
    LEFT JOIN record_items ri ON r.id = ri.record_id
    WHERE r.user_id = ?
    ORDER BY r.date DESC
";
$stmt2 = $conn->prepare($recordsQuery);
if (!$stmt2) die("❌ SQL prepare (recordsQuery) failed: " . $conn->error);
$stmt2->bind_param("i", $userid);
$stmt2->execute();
$result = $stmt2->get_result();
$stmt2->close();

// ✅ Group records + their recyclable items
$userRecords = [];
while ($row = $result->fetch_assoc()) {
    $rid = $row['record_id'];
    if (!isset($userRecords[$rid])) {
        $userRecords[$rid] = [
            'record_name' => $row['record_name'],
            'date' => $row['date'],
            'rec_img' => $row['rec_img'],
            'items' => array_fill_keys(array_keys($categories), ['qty' => 0, 'unit' => ''])
        ];
    }
    if ($row['recyclable_id']) {
        $userRecords[$rid]['items'][$row['recyclable_id']] = [
            'qty' => $row['quantity'],
            'unit' => $row['unit']
        ];
    }
}

// ✅ Helper to resolve image path
function resolveRecordImageUrl($filename) {
    if (empty($filename)) return null;
    $paths = [
        $_SERVER['DOCUMENT_ROOT'] . '/capstoneweb/assets/proofs/' . $filename,
        $_SERVER['DOCUMENT_ROOT'] . '/capstoneweb/uploads/' . $filename,
        $_SERVER['DOCUMENT_ROOT'] . '/capstoneweb/assets/' . $filename,
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) {
            $pos = strpos($p, '/capstoneweb');
            if ($pos !== false) return substr($p, $pos);
        }
    }
    return '/capstoneweb/assets/proofs/' . rawurlencode($filename);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Records for <?= htmlspecialchars($user['name']) ?></title>
<style>
    body { font-family: Arial, sans-serif; background: #f6f6f6; padding: 20px; color:#222; }
    .card { max-width:950px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 3px 12px rgba(0,0,0,0.08); }
    h1 { color:#2d6a4f; margin-bottom:6px; }
    .meta { color:#666; margin-bottom:16px; }
    table { width:100%; border-collapse:collapse; margin-top:12px; }
    th, td { border:1px solid #e2e2e2; padding:10px; text-align:center; }
    th { background:#2d6a4f; color:#fff; }
    img.rec-img { max-width:120px; height:auto; border-radius:6px; }
    .record-block { margin-bottom:40px; }
    h2 { color:#1b4332; margin-top:10px; }
</style>
</head>
<body>
<div class="card">
    <h1>Records for <?= htmlspecialchars($user['name']) ?></h1>
    <div class="meta">Email: <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; Purok: <?= htmlspecialchars($user['purok'] ?? '—') ?></div>

    <?php if (!empty($userRecords)): ?>
        <?php foreach ($userRecords as $rec): ?>
            <div class="record-block">
                <h2><?= htmlspecialchars($rec['record_name']) ?> (<?= htmlspecialchars($rec['date']) ?>)</h2>
                <?php if ($rec['rec_img']): 
                    $imgUrl = resolveRecordImageUrl($rec['rec_img']); ?>
                    <img src="<?= htmlspecialchars($imgUrl) ?>" class="rec-img" alt="proof">
                <?php endif; ?>

                <table>
                    <thead>
                        <tr>
                            <th>Recyclable Material</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cid => $cname): ?>
                            <tr>
                                <td><?= htmlspecialchars($cname) ?></td>
                                <td><?= htmlspecialchars($rec['items'][$cid]['qty']) ?></td>
                                <td><?= htmlspecialchars($rec['items'][$cid]['unit']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No records found for this specific user.</p>
    <?php endif; ?>
</div>
</body>
</html>
