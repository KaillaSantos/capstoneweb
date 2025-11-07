<?php
// announcement.php - complete fixed file

// includes
require_once __DIR__ . '/../../includes/authSession.php';
require_once __DIR__ . '/../../conn/dbconn.php';

// ✅ Check session early
if (!isset($_SESSION['userid'])) {
  echo "<script>alert('Unauthorized access. Please login.'); window.location.href='../login.php';</script>";
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
  <title>Announcement | E-Recycle</title>

  <!-- CSS + Icons -->
  <link rel="stylesheet" href="/capstoneweb/assets/fontawesome-free-7.0.1-web/css/all.min.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-5.3.7-dist/css/bootstrap.css">
  <link rel="stylesheet" href="/capstoneweb/assets/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <link rel="icon" type="image/x-icon" href="/capstoneweb/assets/E-Recycle_Logo_with_Green_and_Blue_Palette-removebg-preview.png">
  <link rel="stylesheet" href="/capstoneweb/user-admin.css">
  <link rel="stylesheet" href="/capstoneweb/user-admin1.css">

  <style>
    /* === Bento Grid Styling === */
    .announcement-section {
      padding: 28px;
      background: #f9fafb;
      font-family: 'Poppins', sans-serif;
    }

    .announcement-title {
      text-align: left;
      font-size: 1.5rem;
      font-weight: 700;
      color: #333;
      margin: 0 0 18px 8px;
    }

    .bento-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      grid-auto-rows: 220px;
      gap: 16px;
    }

    /* === Cards === */
    .bento-card {
      position: relative;
      overflow: hidden;
      border-radius: 14px;
      background: #fff;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      transition: transform 0.28s ease, box-shadow 0.28s ease;
      cursor: pointer;
      z-index: 1;
    }

    .bento-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.14);
    }

    .bento-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: brightness(0.72);
      transition: filter 0.28s ease;
      display: block;
    }

    .bento-card:hover img {
      filter: brightness(0.92);
    }

    /* === Full-width gradient overlay === */
    .bento-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      padding: 12px 14px;
      color: #fff;
      background: linear-gradient(180deg, rgba(0, 0, 0, 0.0) 0%, rgba(0, 0, 0, 0.45) 40%, rgba(0, 0, 0, 0.75) 100%);
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: nowrap;
      gap: 10px;
      z-index: 2;
    }

    .bento-text {
      max-width: calc(100% - 90px);
      overflow: hidden;
    }

    .bento-text h3 {
      font-size: 1rem;
      font-weight: 600;
      margin: 0 0 4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .bento-text p {
      font-size: 0.82rem;
      margin: 0;
      color: rgba(255, 255, 255, 0.95);
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* === Edit Button === */
    .bento-overlay .btn-warning {
      background: #ffc107;
      border: none;
      color: #000;
      font-size: 0.78rem;
      border-radius: 6px;
      padding: 6px 9px;
      z-index: 3;
      /* above overlay */
      cursor: pointer;
    }

    .bento-overlay .btn-warning:hover {
      background: #e0a800;
    }

    /* === Full overlay button (Read More) === */
    .overlay-readmore-btn {
      position: absolute;
      inset: 0;
      z-index: 4;
      background: transparent;
      border: none;
      cursor: pointer;
    }

    .overlay-readmore-btn:focus {
      outline: none;
      box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.12);
    }

    .overlay-readmore-btn:hover {
      background: rgba(0, 0, 0, 0.12);
    }

    /* === Card Size Variations === */
    .bento-card.large {
      grid-row: span 2;
      grid-column: span 2;
    }

    .bento-card.medium {
      grid-row: span 1;
      grid-column: span 1;
    }

    .bento-card.small {
      grid-row: span 1;
      grid-column: span 1;
    }

    .bento-card.wide {
      grid-column: span 2;
    }

    /* 🧩 Fix overlay overlap issue */
    .announcement-card {
      position: relative;
      overflow: hidden;
    }

    .announcement-card .read-more-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      background: linear-gradient(transparent, rgba(0, 0, 0, 0.6));
      opacity: 0;
      transition: opacity 0.3s ease-in-out;
      z-index: 1;
      /* keep it below the edit button */
    }

    .announcement-card:hover .read-more-overlay {
      opacity: 1;
    }

    /* 🟩 Ensure Edit button stays on top */
    .announcement-card .edit-btn {
      position: relative;
      z-index: 2;
    }


    /* === Responsive tweaks === */
    @media (max-width: 768px) {

      .bento-card.large,
      .bento-card.wide {
        grid-column: span 1;
        grid-row: span 1;
      }

      .announcement-section {
        padding: 16px;
      }

      .announcement-title {
        font-size: 1.25rem;
        margin-left: 4px;
      }
    }

    /* === Add Announcement Modal styling (kept) === */
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

    /* === Read More Modal - ensure it's above overlays/backdrops === */
    #readMoreModal {
      z-index: 99999 !important;
    }

    #readMoreModal .modal-dialog {
      z-index: 100000 !important;
    }

    #readMoreModal .modal-content {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35);
      background: #fff;
    }

    #readMoreModal .modal-header {
      background: #1A4314;
      color: #fff;
      border-bottom: none;
    }

    #readMoreModal .modal-body {
      padding: 1.25rem;
    }

    #readMoreModal img {
      width: 100%;
      height: auto;
      max-height: 420px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: .75rem;
    }

    #readMoreModal .btn-close {
      filter: invert(1);
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Sidebar Toggle -->
  <button id="toggleSidebar"><i class="fa fa-bars"></i></button>
  <div class="overlay"></div>

  <!-- Main content -->
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

    <!-- Filter + Add Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div></div>
      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <form method="get" action="announcement.php" class="d-flex align-items-center">
          <select name="status" id="status_filter" class="form-select w-auto" onchange="this.form.submit()">
            <option value="Posted" <?= (isset($_GET['status']) && $_GET['status'] == 'Posted') ? 'selected' : '' ?>>All</option>
            <option value="Archived" <?= (isset($_GET['status']) && $_GET['status'] == 'Archived') ? 'selected' : '' ?>>Archived</option>
          </select>
        </form>

        <form method="post" action="../../function/function.php" class="d-inline">
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
            <i class="fa fa-plus"></i> Add New Announcement
          </button>
          <button type="submit" class="btn btn-danger" name="archive_selected">
            <i class="fa-solid fa-box-archive"></i> Archive
          </button>
        </form>
      </div>
    </div>

    <!-- Bento Grid Announcements -->
    <section class="announcement-section">
      <h2 class="announcement-title">Latest Announcements</h2>
      <div class="bento-grid">
        <?php
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'Posted';
        if ($statusFilter === 'All') {
          $sql = "SELECT * FROM announcement ORDER BY announce_date DESC";
        } else {
          $sql = "SELECT * FROM announcement WHERE status = '$statusFilter' ORDER BY announce_date DESC";
        }
        $run = mysqli_query($conn, $sql);
        if (mysqli_num_rows($run) > 0) {
          $cardSizes = ['large', 'medium', 'small', 'wide', 'medium', 'small']; // pattern
          $i = 0;
          while ($rows = mysqli_fetch_assoc($run)) {
            $announceImage = !empty($rows['announce_img']) ? "../../announceImg/" . $rows['announce_img'] : "../../announceImg/announcementPlaceholder.jpg";
            $size = $cardSizes[$i % count($cardSizes)];
            $i++;
        ?>
            <div class="bento-card <?= htmlspecialchars($size) ?>"
              data-title="<?= htmlspecialchars($rows['announce_name']) ?>"
              data-date="<?= date("F j, Y", strtotime($rows['announce_date'])) ?>"
              data-text="<?= htmlspecialchars($rows['announce_text']) ?>"
              data-image="<?= htmlspecialchars($announceImage) ?>">
              <img src="<?= htmlspecialchars($announceImage) ?>" alt="Announcement">

              <div class="bento-overlay">
                <div class="bento-text">
                  <h3><i class="fa-solid fa-bullhorn"></i> <?= htmlspecialchars($rows['announce_name']) ?></h3>
                  <p><i class="fa-solid fa-calendar"></i> <?= date("F j, Y", strtotime($rows['announce_date'])) ?></p>
                </div>

                <button type="button" class="btn btn-warning btn-sm edit-btn"
                  data-id="<?= htmlspecialchars($rows['announce_id']) ?>"
                  data-title="<?= htmlspecialchars($rows['announce_name']) ?>"
                  data-text="<?= htmlspecialchars($rows['announce_text']) ?>"
                  data-img="<?= htmlspecialchars($rows['announce_img']) ?>">
                  <i class="fa fa-edit"></i> Edit
                </button>
              </div>

              <div class="announcement-card">
                <img src="<?= $announceImage ?>" alt="Announcement" class="announcement-img">


                <!-- Full overlay button (Read More) -->
                <button type="button" class="overlay-readmore-btn read-more-btn"
                  aria-label="Read more"
                  data-title="<?= htmlspecialchars($rows['announce_name']) ?>"
                  data-date="<?= date("F j, Y", strtotime($rows['announce_date'])) ?>"
                  data-text="<?= htmlspecialchars($rows['announce_text']) ?>"
                  data-image="<?= htmlspecialchars($announceImage) ?>">
                </button>
              </div>

              <div class="announcement-body">
                <h5><i class="fa-solid fa-bullhorn"></i> <?= htmlspecialchars($rows['announce_name']) ?></h5>
                <p><i class="fa-solid fa-calendar"></i> <?= date("F j, Y", strtotime($rows['announce_date'])) ?></p>
                <p><i class="fa-regular fa-note-sticky"></i> <?= nl2br(htmlspecialchars($rows['announce_text'])) ?></p>

                <div class="announcement-actions">
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
    </section>
  </div>

  <!-- Read More Modal (single instance, kept outside grid) -->
  <div class="modal fade" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="readMoreModalLabel">Announcement Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img id="modalImage" src="" alt="Announcement Image">
          <h3 id="modalTitle"></h3>
          <p><strong>Date:</strong> <span id="modalDate"></span></p>
          <div id="modalText" style="white-space: pre-wrap; text-align: left; margin-top: .5rem;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Verify Password Modal -->
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

  <!-- Add Announcement Modal (unchanged) -->
  <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background:#1A4314; color:white;">
          <h5 class="modal-title" id="addAnnouncementModalLabel"><i class="fa-solid fa-bullhorn"></i> New Announcement</h5>
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
              <input type="date" class="form-control" id="announce_date" name="announce_date">
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

  <!-- Image Preview Modal -->
  <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-transparent border-0 text-center">
        <img id="largePreview" src="" alt="Preview" class="img-fluid rounded shadow" style="max-height: 90vh;">
      </div>
    </div>
  </div>

  <!-- JS (Bootstrap bundle + sidebar toggle) -->
  <script src="/capstoneweb/assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
  <script src="/capstoneweb/assets/sidebarToggle.js"></script>

  <script>
    // ---------------------------
    // Read More (event delegation)
    // ---------------------------
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.read-more-btn');
      if (!btn) return;

      // populate modal
      const title = btn.dataset.title || '';
      const date = btn.dataset.date || '';
      const text = btn.dataset.text || '';
      const image = btn.dataset.image || '';

      const modalTitle = document.getElementById('modalTitle');
      const modalDate = document.getElementById('modalDate');
      const modalText = document.getElementById('modalText');
      const modalImage = document.getElementById('modalImage');

      if (modalTitle) modalTitle.textContent = title;
      if (modalDate) modalDate.textContent = date;
      if (modalText) modalText.textContent = text;
      if (modalImage && image) modalImage.src = image;

      // show modal
      const modalEl = document.getElementById('readMoreModal');
      if (modalEl) {
        const modal = new bootstrap.Modal(modalEl, {
          backdrop: true
        });
        modal.show();
      }
    });

    // ---------------------------
    // Edit button (event delegation)
    // ---------------------------
    document.addEventListener('click', function(e) {
      const editBtn = e.target.closest('.edit-btn');
      if (!editBtn) return;

      const id = editBtn.dataset.id || '';
      const title = editBtn.dataset.title || '';
      const text = editBtn.dataset.text || '';
      const img = editBtn.dataset.img || '';

      // fill edit modal
      const idField = document.getElementById('edit_announce_id');
      const titleField = document.getElementById('edit_announce_name');
      const textField = document.getElementById('edit_announce_text');
      const currentImage = document.getElementById('currentImage');
      const imgContainer = document.getElementById('currentImageContainer');

      if (idField) idField.value = id;
      if (titleField) titleField.value = title;
      if (textField) textField.value = text;

      if (img && img.trim() !== '') {
        if (currentImage) {
          currentImage.src = `/capstoneweb/announceImg/${img}`;
          if (imgContainer) imgContainer.style.display = 'block';
        }
      } else {
        if (imgContainer) imgContainer.style.display = 'none';
      }

      // reset file input if present
      const fileInput = document.getElementById('edit_announce_img');
      if (fileInput) fileInput.value = '';

      // show modal
      const editModalEl = document.getElementById('editAnnouncementModal');
      if (editModalEl) {
        const modal = new bootstrap.Modal(editModalEl, {});
        modal.show();
      }
    });

    // ---------------------------
    // Live preview when selecting new image in edit modal
    // ---------------------------
    (function() {
      const fileInput = document.getElementById('edit_announce_img');
      const imgContainer = document.getElementById('currentImageContainer');
      const currentImage = document.getElementById('currentImage');

      if (!fileInput) return;

      fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            if (currentImage) currentImage.src = e.target.result;
            if (imgContainer) imgContainer.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else {
          if (imgContainer) imgContainer.style.display = 'none';
        }
      });
    })();

    // ---------------------------
    // Click currentImage to enlarge
    // ---------------------------
    (function() {
      const currentImage = document.getElementById('currentImage');
      if (!currentImage) return;

      currentImage.addEventListener('click', function() {
        const largePreview = document.getElementById('largePreview');
        if (largePreview) {
          largePreview.src = this.src;
          const previewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
          previewModal.show();
        }
      });
    })();

    // ---------------------------
    // Auto-fill today for add modal date
    // ---------------------------
    document.addEventListener("DOMContentLoaded", function() {
      const today = new Date().toISOString().split("T")[0];
      const announceDate = document.getElementById("announce_date");
      if (announceDate) {
        announceDate.value = today;
        announceDate.setAttribute("min", today);
      }

      // Reset add form when modal closed
      const addModalEl = document.getElementById('addAnnouncementModal');
      if (addModalEl) {
        addModalEl.addEventListener('hidden.bs.modal', function() {
          const form = addModalEl.querySelector('form');
          if (form) form.reset();
        });
      }
    });
  </script>
</body>

</html>