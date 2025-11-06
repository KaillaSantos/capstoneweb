<?php
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../includes/passwordVerification.php';
require_once __DIR__ . '/../../includes/fetchData.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Record | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb/user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

</head>

<body>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle Button (visible on all screens) -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- Overlay (for mobile view) -->
  <div class="overlay"></div>

  <!-- Page Content -->
  <div class="content" id="content">
    
    <!-- content header -->
     <header class="dashboard-header">
        <div class="header-left">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
            <h1>E-Recycle Records</h1>
            <p>Municipality of San Ildefonso</p>
        </div>
        </div>

        <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
        </div>
    </header>

    <div class="d-flex justify-content-between align-items-center" style="padding-bottom: 5px;">
      <h3 style="padding-left: 50px;"> </h3>

      <div class="d-flex gap-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addRecordModal">
          <i class="fa fa-plus"></i> Add Record
        </button>
        <form action="\capstoneweb\function/function.php" method="post" class="d-inline">
          <button type="submit" name="reset_data" class="btn btn-danger" onclick="return confirm('Are you sure you want to reset all data? This cannot be undone.');"><i class="fa-solid fa-trash-can-arrow-up"></i> Reset </button>
        </form>
      </div>
    </div>

    <div class="table-responsive mt-3">
      <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark align-middle">
          <tr>
            <!-- Date -->
            <th>
              <div class="d-flex align-items-center justify-content-between">
                <span>Date</span>
                <div class="d-flex flex-column ms-1">
                  <a href="?sort=date_asc" class="text-light text-decoration-none small">
                    <i class="fa-solid fa-caret-up <?= ($sort == 'date_asc') ? 'text-warning' : '' ?>"></i>
                  </a>
                  <a href="?sort=date_desc" class="text-light text-decoration-none small">
                    <i class="fa-solid fa-caret-down <?= ($sort == 'date_desc') ? 'text-warning' : '' ?>"></i>
                  </a>
                </div>
              </div>
            </th>

            <!-- Household Name -->
            <th>
              <div class="d-flex align-items-center justify-content-between">
                <span>Household Name</span>
                <div class="d-flex flex-column ms-1">
                  <a href="?sort=name_asc" class="text-light text-decoration-none small">
                    <i class="fa-solid fa-caret-up <?= ($sort == 'name_asc') ? 'text-warning' : '' ?>"></i>
                  </a>
                  <a href="?sort=name_desc" class="text-light text-decoration-none small">
                    <i class="fa-solid fa-caret-down <?= ($sort == 'name_desc') ? 'text-warning' : '' ?>"></i>
                  </a>
                </div>
              </div>
            </th>

            <!-- Category Columns with sorting -->
            <?php foreach ($categories as $catId => $catName): ?>
              <th>
                <div class="d-flex align-items-center justify-content-between">
                  <span><?= htmlspecialchars($catName) ?></span>
                  <div class="d-flex flex-column ms-1">
                    <a href="?sort=cat_<?= $catId ?>_asc" class="text-light text-decoration-none small">
                      <i class="fa-solid fa-caret-up <?= ($sort == 'cat_' . $catId . '_asc') ? 'text-warning' : '' ?>"></i>
                    </a>
                    <a href="?sort=cat_<?= $catId ?>_desc" class="text-light text-decoration-none small">
                      <i class="fa-solid fa-caret-down <?= ($sort == 'cat_' . $catId . '_desc') ? 'text-warning' : '' ?>"></i>
                    </a>
                  </div>
                </div>
              </th>
            <?php endforeach; ?>

            <th>Image</th>
          </tr>
        </thead>
        </thead>
        <tbody>
          <?php if (!empty($records)): ?>
            <?php foreach ($records as $rec): ?>
              <tr>
                <td><?= $rec['date'] ?></td>
                <td style="text-transform: capitalize;"><?= htmlspecialchars($rec['name']) ?></td>
                <?php foreach ($categories as $catId => $catName): ?>
                  <?php
                  $item = $rec['items'][$catId];
                  $display = $item['qty'] > 0 ? $item['qty'] . " " . htmlspecialchars($item['unit']) : "0";
                  ?>
                  <td><?= $display ?></td>
                <?php endforeach; ?>
                <td><?php if (!empty($rec['rec_img'])) {
                    ?>
                    <a href="#" class="btn btn-small btn-success viewImageBtn" data-bs-toggle="modal" data-bs-target="#imageModal"
                      data-img="../../assets/proofs/<?= htmlspecialchars($rec['rec_img']) ?>" data-name="<?= htmlspecialchars($rec['name']) ?>"><i class="fa-solid fa-eye" style=" width: 50px;"></i></a>
                  <?php
                    } else {
                      echo 'no image uploaded';
                    }  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?= count($categories) + 3 ?>" class="text-center">No records found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3">
        <nav>
          <ul class="pagination">
            <?php if ($page > 1): ?>
              <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1 ?>&userid=<?= urlencode($userid) ?>">← Prev</a>
              </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&userid=<?= urlencode($userid) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
              <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1 ?>&userid=<?= urlencode($userid) ?>">Next →</a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>
    </div>
  </div>
  </div>


  <!-- Image Modal -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="modalName" class="mb-3"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="modalImage" src="" alt="Record Image" class="img-fluid rounded">
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const modalImage = document.getElementById("modalImage");
      const modalName = document.getElementById("modalName");

      document.querySelectorAll(".viewImageBtn").forEach(btn => {
        btn.addEventListener("click", function() {
          const imgSrc = this.getAttribute("data-img");
          const name = this.getAttribute("data-name");

          modalImage.src = imgSrc;
          modalName.textContent = name;
        });
      });
    });
  </script>

  <!-- Verify Password Modal -->
  <div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="">
          <input type="hidden" name="userid" value="<?= htmlspecialchars($_SESSION['userid']); ?>">
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

  <!-- toggle -->
  <script src="\capstoneweb/assets/sidebarToggle.js"></script>

  <!-- 🔹 Add Record Modal -->
<div class="modal fade" id="addRecordModal" tabindex="-1" aria-labelledby="addRecordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="addRecordModalLabel">Add New Record</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      
      <form method="POST" action="/capstoneweb/function/function.php" enctype="multipart/form-data">
        <div class="modal-body">
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Record Name</label>
              <input type="text" name="record_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" name="date" class="form-control" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Select User</label>
            <select name="user_id" class="form-select" required>
              <option value="">-- Choose User --</option>
              <?php
                $userQuery = "SELECT userid, userName FROM account WHERE userRole = 'user'";
                $userResult = mysqli_query($conn, $userQuery);
                while ($user = mysqli_fetch_assoc($userResult)) {
                  echo "<option value='{$user['userid']}'>{$user['userName']}</option>";
                }
              ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Upload Proof Image (optional)</label>
            <input type="file" name="rec_img" class="form-control" accept="image/*">
          </div>

          <hr>
          <h6>Select Materials</h6>
          <?php
            $recyclables = mysqli_query($conn, "SELECT * FROM recyclable");
            while ($row = mysqli_fetch_assoc($recyclables)) {
          ?>
            <div class="row align-items-center mb-2">
              <div class="col-md-4"><?= htmlspecialchars($row['RM_name']); ?></div>
              <div class="col-md-4">
                <input type="number" step="0.01" name="materials[<?= $row['id']; ?>][quantity]" class="form-control" placeholder="Quantity">
              </div>
              <div class="col-md-4">
                <select name="materials[<?= $row['id']; ?>][unit]" class="form-select">
                  <option value="kg">kg</option>
                  <option value="pcs">pcs</option>
                </select>
              </div>
            </div>
          <?php } ?>

        </div>
        <div class="modal-footer">
          <button type="submit" name="submit_redeem" class="btn btn-success">Save Record</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>



</body>

</html>