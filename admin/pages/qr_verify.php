<?php
include('../includes/db.php'); // adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_data = $_POST['qr_data']; // data scanned from QR
    $response = ['status' => 'error', 'message' => 'Invalid QR Code'];

    // Example QR content format: user_id=37&reward_id=8
    parse_str($qr_data, $data);
    $user_id = $data['user_id'] ?? null;
    $reward_id = $data['reward_id'] ?? null;

    if ($user_id && $reward_id) {
        $check = $conn->prepare("SELECT * FROM user_rewards WHERE user_id=? AND reward_id=?");
        $check->bind_param("ii", $user_id, $reward_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['status'] === 'Pending') {
                $update = $conn->prepare("UPDATE user_rewards SET status='Redeemed', date_redeemed=NOW() WHERE id=?");
                $update->bind_param("i", $row['id']);
                $update->execute();
                $response = ['status' => 'success', 'message' => 'Reward successfully redeemed!'];
            } else {
                $response = ['status' => 'info', 'message' => 'Reward already redeemed.'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'No matching record found.'];
        }
    }

    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QR Reward Verification</title>
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body>
  <h2>QR Reward Verification</h2>
  <div id="reader" style="width:300px;"></div>
  <p id="result"></p>

  <script>
    function onScanSuccess(decodedText) {
      // Send scanned data to server
      fetch('qr_verify.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'qr_data=' + encodeURIComponent(decodedText)
      })
      .then(res => res.json())
      .then(data => {
        document.getElementById('result').innerText = data.message;
      });
    }

    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
      { facingMode: "environment" }, 
      { fps: 10, qrbox: 250 },
      onScanSuccess
    );
  </script>
</body>
</html>
