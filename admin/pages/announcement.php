  <?php


  // includes
  require_once __DIR__ . '/../../includes/authSession.php';
  require_once __DIR__ . '/../../conn/dbconn.php';

  // ✅ Check session early
  if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Unauthorized access. Please login.');
    window.location.href='../login.php';</script>";
    exit();
  }

  $userid = $_SESSION['userid']; // ✅ define first

  // Fetch user info
  $query = "SELECT * FROM account WHERE userid = '$userid'";
  $result = mysqli_query($conn, $query);
  $user = mysqli_fetch_assoc($result);

  // include helpers
  require_once __DIR__ . '/../includes/passwordVerification.php';
  require_once __DIR__ . '/../../includes/fetchData.php';

  if (isset($_SESSION['message'])) {
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
  }
  ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, maximum-scale=1.0">
  <title>Announcements | E-Recycle</title>

  <link rel="stylesheet" href="/capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="/capstoneweb/user-admin.css">
  <link rel="stylesheet" href="/capstoneweb/user-admin1.css">

  <style>
    /* ✅ Unified Announcement Card Grid (like rewards.php) */
    .announcement-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 18px;
      padding: 15px;
    }

    .announcement-card {
      border: 1px solid #ccc;
      border-left: 6px solid #2c5e1a;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      transition: 0.2s ease-in-out;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      height: 100%;
    }

    .announcement-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
    }

    .announcement-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 6px 6px 0 0;
    }

    .announcement-body {
      padding: 12px 15px;
      flex-grow: 1;
    }

    .announcement-body h2 {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 6px;
      color: #2c5e1a;
    }

    .announcement-body .date {
      font-size: 0.9rem;
      color: #555;
      margin-bottom: 8px;
    }

    .announcement-text {
      font-size: 0.9rem;
      color: #333;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
    }

    .announcement-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 12px;
      gap: 8px;
    }

    .announcement-actions .btn-link {
      color: #2c5e1a;
      text-decoration: none;
      font-weight: 500;
      padding-left: 0;
    }

    .announcement-actions .btn-link:hover {
      text-decoration: underline;
    }

    .announcement-actions .btn-warning {
      background-color: #ffc107;
      border: none;
      color: #000;
      font-size: 0.85rem;
      border-radius: 6px;
    }

    .announcement-actions .btn-warning:hover {
      background-color: #e0a800;
    }

    input[type="checkbox"] {
      margin-right: 8px;
      accent-color: #2c5e1a;
    }

    /* === Add Announcement Modal === */
    #addAnnouncementModal .modal-content {
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    #addAnnouncementModal label {
      color: #1A4314;
    }

    #addAnnouncementModal .form-control:focus {
      border-color: #2C5E1A;
      box-shadow: 0 0 5px rgba(44, 94, 26, 0.3);
    }

    @media (max-width: 768px) {
      .announcement-card {
        flex-direction: column;
      }
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle Button -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>

  <!-- Overlay -->
  <div class="overlay"></div>

  <!-- Content -->
  <div class="content" id="content">
    <header class="dashboard-header">
      <div class="header-left">
        <img src="/capstoneweb/assets/logo_matimbubong.jpeg" alt="E-Recycle Logo" class="header-logo">
        <div class="header-text">
          <h1>E-Recycle Announcement Page</h1>
          <p>Municipality of San Ildefonso</p>
        </div>
      </div>

      <div class="header-right">
        <span class="date-display"><?php echo date("F j, Y"); ?></span>
      </div>
    </header>

    <!-- Filters and Buttons -->
    <div class="d-flex justify-content-end align-items-center mb-3" style="gap:10px;">
      <form method="get" action="announcement.php" class="d-flex align-items-center mb-0">
        <select name="status" id="status_filter" class="form-select w-auto" onchange="this.form.submit()">
          <option value="Posted" <?= (isset($_GET['status']) && $_GET['status'] == 'Posted') ? 'selected' : '' ?>>All</option>
          <option value="Archived" <?= (isset($_GET['status']) && $_GET['status'] == 'Archived') ? 'selected' : '' ?>>Archived</option>
        </select>
      </form>

      <form method="post" action="../../function/function.php" class="d-flex gap-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
          <i class="fa fa-plus"></i> Add New
        </button>
        <button type="submit" class="btn btn-danger" name="archive_selected">
          <i class="fa-solid fa-box-archive"></i> Archive
        </button>
    </div>

    <!-- Announcement Grid -->
    <div class="announcement-container">
      <?php
      $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'Posted';

      if ($statusFilter === 'All') {
        $sql = "SELECT * FROM announcement ORDER BY announce_date DESC";
      } else {
        $sql = "SELECT * FROM announcement WHERE status = '$statusFilter' ORDER BY announce_date DESC";
      }
      $run = mysqli_query($conn, $sql);

      if (mysqli_num_rows($run) > 0) {
        while ($rows = mysqli_fetch_assoc($run)) {
          $announceImage = !empty($rows['announce_img'])
            ? "../../announceImg/" . $rows['announce_img']
            : "../../announceImg/announcementPlaceholder.jpg";
      ?>
          <div class="announcement-card">
            <input type="checkbox" name="archive_ids[]" value="<?= $rows['announce_id'] ?>">
            <img src="<?= $announceImage ?>" alt="Announcement" class="announcement-img">
            <div class="announcement-body">
              <h2><i class="fa-solid fa-bullhorn"></i> <?= htmlspecialchars($rows['announce_name']) ?></h2>
              <p class="date"><i class="fa-solid fa-calendar"></i> <?= date("F j, Y", strtotime($rows['announce_date'])) ?></p>
              <p class="announcement-text"><i class="fa-regular fa-note-sticky"></i> <?= nl2br(htmlspecialchars($rows['announce_text'])) ?></p>

              <div class="announcement-actions">
                <button type="button" class="btn btn-link read-more-btn"
                  data-title="<?= htmlspecialchars($rows['announce_name']) ?>"
                  data-date="<?= date("F j, Y", strtotime($rows['announce_date'])) ?>"
                  data-text="<?= htmlspecialchars($rows['announce_text']) ?>"
                  data-image="<?= $announceImage ?>">
                  Read More »
                </button>

                <button type="button" class="btn btn-warning btn-sm edit-btn"
                  data-id="<?= $rows['announce_id'] ?>"
                  data-title="<?= htmlspecialchars($rows['announce_name']) ?>"
                  data-text="<?= htmlspecialchars($rows['announce_text']) ?>"
                  data-img="<?= htmlspecialchars($rows['announce_img']) ?>">
                  <i class="fa fa-edit"></i> Edit
                </button>
              </div>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p class='text-center mt-4'>No announcements yet.</p>";
      }
      ?>
    </div>
    </form>
  </div>
    <!-- 📜 Read More Modal -->
    <div class="modal fade" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="readMoreModalLabel">Announcement Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <img id="modalImage" src="" class="img-fluid mb-3 rounded" alt="Announcement Image" style="height: 350px;">
            <h3 id="modalTitle"></h3>
            <p><strong>Date:</strong> <span id="modalDate"></span></p>
            <p id="modalText"></p>
          </div>
        </div>
      </div>
    </div>

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
    </div>

    <!-- 🟢 Add New Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:#1A4314; color:white;">
        <h5 class="modal-title" id="addAnnouncementModalLabel">
          <i class="fa-solid fa-bullhorn"></i> New Announcement
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3">
            <label for="announce_name" class="form-label fw-bold">Announcement Title:</label>
            <input type="text" class="form-control" name="announce_name" placeholder="Enter title" required>
          </div>

          <div class="mb-3">
            <label for="announce_text" class="form-label fw-bold">Announcement Body:</label>
            <textarea class="form-control" name="announce_text" rows="4" placeholder="Enter announcement text" required></textarea>
          </div>

          <div class="mb-3">
            <label for="announce_date" class="form-label fw-bold">Date:</label>
            <input type="date" class="form-control" id="announce_date" name="announce_date" required>
          </div>

          <div class="mb-3">
            <label for="announce_img" class="form-label fw-bold">Upload Image (optional):</label>
            <input type="file" class="form-control" name="announce_img" accept="image/*">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-xmark"></i> Cancel
          </button>
          <button type="submit" class="btn btn-success" name="submit_announcement">
            <i class="fa-solid fa-paper-plane"></i> Post Announcement
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


    <script src="/capstoneweb/assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    <script src="/capstoneweb/assets/sidebarToggle.js"></script>

    <script>
      // Read More Modal handler
      document.querySelectorAll('.read-more-btn').forEach(button => {
        button.addEventListener('click', function() {
          document.getElementById('modalTitle').textContent = this.getAttribute('data-title');
          document.getElementById('modalDate').textContent = this.getAttribute('data-date');
          document.getElementById('modalText').textContent = this.getAttribute('data-text');
          document.getElementById('modalImage').src = this.getAttribute('data-image');

          const modal = new bootstrap.Modal(document.getElementById('readMoreModal'));
          modal.show();
        });
      });

      // Dynamic redirect for password modal
      document.querySelectorAll('[data-bs-target="#verifyPasswordModal"]').forEach(btn => {
        btn.addEventListener('click', () => {
          const redirectInput = document.querySelector('#verifyPasswordModal input[name="redirect"]');
          if (btn.dataset.redirect) {
            redirectInput.value = btn.dataset.redirect;
          }
        });
      });
    </script>

    <script>
      const addAnnouncementModal = document.getElementById('addAnnouncementModal');
      addAnnouncementModal.addEventListener('hidden.bs.modal', function () {
        addAnnouncementModal.querySelector('form').reset();
      });
    </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const today = new Date().toISOString().split("T")[0];
      const announceDate = document.getElementById("announce_date");
      if (announceDate) {
        announceDate.value = today;
        announceDate.setAttribute("min", today); // Optional
      }
    });
  </script>

<!-- Edit Announcement Modal -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title fw-bold" id="editAnnouncementModalLabel">Edit Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form method="post" action="/capstoneweb/function/function.php" enctype="multipart/form-data" id="editAnnouncementForm">
          <input type="hidden" name="announce_id" id="edit_announce_id">

          <div class="mb-3">
            <label for="edit_announce_name" class="form-label fw-semibold">Announcement Title:</label>
            <input type="text" class="form-control" id="edit_announce_name" name="announce_name" required>
          </div>

          <div class="mb-3">
            <label for="edit_announce_text" class="form-label fw-semibold">Announcement Body:</label>
            <textarea class="form-control" id="edit_announce_text" name="announce_text" rows="4" required></textarea>
          </div>

          <!-- Removed date input -->

          <div class="mb-3">
            <label for="edit_announce_img" class="form-label fw-semibold">Upload Image (optional):</label>
            <input type="file" class="form-control" id="edit_announce_img" name="announce_img" accept="image/*">
          </div>

          <div class="mb-3" id="currentImageContainer" style="display: none;">
            <label class="form-label fw-semibold">Current / Preview Image:</label><br>
            <img id="currentImage" src="" alt="Current Image"
                 style="width: 150px; height: auto; border-radius: 8px; border: 2px solid #333; cursor: pointer;"
                 title="Click to enlarge">
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" name="update_announcement" class="btn btn-warning fw-semibold">
              <i class="fa fa-save"></i> Update Announcement
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- 🟦 Image Preview Lightbox Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-transparent border-0 text-center">
      <img id="largePreview" src="" alt="Preview" class="img-fluid rounded shadow" style="max-height: 90vh;">
    </div>
  </div>
</div>

<script>
// 🟨 Handle edit button click
document.querySelectorAll('.edit-btn').forEach(button => {
  button.addEventListener('click', function() {
    const id = this.getAttribute('data-id');
    const title = this.getAttribute('data-title');
    const text = this.getAttribute('data-text');
    const img = this.getAttribute('data-img');

    // Fill modal fields
    document.getElementById('edit_announce_id').value = id;
    document.getElementById('edit_announce_name').value = title;
    document.getElementById('edit_announce_text').value = text;

    const imgContainer = document.getElementById('currentImageContainer');
    const currentImage = document.getElementById('currentImage');

    if (img && img.trim() !== '') {
      currentImage.src = `/capstoneweb/announceImg/${img}`;
      imgContainer.style.display = 'block';
    } else {
      imgContainer.style.display = 'none';
    }

    // Reset file input preview
    document.getElementById('edit_announce_img').value = '';

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editAnnouncementModal'));
    modal.show();
  });
});

// 🟩 Live preview when selecting a new image
document.getElementById('edit_announce_img').addEventListener('change', function(event) {
  const file = event.target.files[0];
  const imgContainer = document.getElementById('currentImageContainer');
  const currentImage = document.getElementById('currentImage');

  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      currentImage.src = e.target.result;
      imgContainer.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    imgContainer.style.display = 'none';
  }
});

// 🟦 Click image to enlarge
document.getElementById('currentImage').addEventListener('click', function() {
  const largePreview = document.getElementById('largePreview');
  largePreview.src = this.src;
  const previewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
  previewModal.show();
});
</script>


  </body>

  </html>