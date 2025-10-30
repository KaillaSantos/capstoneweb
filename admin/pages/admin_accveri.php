<?php
// print out error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../includes/passwordVerification.php';
include __DIR__ . '/../includes/sidebar.php';


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
  <link rel="stylesheet" href="/capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="/capstoneweb/user-admin.css">
  <link rel="stylesheet" href="/capstoneweb/user-admin1.css">
</head>

<body>

  <!-- ===== SIDEBAR ===== -->
  <?php include  '../includes/sidebar.php'; ?>

  <!-- ===== TOGGLE BUTTON ===== -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- ===== CONTENT AREA ===== -->
  <div class="content" id="content">

    <!-- content header -->
    <header class="dashboard-header">
      <div class="header-left">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>E-Recycle Account Verification</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>

      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <div class="table-responsive mt-3">
      <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark align-middle">
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
          // Pagination setup
          $records_per_page = 5;

          // Get current page from URL (default 1)
          $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
          $offset = ($page - 1) * $records_per_page;

          // Count total records
          $countQuery = "SELECT COUNT(*) AS total FROM account WHERE role = 'User' AND status = 'not verified'";
          $countResult = mysqli_query($conn, $countQuery);
          $total_records = mysqli_fetch_assoc($countResult)['total'];
          $total_pages = ceil($total_records / $records_per_page);

          // Fetch paginated records
          $sql = "
  SELECT userid, userimg, userName, email, purok, status 
  FROM account 
  WHERE role = 'User' AND status = 'not verified'
  ORDER BY userid DESC
  LIMIT $records_per_page OFFSET $offset
";
          $result = mysqli_query($conn, $sql);
          ?>

        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <?php
              $imagePath = !empty($row['userimg'])
                ? "../../image/" . $row['userimg']
                : "../../image/placeholder.jpg";
              ?>
              <tr>
                <td>
                  <img src="<?= htmlspecialchars($imagePath) ?>" alt="User Image" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                </td>
                <td style="text-transform: capitalize;"><?= htmlspecialchars($row['userName']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['purok']) ?></td>
                <td>
                  <span class="badge bg-warning text-dark">Pending</span>
                </td>
                <td>
                  <?php if ($row['status'] === "not verified"): ?>
                    <form action="../../function/function.php" method="POST" class="d-inline">
                      <input type="hidden" name="userid" value="<?= $row['userid'] ?>">
                      <button type="submit" name="approve_user" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i>
                      </button>
                    </form>
                    <form action="../../function/function.php" method="POST" class="d-inline">
                      <input type="hidden" name="userid" value="<?= $row['userid'] ?>">
                      <button type="submit" name="reject_user" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i>
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted">No Action Available</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted">No users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- PAGINATION -->
      <div class="d-flex justify-content-center mt-3">
        <nav>
          <ul class="pagination">
            <!-- Previous button -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>

            <!-- Page numbers -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <!-- Next button -->
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
  </div>

  </div>
  </div>



  <script src="/capstoneweb/assets/sidebarToggle.js"></script>

  <!-- Verify Password Modal -->
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

</body>

</html>