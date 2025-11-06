<?php
require_once __DIR__ . '/../../includes/authSession.php';
include_once __DIR__ . '/../includes/passwordVerification.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">  
  <title>Recyclables | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb/user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">  
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
   /* === Container: 3 cards per row === */
.container .row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  max-width: 1100px;
  margin: 50px auto;
  padding: 0 20px;
}

/* === Card styling (balanced, not tall) === */
.card {
  background: #f9fff9;
  border-radius: 20px;
  box-shadow: 0 6px 16px rgba(44, 94, 26, 0.15);
  text-align: center;
  padding: 20px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  width: 100%;
  height: 250px; /* ✅ shorter height */
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Hover effect */
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(44, 94, 26, 0.25);
}

/* Decorative header */
.card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  height: 8px;
  width: 100%;
  background: linear-gradient(90deg, #2c5e1a, #7cd957);
  border-radius: 20px 20px 0 0;
}

/* === Icon === */
.eco-icon {
  font-size: 1.8rem;
  color: #2c5e1a;
  background: #e9f9e9;
  border-radius: 50%;
  padding: 8px;
  display: inline-block;
  margin-bottom: 8px;
}

/* === Image === */
.card-img {
  width: 80px;
  height: 80px;
  object-fit: contain;
  margin: 0 auto;
  transition: transform 0.3s ease;
}
.card:hover .card-img {
  transform: scale(1.1);
}

/* === Title === */
.card-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c5e1a;
}

/* === Text === */
.card-body p {
  font-size: 1rem;
  color: #333;
  margin: 0;
}

/* === Responsive Adjustments === */
@media (max-width: 992px) {
  .container .row {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 768px) {
  .container .row {
    grid-template-columns: 1fr;
  }
}

  </style>
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
          <h1>E-Recycle Recyclables</h1>
          <p>Municipality of San Ildefonso</p>
      </div>
      </div>

      <div class="header-right">
      <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
  </header>

    <!-- 🔹 Add New, Add, Reset Button -->
    <div class="d-flex justify-content-between align-items-center">
      <h3 style="padding-left: 50px;">  </h3>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
          <i class="fa fa-plus"></i> Add New
        </button>
      </div>
    </div>


    <!-- adjust Total -->
    <?php
    // ✅ Fetch recyclables with total redeemed quantity
    $query = "SELECT r.id, r.RM_name, r.RM_img, 
         COALESCE(SUM(ri.quantity), 0) AS total_quantity,
         COALESCE(MAX(ri.unit), '') AS unit
          FROM recyclable r
          LEFT JOIN record_items ri ON r.id = ri.recyclable_id
          GROUP BY r.id, r.RM_name, r.RM_img
        ";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
    ?>
      <div class="container mt-4">
        <div class="row justify-content-center">
          <?php while ($rows = mysqli_fetch_assoc($result)) { ?>
            <div>
              <div class="card text-center">
                <div class="card-body">
                  <div class="eco-icon"><i class="fa-solid fa-leaf"></i></div>
                  <h4 class="card-title"><?= $rows['RM_name']; ?></h4>
                  <img src="/capstoneweb/assets/<?= $rows['RM_img'] ?>" class="card-img">
                  <p><i class="fa-solid fa-recycle"></i> Total: <?= $rows['total_quantity']; ?> <?= htmlspecialchars($rows['unit']); ?></p>
                </div>
              </div>
            </div>
        <?php }
        } ?>
        </div>
      </div>


  </div>

  <!-- 🔹 Add Material Modal -->
  <div class="modal fade" id="addMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Recyclable Material</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" action="../../function/function.php" enctype="multipart/form-data">
          <div class="modal-body">
            <label>Material Name:</label>
            <!-- <input type="text" name="RM_name" class="form-control" placeholder="e.g. Plastic Bottle" required> -->
            <select name="RM_name" class="form-control">
              <option value="Plastik">Plastik</option>
              <option value="Bakal">Bakal/Metal</option>
              <option value="Lata">Lata</option>
              <option value="Bote">Bote/Bubog</option>
              <option value="Karton">Karton/Papel</option>
            </select>

            <label class="mt-3">Upload Image:</label>
            <input type="file" name="RM_img" class="form-control" accept="image/*">
          </div>
          <div class="modal-footer">
            <button type="submit" name="add_material" class="btn btn-success">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

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
  <script src="../../assets/sidebarToggle.js"></script>

</body>

</html>