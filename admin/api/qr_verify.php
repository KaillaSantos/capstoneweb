<?php
require_once __DIR__ . '/../../conn/dbconn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrData = trim($_POST['qr_data'] ?? '');

    if (empty($qrData)) {
        echo json_encode(['success' => false, 'message' => 'No QR data received']);
        exit;
    }

    // Example: QR contains user_id=5
    if (strpos($qrData, 'user_id=') === 0) {
        parse_str($qrData, $data);
        $userId = $data['user_id'] ?? null;

        if ($userId) {
            // 🔍 Fetch user info
            $sql = "SELECT userid, userName, total_points FROM account WHERE userid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // 🔍 Fetch latest pending reward for this user (optional but useful)
                $rewardQuery = "
                    SELECT ur.reward_id, r.product_name 
                    FROM user_rewards ur
                    JOIN rewards r ON ur.reward_id = r.reward_id
                    WHERE ur.user_id = ? AND ur.status = 'pending'
                    ORDER BY ur.date_redeemed DESC
                    LIMIT 1
                ";
                $rStmt = $conn->prepare($rewardQuery);
                $rStmt->bind_param("i", $userId);
                $rStmt->execute();
                $rResult = $rStmt->get_result();

                if ($rResult->num_rows > 0) {
                    $reward = $rResult->fetch_assoc();
                    $user['reward_id'] = $reward['reward_id'];
                    $user['reward_name'] = $reward['product_name'];
                } else {
                    $user['reward_id'] = null;
                    $user['reward_name'] = null;
                }

                echo json_encode([
                    'success' => true,
                    'user' => $user,
                    'message' => 'User verified successfully.'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid QR format.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Unrecognized QR data.']);
    }
}
?>
