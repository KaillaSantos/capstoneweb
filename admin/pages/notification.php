<?php
require_once __DIR__ . '/../../includes/authSession.php';
include_once __DIR__ . '/../includes/passwordVerification.php';
require_once __DIR__ . '/../../conn/dbconn.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Reward Redemption Notifications | E-Recycle</title>
  <link rel="stylesheet" href="/capstoneweb/user-admin.css">
  <link rel="stylesheet" href="/capstoneweb/user-admin1.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="stylesheet" href="/capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .reward-card {
      border: 1px solid #ccc;
      border-left: 6px solid #2c5e1a;
      border-radius: 8px;
      padding: 15px;
      margin: 15px 0;
      background: #fff;
      display: flex;
      align-items: flex-start;
      gap: 15px;
      transition: 0.2s ease-in-out;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .reward-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
    }

    .reward-img {
      width: 150px;
      height: 120px;
      object-fit: cover;
      border-radius: 6px;
    }

    .reward-body h5 {
      font-weight: 600;
      margin-bottom: 6px;
    }

    .reward-body p {
      margin: 0;
      font-size: 0.9rem;
    }

    .badge-status {
      font-size: 0.85rem;
      padding: 4px 10px;
      border-radius: 12px;
    }

    .reward-actions {
      margin-top: 10px;
    }

    .reward-actions .btn {
      width: 100%;
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .reward-actions .btn-success {
      background-color: #2c5e1a;
      border: none;
    }

    .reward-actions .btn-success:hover {
      background-color: #4ea42f;
    }
  </style>
</head>

<body>

  <?php include '../includes/sidebar.php'; ?>

  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>
  <div class="overlay"></div>

  <div class="content" id="content">
    <header class="dashboard-header">
      <div class="header-left">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>Reward Redemption Notifications</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>
      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <?php if (isset($_SESSION['notif_success']) || isset($_SESSION['notif_error'])): ?>
      <div id="notif-alert"
        class="alert <?= isset($_SESSION['notif_success']) ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show text-center mx-auto mt-3"
        style="max-width: 600px; z-index: 2000;">
        <?= $_SESSION['notif_success'] ?? $_SESSION['notif_error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php unset($_SESSION['notif_success'], $_SESSION['notif_error']); ?>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4 reward-container">
      <?php
      $records_per_page = 6;
      $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
      $offset = ($page - 1) * $records_per_page;

      $sql = "
        SELECT ur.id, ur.user_id, ur.reward_id, ur.status, ur.date_redeemed,
               a.userName, r.product_name, r.product_img
        FROM user_rewards ur
        JOIN account a ON ur.user_id = a.userid
        JOIN rewards r ON ur.reward_id = r.reward_id
        WHERE ur.status = 'pending'
        ORDER BY ur.date_redeemed DESC
        LIMIT $records_per_page OFFSET $offset
      ";
      $run = mysqli_query($conn, $sql);

      if (mysqli_num_rows($run) > 0) {
        while ($rows = mysqli_fetch_assoc($run)) {
          $rewardImage = !empty($rows['product_img'])
            ? "../../uploads/productImg/" . $rows['product_img']
            : "../../uploads/productImg/rewardPlaceholder.jpg";
      ?>
          <div class="col">
            <div class="reward-card shadow-sm">
              <img src="<?php echo $rewardImage; ?>" alt="Reward Image" class="reward-img">
              <div class="reward-body flex-grow-1">
                <h5 style="text-transform: capitalize;"><?= htmlspecialchars($rows['product_name']) ?></h5>
                <p><i class="fa fa-user text-success"></i> <?= htmlspecialchars($rows['userName']) ?></p>
                <p><i class="fa fa-calendar"></i> <?= date("F j, Y", strtotime($rows['date_redeemed'])) ?></p>
                <span class="badge bg-warning text-dark badge-status"><?= ucfirst($rows['status']) ?></span>
                <div class="reward-actions">
                  <button type="button" class="btn btn-success btn-sm approve-btn"
                          data-rewardid="<?= htmlspecialchars($rows['reward_id']) ?>"
                          data-userid="<?= htmlspecialchars($rows['user_id']) ?>"
                          data-username="<?= htmlspecialchars($rows['userName']) ?>"
                          data-rewardname="<?= htmlspecialchars($rows['product_name']) ?>">
                    <i class="fa fa-check"></i> Accept
                  </button>
                </div>
              </div>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p class='text-center mt-4'>No pending reward requests yet.</p>";
      }
      ?>
    </div>

    <?php
    $countQuery = "SELECT COUNT(*) AS total FROM user_rewards WHERE status = 'Pending'";
    $countResult = mysqli_query($conn, $countQuery);
    $total_records = mysqli_fetch_assoc($countResult)['total'];
    $total_pages = ceil($total_records / $records_per_page);
    if ($total_pages > 1): ?>
      <div class="d-flex justify-content-center mt-4 mb-4">
        <nav>
          <ul class="pagination">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    <?php endif; ?>
  </div>

  <!-- ✅ QR SCANNER MODAL -->
  <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center">
        <div class="modal-header">
          <h5 class="modal-title" id="qrScannerModalLabel"><i class="bi bi-qr-code-scan"></i> Scan User QR</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="qr-reader-modal" style="width: 100%;"></div>
          <p id="qrScanStatus" class="mt-3 text-success fw-bold"></p>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ APPROVAL MODAL -->
  <div class="modal fade" id="approveRewardModal" tabindex="-1" aria-labelledby="approveRewardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="/capstoneweb/function/function.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="approveRewardModalLabel">Confirm Reward Approval</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to approve this reward redemption?</p>
            <ul class="list-unstyled mb-3">
              <li><strong>User:</strong> <span id="modalUserName" class="text-success"></span></li>
              <li><strong>Reward:</strong> <span id="modalRewardName" class="text-primary"></span></li>
            </ul>
            <input type="hidden" name="reward_id" id="modalRewardId">
            <input type="hidden" name="user_id" id="modalUserId">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="approve_reward" class="btn btn-success">Confirm Approve</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="/capstoneweb/assets/sidebarToggle.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>


  <script>
  document.addEventListener("DOMContentLoaded", () => {
  const approveModal = new bootstrap.Modal(document.getElementById('approveRewardModal'));
  const qrScannerModal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
  let qrScanner;
  let isQrVerified = false;

  const modalRewardId = document.getElementById('modalRewardId');
  const modalUserId = document.getElementById('modalUserId');
  const modalUserName = document.getElementById('modalUserName');
  const modalRewardName = document.getElementById('modalRewardName');
  const qrScanStatus = document.getElementById('qrScanStatus');
  const confirmButton = document.querySelector('#approveRewardModal button[name="approve_reward"]');

  // Disable the confirm button by default
  confirmButton.disabled = true;

  document.querySelectorAll('.approve-btn').forEach(button => {
    button.addEventListener('click', () => {
      const rewardId = button.dataset.rewardid;
      const userId = button.dataset.userid;
      const userName = button.dataset.username;
      const rewardName = button.dataset.rewardname;

      modalRewardId.value = rewardId;
      modalUserId.value = userId;
      modalUserName.textContent = userName;
      modalRewardName.textContent = rewardName;

      // Reset QR status
      isQrVerified = false;
      qrScanStatus.innerText = "Please scan the user's QR code to verify.";
      confirmButton.disabled = true;

      qrScannerModal.show();

      setTimeout(() => {
        qrScanner = new Html5Qrcode("qr-reader-modal");
        Html5Qrcode.getCameras().then(cameras => {
          if (!cameras.length) {
            qrScanStatus.innerText = "No camera found.";
            return;
          }
          qrScanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            decodedText => {
              qrScanStatus.innerText = "Verifying QR...";

              fetch("/capstoneweb/admin/api/qr_verify.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "qr_data=" + encodeURIComponent(decodedText)
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  // ✅ Verified user
                  isQrVerified = true;
                  qrScanStatus.innerHTML = "✅ Verified: " + data.user.userName;

                  // Stop scanner and open approval modal
                  setTimeout(() => {
                    qrScanner.stop().then(() => {
                      qrScannerModal.hide();
                      approveModal.show();
                      confirmButton.disabled = false; // enable only now
                    });
                  }, 800);
                } else {
                  qrScanStatus.innerHTML = "❌ " + data.message;
                }
              })
              .catch(err => {
                qrScanStatus.innerText = "Error verifying: " + err;
              });
            },
            err => {}
          );
        });
      }, 300);
    });
  });

  // Prevent approval if QR not verified
  document.querySelector('form[action="/capstoneweb/function/function.php"]').addEventListener('submit', e => {
    if (!isQrVerified) {
      e.preventDefault();
      alert("Please verify the user's QR code before approving.");
    }
  });

  // Stop camera on modal close
  document.getElementById('qrScannerModal').addEventListener('hidden.bs.modal', () => {
    if (qrScanner) qrScanner.stop().catch(() => {});
  });
});
</script>

</body>
</html>
