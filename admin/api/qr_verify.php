<?php
require_once __DIR__ . '/../../conn/dbconn.php';

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
            $sql = "SELECT userid, userName, total_points FROM account WHERE userid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
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
