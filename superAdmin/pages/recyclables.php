<?php
require_once __DIR__ . '/../../includes/authSession.php';
include_once __DIR__ . '/../includes/passwordVerification.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Recyclables | E-Recycle</title>
  <link rel="stylesheet" href="\capstoneweb\user-admin.css">
  <link rel="stylesheet" href="\capstoneweb/user-admin1.css">
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="\capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="stylesheet" href="\capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">

  <style>
/* ===== Page Background (animated bubbles) ===== */
body {
  background: linear-gradient(135deg, #e8f5e9, #f9fff9);
  overflow-x: hidden;
  font-family: 'Poppins', sans-serif;
}

/* floating bubbles */
.bubble {
  position: fixed;
  bottom: -100px;
  background: rgba(76, 175, 80, 0.2);
  border-radius: 50%;
  animation: rise 12s infinite ease-in;
  z-index: 0;
}
@keyframes rise {
  0% { transform: translateY(0) scale(1); opacity: 0.7; }
  100% { transform: translateY(-1200px) scale(1.3); opacity: 0; }
}

/* === Recyclables Container === */
.container .row {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 25px;
  max-width: 1200px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

/* === Card Styling === */
.card {
  border: none;
  border-radius: 18px;
  background: #fff;
  box-shadow: 0 5px 15px rgba(44, 94, 26, 0.15);
  text-align: center;
  padding: 25px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

/* hover effect */
.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 25px rgba(44, 94, 26, 0.25);
}

/* bubble accent */
.card::after {
  content: '';
  position: absolute;
  top: -50px;
  right: -50px;
  width: 100px;
  height: 100px;
  background: rgba(76, 175, 80, 0.1);
  border-radius: 50%;
  z-index: 0;
}

/* image */
.card-img {
  width: 140px;
  height: 140px;
  object-fit: contain;
  margin: 0 auto 15px auto;
  position: relative;
  z-index: 1;
  transition: transform 0.3s ease;
}
.card:hover .card-img { transform: scale(1.08); }

/* text */
.card-title {
  font-weight: 700;
  color: #2c5e1a;
  font-size: 1.3rem;
  margin-bottom: 5px;
  z-index: 1;
  position: relative;
}
.card p {
  color: #333;
  font-size: 1rem;
  margin-top: 5px;
  position: relative;
  z-index: 1;
}

/* fade-up animation */
.card {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeUp 0.6s ease forwards;
}
@keyframes fadeUp {
  to { opacity: 1; transform: translateY(0); }
}

/* responsive tweaks */
@media (max-width: 768px) {
  .container .row {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }
  .card-img {
    width: 100px;
    height: 100px;
  }
}

  </style>
</head>

<body>
  <!-- Background bubbles -->
  <?php for ($i=0; $i<10; $i++) { 
    $size = rand(40,100); $left = rand(0,100);
    $delay = rand(0,10); $dur = rand(10,20);
    echo "<div class='bubble' style='width:{$size}px;height:{$size}px;left:{$left}%;animation-delay:{$delay}s;animation-duration:{$dur}s;'></div>";
  } ?>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>
  <div class="overlay"></div>

  <div class="content" id="content">
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

    <div class="d-flex justify-content-between align-items-center">
      <h3 class="ms-5"></h3>
      <div class="d-flex gap-2 me-4">
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
          <i class="fa fa-plus"></i> Add New
        </button>
      </div>
    </div>

    <?php
    $query = "SELECT r.id, r.RM_name, r.RM_img, 
                     COALESCE(SUM(ri.quantity), 0) AS total_quantity,
                     COALESCE(MAX(ri.unit), '') AS unit
              FROM recyclable r
              LEFT JOIN record_items ri ON r.id = ri.recyclable_id
              GROUP BY r.id, r.RM_name, r.RM_img";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
      echo '<div class="container mt-4"><div class="row">';
      while ($rows = mysqli_fetch_assoc($result)) { ?>
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="/capstoneweb/assets/<?= $rows['RM_img']; ?>" alt="<?= htmlspecialchars($rows['RM_name']); ?>" class="card-img">
            <h4 class="card-title"><?= htmlspecialchars($rows['RM_name']); ?></h4>
            <p><i class="fa-solid fa-recycle"></i> Total: <?= $rows['total_quantity']; ?> <?= htmlspecialchars($rows['unit']); ?></p>
          </div>
        </div>
      <?php }
      echo '</div></div>';
    }
    ?>
  </div>

  <!-- Add Material Modal -->
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
            <select name="RM_name" class="form-control">
              <option value="Plastik">Plastik</option>
              <option value="Bakal">Bakal / Metal</option>
              <option value="Lata">Lata</option>
              <option value="Bote">Bote / Bubog</option>
              <option value="Karton">Karton / Papel</option>
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
  <script src="../../assets/sidebarToggle.js"></script>
</body>
</html>
