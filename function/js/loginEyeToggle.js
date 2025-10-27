// Password toggle functionality
const togglePassword = (fieldId, iconId) => {
  const field = document.getElementById(fieldId);
  const icon = document.getElementById(iconId);
  const type = field.getAttribute("type") === "password" ? "text" : "password";
  field.setAttribute("type", type);
  icon.classList.toggle("bi-eye-slash");
  icon.classList.toggle("bi-eye");
};

document.getElementById("togglePassword").addEventListener("click", () => {
  togglePassword("passWord", "togglePassword");
});

document.getElementById("toggleRePassword").addEventListener("click", () => {
  togglePassword("rePassword", "toggleRePassword");
});

// Real-time password validation
document.addEventListener("DOMContentLoaded", () => {
  const passwordField = document.getElementById("passWord");
  const rePasswordField = document.getElementById("rePassword");
  const form = document.querySelector("form");

  // Real-time matching indicator
  rePasswordField.addEventListener("input", () => {
    if (passwordField.value !== rePasswordField.value) {
      rePasswordField.setCustomValidity("Passwords do not match");
    } else {
      rePasswordField.setCustomValidity("");
    }
  });

  // Form submission validation
  form.addEventListener("submit", (e) => {
    if (passwordField.value !== rePasswordField.value) {
      e.preventDefault();
      alert("Passwords do not match");
      rePasswordField.focus();
    }
  });
});
