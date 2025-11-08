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


    <?php if (isset($_SESSION['message'])): ?>
      <div class="container mt-3">
        <div class="alert alert-<?= $_SESSION['alert_type'] ?? 'info' ?> alert-dismissible fade show shadow-sm" role="alert">
          <?= htmlspecialchars($_SESSION['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
      <?php unset($_SESSION['message'], $_SESSION['alert_type']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold text-center mb-0">Manage Accounts</h4>
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
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold text-center">Account Verification</h4>
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

          // ===== COUNT TOTAL RECORDS =====
          $countQuery = "SELECT COUNT(*) AS total FROM account WHERE role = 'admin' AND status = 'pending'";
          $countResult = mysqli_query($conn, $countQuery);
          $total_records = mysqli_fetch_assoc($countResult)['total'];
          $total_pages = ceil($total_records / $records_per_page);

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
                  <form action="../../function/function.php" method="POST" class="d-inline">
                    <input type="hidden" name="userid" value="<?= htmlspecialchars($row['userid']) ?>">
                    <button type="submit" name="reject_user" class="btn btn-danger btn-sm" title="Reject">
                      <i class="fa fa-times"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php
            endwhile;
          else:
            ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-4">No unverified users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- ===== PAGINATION ===== -->
      <?php if ($total_pages > 1): ?>
        <div class="d-flex justify-content-center mt-3">
          <nav>
            <ul class="pagination">
              <!-- Prev -->
              <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
              </li>

              <!-- Page numbers -->
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <!-- Next -->
              <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
              </li>
            </ul>
          </nav>
        </div>
      <?php endif; ?>
      </div>
    </div>

    <!-- ACCOUNT DISSABLING TABLE-->
    <div class="table-responsive mt-4">
      <?php
      // ===== PAGINATION =====
      $records_per_page = 5;
      $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
      $offset = ($page - 1) * $records_per_page;

      // ===== COUNT TOTAL RECORDS =====
      $countQuery = "
        SELECT COUNT(*) AS total 
        FROM account 
        WHERE status = 'approved' 
        AND (role = 'admin' OR role = 'user')
      ";
      $countResult = mysqli_query($conn, $countQuery);
      $total_records = mysqli_fetch_assoc($countResult)['total'];
      $total_pages = ceil($total_records / $records_per_page);

      // ===== FETCH APPROVED ADMINS + USERS =====
      $sql = "
        SELECT userid, userimg, userName, email, purok, status, role
        FROM account 
        WHERE status = 'approved'
        AND (role = 'admin' OR role = 'user')
        ORDER BY userid DESC
        LIMIT $records_per_page OFFSET $offset
      ";
      $result = mysqli_query($conn, $sql);
      ?>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-center">Approved Accounts</h4>
      </div>

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
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <?php
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
                <td class="text-capitalize">
                  <?php if ($row['role'] === 'admin'): ?>
                    <span class="badge bg-primary">Admin</span>
                  <?php else: ?>
                    <span class="badge bg-success">User</span>
                  <?php endif; ?>
                </td>
                <td><span class="badge bg-success">Approved</span></td>
                <td>
                  <form action="../../function/function.php" method="POST" class="d-inline">
                    <input type="hidden" name="userid" value="<?= htmlspecialchars($row['userid']) ?>">
                    <button type="submit" name="disable_user" class="btn btn-warning btn-sm" title="Disable Account" onclick="return confirm('Are you sure you want to disable this account?');">
                      <i class="fa fa-ban"></i> Disable
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-4">No approved admin or user accounts found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- ===== PAGINATION ===== -->
      <?php if ($total_pages > 1): ?>
        <div class="d-flex justify-content-center mt-3">
          <nav>
            <ul class="pagination">
              <!-- Prev -->
              <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
              </li>

              <!-- Page numbers -->
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <!-- Next -->
              <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
              </li>
            </ul>
          </nav>
        </div>
      <?php endif; ?>
    </div>

    <!-- DISSABLED ACCOUNT -->
    <div id="disabledTableContainer" class="table-section" style="display:none;">
      <?php
      $sql = "
        SELECT userid, userimg, userName, email, purok, status, role
        FROM account 
        WHERE status = 'disabled'
        ORDER BY userid DESC
      ";
      $result = mysqli_query($conn, $sql);
      ?>
      <h4 class="fw-bold text-center mb-3">Disabled Accounts</h4>
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
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                $imagePath = !empty($row['userimg'])
                  ? "../../image/" . htmlspecialchars($row['userimg'])
                  : "../../image/placeholder.jpg";
                ?>
                <tr>
                  <td><img src="<?= $imagePath ?>" class="img-thumbnail rounded" style="width: 60px; height: 60px; object-fit: cover;"></td>
                  <td class="text-capitalize"><?= htmlspecialchars($row['userName']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['purok']) ?></td>
                  <td class="text-capitalize"><?= htmlspecialchars($row['role']) ?></td>
                  <td><span class="badge bg-secondary">Disabled</span></td>
                  <td>
                    <form action="../../function/function.php" method="POST" class="d-inline">
                      <input type="hidden" name="userid" value="<?= htmlspecialchars($row['userid']) ?>">
                      <button type="submit" name="enable_user" class="btn btn-success btn-sm" title="Enable Account">
                        <i class="fa fa-check"></i> Enable
                      </button>
                    </form>
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

  <!-- ===== JS ===== -->
  <script src="/capstoneweb/assets/sidebarToggle.js"></script>
  <script src="/capstoneweb/assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const alert = document.querySelector(".alert");
      if (alert) {
        setTimeout(() => {
          const fade = new bootstrap.Alert(alert);
          fade.close();
        }, 3000);
      }
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Auto-close alert after 3s
      const alert = document.querySelector(".alert");
      if (alert) {
        setTimeout(() => new bootstrap.Alert(alert).close(), 3000);
      }

      // Handle approve modal
      const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
      const approveUserName = document.getElementById('approveUserName');
      const approveUserId = document.getElementById('approveUserId');

      document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', () => {
          const userId = button.dataset.userid;
          const userName = button.dataset.username;
          approveUserName.textContent = userName;
          approveUserId.value = userId;
          approveModal.show();
        });
      });
    });
  </script>

  <script>
document.addEventListener("DOMContentLoaded", () => {
  const dropdownItems = document.querySelectorAll(".dropdown-item");
  const sections = {
    pending: document.getElementById("pendingTableContainer"),
    approved: document.getElementById("approvedTableContainer"),
    disabled: document.getElementById("disabledTableContainer")
  };

  dropdownItems.forEach(item => {
    item.addEventListener("click", e => {
      e.preventDefault();

      // Remove active state from all
      dropdownItems.forEach(i => i.classList.remove("active"));
      item.classList.add("active");

      // Hide all tables
      Object.values(sections).forEach(sec => sec.style.display = "none");

      // Show selected table
      const selected = item.dataset.table;
      if (sections[selected]) sections[selected].style.display = "block";

      // Update dropdown text
      document.getElementById("filterDropdown").textContent = item.textContent;
    });
  });
});
</script>


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

  <!-- ===== SECONDARY APPROVAL MODAL ===== -->
  <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="../../function/function.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="approveModalLabel">Confirm Account Approval</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to <strong>approve</strong> the account of:</p>
            <h6 class="text-success" id="approveUserName"></h6>
            <input type="hidden" name="userid" id="approveUserId">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="approve_user" class="btn btn-success">Confirm Approve</button>
          </div>
        </form>
      </div>
    </div>
  </div>




</body>

</html>