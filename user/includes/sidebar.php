<?php
$query = "SELECT * FROM account WHERE userid = '$userid'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$userImage = !empty($user['userimg']) ? "../image/" . $user['userimg'] : "../image/placeholder.jpg";

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

  <div class="profile">
    <img src="<?= htmlspecialchars($userImage) ?>" alt="User">
    <div class="profile-info">
      <h3><?= htmlspecialchars($user['userName']) ?></h3>
      <p ><?= htmlspecialchars($user['email']) ?></p>
      <p> Purok <?= htmlspecialchars($user['purok']) ?></p>
    </div>
  </div>

  <div class="sidenav">
    <a href="/capstoneweb/admin/pages/dashboard.php?userid=<?= $userid ?>" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>"><i class="fas fa-home" style="font-size: 20px;"></i> <span class="link-text">Dashboard</span></a>
    <a href="/capstoneweb/admin/pages/announcement.php?userid=<?= $userid ?>" class="<?= ($current_page == 'announcement.php') ? 'active' : '' ?>"><i class="fas fa-bell"></i> <span class="link-text"> Announcement</span></a>
    <a href="/capstoneweb/admin/pages/record.php?userid=<?= $userid ?>" class="<?= ($current_page == 'record.php') ? 'active' : '' ?>"><i class="fas fa-clipboard-list"></i><span class="link-text"> Records</span></a>
    <a href="/capstoneweb/admin/pages/recyclables.php?userid=<?= $userid ?>" class="<?= ($current_page == 'recyclables.php') ? 'active' : '' ?>"><i class="fas fa-recycle"></i><span class="link-text"> Recyclables</span></a>
    <a href="/capstoneweb/admin/pages/reward.php?userid=<?= $userid ?>" class="<?= ($current_page == 'reward.php') ? 'active' : '' ?>"><i class="fa-solid fa-award"></i><span class="link-text"> Reward</span></a>
    <a href="/capstoneweb/admin/pages/notification.php?userid=<?= $userid ?>" class="<?= ($current_page == 'notification.php') ? 'active' : '' ?>"><i class="fas fa-exclamation-circle"></i><span class="link-text"> Notification</span></a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#verifyPasswordModal" class="<?= ($current_page == 'accsetting.php') ? 'active' : '' ?>">
      <i class="fa-solid fa-gears"></i> <span class="link-text"> Account Settings </span>
    </a>
    <a href="/capstoneweb/admin/function/logout.php"><i class="fas fa-door-open"></i><span class="link-text"> Logout </span></a>
  </div>

  <script>
    const toggleBtn = document.getElementById("toggleSidebar");

    if (toggleBtn) {
      toggleBtn.addEventListener("click", function () {
        if (window.innerWidth <= 768) {
          // On mobile, toggle sidebar
          document.body.classList.toggle("sidebar-collapsed");
        }
      });
    }

    // Optional: Close sidebar when clicking overlay (for mobile)
    const overlay = document.querySelector(".overlay");
    if (overlay) {
      overlay.addEventListener("click", function () {
        document.body.classList.remove("sidebar-collapsed");
      });
    }
  </script>


<!-- Bootstrap 5 JS (at the bottom, before </body>) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</div>
</div>