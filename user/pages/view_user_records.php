<?php
require_once __DIR__ . '/../../conn/dbconn.php'; // keep your original DB connect

// Accept either `id` or `userid` parameter (QR might use either)
$userid = null;
if (isset($_GET['id'])) {
    $userid = intval($_GET['id']);
} elseif (isset($_GET['userid'])) {
    $userid = intval($_GET['userid']);
}

if (!$userid) {
    die("❌ Missing user ID in URL. Provide ?id=18 or ?userid=18");
}

/*
 * 1) Fetch user info (use actual column names in your `account` table)
 *    Your table uses `userid` and `userName` (based on your screenshots).
 */
$userQuery = "SELECT userid, userName AS name, email, purok FROM account WHERE userid = ?";
$stmt = $conn->prepare($userQuery);
if (!$stmt) {
    die("❌ SQL prepare (userQuery) failed: " . $conn->error);
}
$stmt->bind_param("i", $userid);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();

if (!$user) {
    die("❌ User not found for userid = {$userid}");
}

/*
 * 2) Fetch records for that user (use your `records` table column names)
 *    Your `records` table (from screenshot) uses: id, record_name, date, rec_img, user_id
 */
$recordsQuery = "
    SELECT r.id, r.record_name, r.date, r.rec_img
    FROM records r
    WHERE r.user_id = ?
    ORDER BY r.date DESC
";
$stmt2 = $conn->prepare($recordsQuery);
if (!$stmt2) {
    die("❌ SQL prepare (recordsQuery) failed: " . $conn->error);
}
$stmt2->bind_param("i", $userid);
$stmt2->execute();
$records = $stmt2->get_result();
$stmt2->close();

/**
 * Helper to resolve image URL (checks common upload folders used in your project)
 */
function resolveRecordImageUrl($filename) {
    if (empty($filename)) return null;

    // possible locations (adjust if your project stores rec_img elsewhere)
    $paths = [
        $_SERVER['DOCUMENT_ROOT'] . '/capstoneweb/assets/proofs/' . $filename,    // used in some inserts
        $_SERVER['DOCUMENT_ROOT'] . '/capstoneweb/uploads/' . $filename,          // common uploads folder
        $_SERVER['DOCUMENT_ROOT'] . '/capstoneweb/assets/' . $filename,           // other fallback
    ];

    foreach ($paths as $p) {
        if (file_exists($p)) {
            // convert filesystem path back to web path
            // find '/capstoneweb' in the full path and return that suffix
            $pos = strpos($p, '/capstoneweb');
            if ($pos !== false) {
                return substr($p, $pos);
            }
        }
    }

    // if we couldn't find it on disk, still return a sensible web path to try
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
    .card { max-width:900px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 3px 12px rgba(0,0,0,0.08); }
    h1 { color:#2d6a4f; margin-bottom:6px; }
    .meta { color:#666; margin-bottom:16px; }
    table { width:100%; border-collapse:collapse; margin-top:12px; }
    th, td { border:1px solid #e2e2e2; padding:10px; text-align:left; }
    th { background:#2d6a4f; color:#fff; }
    img.rec-img { max-width:120px; height:auto; border-radius:6px; }
</style>
</head>
<body>
<div class="card">
    <h1>Records for <?= htmlspecialchars($user['name']) ?></h1>
    <div class="meta">Email: <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; Purok: <?= htmlspecialchars($user['purok'] ?? '—') ?></div>

    <?php if ($records && $records->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Record Name</th>
                    <th>Date</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $records->fetch_assoc()): 
                $imgUrl = resolveRecordImageUrl($row['rec_img']);
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['record_name']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td>
                        <?php if (!empty($row['rec_img']) && $imgUrl): ?>
                            <img src="<?= htmlspecialchars($imgUrl) ?>" class="rec-img" alt="proof">
                        <?php else: ?>
                            No image
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No records found for this user. When the admin adds records with <code>user_id = <?= $userid ?></code>, they will appear here.</p>
    <?php endif; ?>
</div>
</body>
</html>
