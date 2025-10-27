// ===== Sidebar Toggle =====
const toggleButton = document.getElementById("toggleSidebar");
const sidebar = document.getElementById("sidebar");
const content = document.getElementById("content");

// Handle sidebar toggle for desktop and mobile
toggleButton.addEventListener("click", function () {
  if (window.innerWidth <= 768) {
    // Mobile: slide sidebar in/out
    document.body.classList.toggle("sidebar-open");
  } else {
    // Desktop: collapse sidebar width
    document.body.classList.toggle("sidebar-collapsed");
  }
});

// Close sidebar when clicking outside (mobile only)
document.addEventListener("click", (e) => {
  if (
    window.innerWidth <= 768 &&
    document.body.classList.contains("sidebar-open") &&
    !sidebar.contains(e.target) &&
    !toggleButton.contains(e.target)
  ) {
    document.body.classList.remove("sidebar-open");
  }
});

// Optional: handle window resize (reset states when resizing)
window.addEventListener("resize", () => {
  if (window.innerWidth > 768) {
    // Ensure mobile-open state doesn’t linger
    document.body.classList.remove("sidebar-open");
  }
});
