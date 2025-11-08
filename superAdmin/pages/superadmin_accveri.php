<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===== REQUIRED FILES =====
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../includes/passwordVerification.php';
include __DIR__ . '/../includes/sidebar.php';

// ===== FETCH LOGGED IN USER =====
$query = "SELECT * FROM account WHERE userid = '$userid'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Account Verification | E-Recycle</title>

  <!-- ===== STYLES ===== -->
  <link rel="stylesheet" href="/capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="/capstoneweb/user-admin.css">
  <link rel="stylesheet" href="/capstoneweb/user-admin1.css">
  <style>
    .table-responsive {
      overflow-x: visible !important;
      overflow-y: visible !important;
    }
  </style>
</head>
<body>

  <!-- ===== SIDEBAR ===== -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- ===== TOGGLE BUTTON ===== -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- ===== MAIN CONTENT ===== -->
  <div class="content" id="content">

    <!-- ===== HEADER ===== -->
    <header class="dashboard-header d-flex justify-content-between align-items-center">
      <div class="header-left d-flex align-items-center">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text ms-3">
          <h1 class="h4 mb-0">E-Recycle Account Verification</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>
      <div class="header-right">
        <span class="date-display fw-semibold"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="dropdown">
        <button class="btn btn-outline-success dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          Select Table
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
          <li><a class="dropdown-item active" href="#" data-table="pending">Account Verification</a></li>
          <li><a class="dropdown-item" href="#" data-table="approved">Approved Accounts</a></li>
          <li><a class="dropdown-item" href="#" data-table="disabled">Disabled Accounts</a></li>
        </ul>
      </div>
    </div>

    <!-- ===== USERS TABLE ===== -->
    <div id="pendingTableContainer" class="table-section">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-center">Account Verification</h4>
      </div>
      <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th style="width: 80px;">Image</th>
              <th>User Name</th>
              <th>Email</th>
              <th>Purok</th>
              <th>Status</th>
              <th style="width: 180px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // ===== PAGINATION =====
            $records_per_page = 5;
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $records_per_page;

            // ===== FETCH USERS =====
            $sql = "
              SELECT userid, userimg, userName, email, purok, status 
              FROM account 
              WHERE role = 'User' AND status = 'pending'
              ORDER BY userid DESC
              LIMIT $records_per_page OFFSET $offset
            ";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0):
              while ($row = mysqli_fetch_assoc($result)):
                $imagePath = !empty($row['userimg'])
                  ? "../../image/" . htmlspecialchars($row['userimg'])
                  : "../../image/placeholder.jpg";
            ?>
                <tr>
                  <td>
                    <img src="<?= $imagePath ?>" alt="User Image" class="img-thumbnail rounded" style="width: 60px; height: 60px; object-fit: cover;">
                  </td>
                  <td class="text-capitalize"><?= htmlspecialchars($row['userName']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['purok']) ?></td>
                  <td>
                    <span class="badge bg-warning text-dark">Pending</span>
                  </td>
                  <td>
                    <button type="button" 
                            class="btn btn-success btn-sm approve-btn" 
                            data-userid="<?= htmlspecialchars($row['userid']) ?>" 
                            data-username="<?= htmlspecialchars($row['userName']) ?>"
                            title="Approve">
                      <i class="fa fa-check"></i>
                    </button>
                    <button type="button"
                            class="btn btn-danger btn-sm reject-btn"
                            data-userid="<?= htmlspecialchars($row['userid']) ?>"
                            data-username="<?= htmlspecialchars($row['userName']) ?>"
                            title="Reject">
                      <i class="fa fa-times"></i>
                    </button>
                  </td>
                </tr>
              <?php endwhile;
            else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-4">No unverified users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ===== APPROVED ACCOUNTS ===== -->
    <div id="approvedTableContainer" class="table-section" style="display:none;">
      <?php
        $sql = "
          SELECT userid, userimg, userName, email, purok, status, role FROM account WHERE status = 'approved' AND (role = 'admin' OR role = 'user') ORDER BY userid DESC LIMIT $records_per_page OFFSET $offset
        ";
        $result = mysqli_query($conn, $sql);
      ?>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-center">Approved Accounts</h4>
      </div>
      <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th style="width: 80px;">Image</th>
              <th>User Name</th>
              <th>Email</th>
              <th>Purok</th>
              <th>Role</th>
              <th>Status</th>
              <th style="width: 150px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($result)):
                $imagePath = !empty($row['userimg']) ? "../../image/" . htmlspecialchars($row['userimg']) : "../../image/placeholder.jpg";
              ?>
                <tr>
                  <td><img src="<?= $imagePath ?>" class="img-thumbnail rounded" style="width: 60px; height: 60px; object-fit: cover;"></td>
                  <td class="text-capitalize"><?= htmlspecialchars($row['userName']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['purok']) ?></td>
                  <td class="text-capitalize">
                    <?php if ($row['role'] === 'admin'): ?>
                      <span class="badge bg-primary">Admin</span>
                    <?php else: ?>
                      <span class="badge bg-success">User</span>
                    <?php endif; ?>
                  </td>
                  <td><span class="badge bg-success">Approved</span></td>
                  <td>
                    <button type="button" 
                            class="btn btn-warning btn-sm disable-btn"
                            data-userid="<?= htmlspecialchars($row['userid']) ?>"
                            data-username="<?= htmlspecialchars($row['userName']) ?>"
                            title="Disable Account">
                      <i class="fa fa-ban"></i> Disable
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No approved accounts found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ===== DISABLED ACCOUNTS ===== -->
    <div id="disabledTableContainer" class="table-section" style="display:none;">
      <?php
        $sql = "SELECT userid, userimg, userName, email, purok, status, role FROM account WHERE status = 'disabled' ORDER BY userid DESC";
        $result = mysqli_query($conn, $sql);
      ?>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-center">Disabled Accounts</h4>
      </div>
      <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th style="width: 80px;">Image</th>
              <th>User Name</th>
              <th>Email</th>
              <th>Purok</th>
              <th>Role</th>
              <th>Status</th>
              <th style="width: 150px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($result)):
                $imagePath = !empty($row['userimg']) ? "../../image/" . htmlspecialchars($row['userimg']) : "../../image/placeholder.jpg";
              ?>
                <tr>
                  <td><img src="<?= $imagePath ?>" class="img-thumbnail rounded" style="width: 60px; height: 60px; object-fit: cover;"></td>
                  <td class="text-capitalize"><?= htmlspecialchars($row['userName']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['purok']) ?></td>
                  <td class="text-capitalize"><?= htmlspecialchars($row['role']) ?></td>
                  <td><span class="badge bg-secondary">Disabled</span></td>
                  <td>
                    <button type="button" 
                            class="btn btn-success btn-sm enable-btn"
                            data-userid="<?= htmlspecialchars($row['userid']) ?>"
                            data-username="<?= htmlspecialchars($row['userName']) ?>"
                            title="Enable Account">
                      <i class="fa fa-check"></i> Enable
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-muted py-4">No disabled accounts found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- ===== MODAL FOR ALL ACTIONS ===== -->
  <div class="modal fade" id="actionConfirmModal" tabindex="-1" aria-labelledby="actionConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="actionConfirmForm" method="POST" action="../../function/function.php">
          <div class="modal-header">
            <h5 class="modal-title" id="actionConfirmModalLabel">Confirm Action</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p id="actionConfirmMessage"></p>
            <input type="hidden" name="userid" id="actionUserId">
            <input type="hidden" name="action_type" id="actionType">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="actionConfirmButton">Confirm</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ===== PASSWORD VERIFY MODAL ===== -->
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

  <!-- ===== JS ===== -->
  <script src="/capstoneweb/assets/sidebarToggle.js"></script>
  <script src="/capstoneweb/assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    // Table dropdown
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const sections = {
      pending: document.getElementById("pendingTableContainer"),
      approved: document.getElementById("approvedTableContainer"),
      disabled: document.getElementById("disabledTableContainer")
    };

    const urlParams = new URLSearchParams(window.location.search);
    let selectedTable = urlParams.get("table") || "pending";

    function showTable(table) {
      Object.values(sections).forEach(sec => sec.style.display = "none");
      if (sections[table]) sections[table].style.display = "block";

      const activeItem = document.querySelector(`.dropdown-item[data-table="${table}"]`);
      if (activeItem) {
        dropdownItems.forEach(i => i.classList.remove("active"));
        activeItem.classList.add("active");
        document.getElementById("filterDropdown").textContent = activeItem.textContent;
      }
    }

    showTable(selectedTable);

    dropdownItems.forEach(item => {
      item.addEventListener("click", e => {
        e.preventDefault();
        const table = item.dataset.table;
        showTable(table);
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set("table", table);
        window.history.replaceState({}, '', newUrl);
      });
    });

    // Modal actions
    const actionModal = new bootstrap.Modal(document.getElementById('actionConfirmModal'));
    const actionUserId = document.getElementById('actionUserId');
    const actionType = document.getElementById('actionType');
    const actionMessage = document.getElementById('actionConfirmMessage');
    const actionConfirmButton = document.getElementById('actionConfirmButton');
    const actionForm = document.getElementById('actionConfirmForm');

    const setActionModal = (userid, username, type) => {
      actionUserId.value = userid;
      actionType.value = type;
      switch(type) {
        case 'approve_user':
          actionMessage.innerHTML = `Are you sure you want to <strong>approve</strong> the account of <strong>${username}</strong>?`;
          actionConfirmButton.className = 'btn btn-success';
          break;
        case 'disable_user':
          actionMessage.innerHTML = `Are you sure you want to <strong>disable</strong> the account of <strong>${username}</strong>?`;
          actionConfirmButton.className = 'btn btn-warning';
          break;
        case 'enable_user':
          actionMessage.innerHTML = `Are you sure you want to <strong>enable</strong> the account of <strong>${username}</strong>?`;
          actionConfirmButton.className = 'btn btn-success';
          break;
        case 'reject_user':
          actionMessage.innerHTML = `Are you sure you want to <strong>reject</strong> the account of <strong>${username}</strong>?`;
          actionConfirmButton.className = 'btn btn-danger';
          break;
      }
      actionModal.show();
    }

    document.querySelectorAll('.approve-btn').forEach(btn => btn.addEventListener('click', () => {
      setActionModal(btn.dataset.userid, btn.dataset.username, 'approve_user');
    }));

    document.querySelectorAll('.disable-btn').forEach(btn => btn.addEventListener('click', () => {
      setActionModal(btn.dataset.userid, btn.dataset.username, 'disable_user');
    }));

    document.querySelectorAll('.enable-btn').forEach(btn => btn.addEventListener('click', () => {
      setActionModal(btn.dataset.userid, btn.dataset.username, 'enable_user');
    }));

    document.querySelectorAll('.reject-btn').forEach(btn => btn.addEventListener('click', () => {
      setActionModal(btn.dataset.userid, btn.dataset.username, 'reject_user');
      actionForm.action = btn.closest('form').action;
    }));

  });
  </script>

</body>
</html>
