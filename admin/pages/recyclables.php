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
    /* === Recyclables Container (2 per row layout) === */
    .container .row {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    /* === Card Styling (modern, announcement-inspired look) === */
    .card {
      flex: 1 1 calc(50% - 20px); /* two cards per row */
      display: flex;
      align-items: center;
      gap: 20px;
      background-color: #ffffff;
      border: 1px solid #e6f4ea;
      border-left: 6px solid #2c5e1a;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(44, 94, 26, 0.1);
      padding: 20px;
      transition: transform 0.2s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 10px rgba(44, 94, 26, 0.2);
    }

    /* Image on the left */
    .card-img {
      width: 250px;
      height: 250;
      flex-shrink: 0;
    }

    /* === Card Body (text area) === */
    .card-body {
      text-align: left;
      padding: 0;
    }

    .card-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: #2c5e1a;
      margin-bottom: 5px;
    }

    .card-body p {
      font-size: 1rem;
      color: #333;
      margin: 0;
      text-align: center;
    }

    /* === Responsive Design === */
    @media (max-width: 992px) {
      .card {
        flex: 1 1 100%; /* stack to 1 per row on smaller screens */
      }
    }

    @media (max-width: 768px) {
      .card {
        flex-direction: column;
        text-align: center;
        align-items: center;
      }

      .card-img {
        width: 90px;
        margin-bottom: 10px;
      }

      .card-body {
        text-align: center;
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
            <div class="col-md-4 mb-4">
              <div class="card text-center">
                <div class="card-body">
                  <h4 class="card-title" style=" border-left: 5px soild green"><?= $rows['RM_name']; ?></h4>
                  <img src="/capstoneweb/assets/<?= $rows['RM_img'] ?>" class="card-img">
                  <p class="mt-3" style="font-size: 20px;">
                    Total: <?= $rows['total_quantity']; ?> <?= htmlspecialchars($rows['unit']); ?>
                  </p>
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
        <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data">
          <div class="modal-body">
            <label>Material Name:</label>
            <input type="text" name="RM_name" class="form-control" placeholder="e.g. Plastic Bottle" required>

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