<?php
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../includes/passwordVerification.php';
require_once __DIR__ . '/../../conn/dbconn.php';

$user_id = $_SESSION['userid'];

// Get total recyclables collected (kg)
$kgQuery = "
    SELECT SUM(ri.quantity) AS total_kg
    FROM record_items ri
    JOIN records r ON ri.record_id = r.id
    WHERE r.user_id = '$user_id' AND ri.unit = 'kg'
";
$kgResult = $conn->query($kgQuery);
$kgData = $kgResult->fetch_assoc();
$totalKg = $kgData['total_kg'] ?? 0;

// Fetch all available rewards
$rewardQuery = "SELECT * FROM rewards";
$rewards = $conn->query($rewardQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Rewards | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png"> 
  <link rel="stylesheet" href="\capstoneweb/user-admin.css">
  <link rel="stylesheet" href="\capstoneweb/user-admin1.css">
</head>

<body>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle Button -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>
  <div class="overlay"></div>

  <!-- Content -->
  <div class="content" id="content">
    <header class="dashboard-header">
      <div class="header-left">
        <img src="\capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>E-Recycle Rewards Page</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>
      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <!--  Rewards Section -->
    <div class="container mt-4">
      <h3 class="mb-3">Available Rewards</h3>
      <p><strong>Total Collected:</strong> <?php echo $totalKg; ?> kg</p>

      <div class="row">
        <?php while ($reward = $rewards->fetch_assoc()): ?>
          <?php
            $requiredPoints = $reward['product_points'];
            $canRedeem = $totalKg >= $requiredPoints;
            $progress = min(100, ($totalKg / $requiredPoints) * 100);
          ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100">
              <img src="..\..\uploads\productImg\<?php echo $reward['product_img']; ?>" class="card-img-top" alt="Reward">
              <div class="card-body">
                <h5 class="card-title"><?php echo $reward['product_name']; ?></h5>
                <p class="card-text"><?php echo $reward['product_description']; ?></p>
                <div class="progress mb-2">
                  <div class="progress-bar"
                      role="progressbar"
                      data-progress="<?php echo $progress; ?>"
                      style="width: 0%"
                      aria-valuemin="0" aria-valuemax="100">
                  </div>
                </div>

                <p><?php echo $totalKg; ?>kg / <?php echo $requiredPoints; ?>kg collected</p>
              </div>
              <div class="card-footer text-center">
                <form method="POST" action="redeem_reward.php">
                  <input type="hidden" name="reward_id" value="<?php echo $reward['reward_id']; ?>">
                  <button type="submit" class="btn btn-success w-100" <?php echo $canRedeem ? '' : 'disabled'; ?>>
                    <?php echo $canRedeem ? 'Redeem Reward' : 'Keep Collecting'; ?>
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

      <!-- Redeemed Rewards Tracking -->
      <h3 class="mt-5 mb-3">My Redeemed Rewards</h3>
      <table class="table table-bordered table-striped">
        <thead class="table-success">
          <tr>
            <th>Reward</th>
            <th>Status</th>
            <th>Date Redeemed</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $track = $conn->query("
              SELECT r.product_name, ur.status, ur.date_redeemed
              FROM user_rewards ur
              JOIN rewards r ON ur.reward_id = r.reward_id
              WHERE ur.user_id = '$user_id'
              ORDER BY ur.date_redeemed DESC
            ");
            if ($track->num_rows > 0):
              while ($row = $track->fetch_assoc()):
          ?>
            <tr>
              <td><?php echo $row['product_name']; ?></td>
              <td><?php echo $row['status']; ?></td>
              <td><?php echo date("F j, Y", strtotime($row['date_redeemed'])); ?></td>
            </tr>
          <?php
              endwhile;
            else:
              echo "<tr><td colspan='3' class='text-center'>No redeemed rewards yet.</td></tr>";
            endif;
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Password Verify Modal -->
  <div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="">
          <div class="modal-header">
            <h5 class="modal-title" id="verifyPasswordModalLabel">Verify Your Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="verifyPassword" class="form-label">Enter Password</label>
              <input type="password" class="form-control" name="verify_password" id="verifyPassword" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="verify_submit" class="btn btn-primary">Verify</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../../assets/sidebarToggle.js"></script>
  <script>
document.addEventListener("DOMContentLoaded", function() {
  const bars = document.querySelectorAll('.progress-bar');

  bars.forEach(bar => {
    const target = parseFloat(bar.getAttribute('data-progress')) || 0;
    let current = 0;

    const animate = setInterval(() => {
      if (current >= target) {
        clearInterval(animate);
      } else {
        current += 1; // Speed — increase for faster animation
        bar.style.width = current + "%";
      }
    }, 10); // Interval speed (ms)
  });
});
</script>
</body>
</html>
