<?php
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../../includes/fetchData.php';
require_once __DIR__ . '/../includes/passwordVerification.php';

// Get logged-in user ID
$userid = $_SESSION['userid'] ?? null;

// Store all user records here
$userRecords = [];

if ($userid) {
    // ✅ Get all record IDs belonging to this user
    $recordIds = [];
    $sql = "SELECT id FROM records WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recordIds[] = $row['id'];
    }

    // ✅ Reuse your fetchData.php logic style
    if (!empty($recordIds)) {
        $ids = implode(",", $recordIds);
        $sql = "
            SELECT r.id, r.date, r.record_name, r.rec_img, ri.recyclable_id, ri.quantity, ri.unit
            FROM records r
            LEFT JOIN record_items ri ON r.id = ri.record_id
            WHERE r.id IN ($ids)
            ORDER BY r.date DESC
        ";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            if (!isset($userRecords[$id])) {
                $userRecords[$id] = [
                    'date' => $row['date'],
                    'name' => $row['record_name'],
                    'rec_img' => $row['rec_img'],
                    'items' => array_fill_keys(array_keys($categories), ['qty' => 0, 'unit' => ''])
                ];
            }

            if ($row['recyclable_id']) {
                $userRecords[$id]['items'][$row['recyclable_id']] = [
                    'qty' => $row['quantity'],
                    'unit' => $row['unit']
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Your Records | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb/user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png"> 
  <link rel="stylesheet" href="assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle Button -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <div class="overlay"></div>

  <!-- Page Content -->
  <div class="content" id="content">
    <header class="dashboard-header">
      <div class="header-left">
        <img src="\capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>My E-Recycle Records</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>

      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <!-- Records Table -->
    <div class="table-responsive mt-4">
      <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark align-middle">
          <tr>
            <th>Date</th>
            <th>Household Name</th>
            <?php foreach ($categories as $catId => $catName): ?>
              <th><?= htmlspecialchars($catName) ?></th>
            <?php endforeach; ?>
            <th>Image</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($userRecords)): ?>
            <?php foreach ($userRecords as $rec): ?>
              <tr>
                <td><?= htmlspecialchars($rec['date']) ?></td>
                <td style="text-transform: capitalize;"><?= htmlspecialchars($rec['name']) ?></td>

                <?php foreach ($categories as $catId => $catName): ?>
                  <?php
                  $item = $rec['items'][$catId];
                  $display = $item['qty'] > 0 ? $item['qty'] . " " . htmlspecialchars($item['unit']) : "0";
                  ?>
                  <td><?= $display ?></td>
                <?php endforeach; ?>

                <td>
                  <?php if (!empty($rec['rec_img'])): ?>
                    <a href="#" class="btn btn-small btn-success viewImageBtn" 
                       data-bs-toggle="modal" 
                       data-bs-target="#imageModal"
                       data-img="../../assets/proofs/<?= htmlspecialchars($rec['rec_img']) ?>" 
                       data-name="<?= htmlspecialchars($rec['name']) ?>">
                       <i class="fa-solid fa-eye"></i>
                    </a>
                  <?php else: ?>
                    no image uploaded
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?= count($categories) + 3 ?>" class="text-center py-5 fs-5 fw-bold">No records found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
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

    <!-- toggle -->
  <script src="\capstoneweb/assets/sidebarToggle.js"></script>

  <!-- 🔐 Password Verification Modal -->
  <div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="">
          <input type="hidden" name="redirect" value="/capstoneweb/admin/pages/accsetting.php">
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


</body>
</html>
