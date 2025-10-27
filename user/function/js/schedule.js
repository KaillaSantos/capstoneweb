function formatDateStr(isoDate) {
  if (!isoDate) return "Set";
  const d = new Date(isoDate + "T00:00:00");
  if (isNaN(d)) return "Set";
  const options = {
    year: "numeric",
    month: "short",
    day: "numeric",
  };
  return d.toLocaleDateString(undefined, options);
}

function updateNotificationBadge(count) {
  const badge = document.getElementById("notifBadge");
  badge.textContent = count > 99 ? "99+" : count;
  badge.style.display = count > 0 ? "inline-block" : "none";
}

function updateScheduleBadge(isoDate) {
  const badge = document.getElementById("scheduleBadge");
  if (!badge) return;
  badge.textContent = isoDate ? formatDateStr(isoDate) : "Set";
  if (isoDate) badge.dataset.iso = isoDate;
  else delete badge.dataset.iso;
}

/* Open native date picker and save choice */
async function openSchedulePicker() {
  const dateInput = document.getElementById("hiddenDateInput");
  if (!dateInput) {
    console.error("Date input not found");
    return;
  }

  // If schedule badge has an ISO date stored in data attribute, prefill it.
  const badge = document.getElementById("scheduleBadge");
  // If you've stored the ISO date somewhere (server-side), you can set it as data-date attribute:
  // <span id="scheduleBadge" data-iso="2025-10-12">Oct 12, 2025</span>
  const isoFromBadge =
    badge && badge.dataset && badge.dataset.iso ? badge.dataset.iso : null;

  // If badge text is 'Set' but we fetched date earlier via JS, store it on the badge
  // (we'll also try to use current displayed text if it's ISO-like)
  if (!isoFromBadge && badge) {
    // quick attempt: if badge.textContent looks like YYYY-MM-DD, use it
    const possible = badge.textContent.trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(possible)) {
      dateInput.value = possible;
    }
  } else if (isoFromBadge) {
    dateInput.value = isoFromBadge;
  } else {
    // optional: try to prefill from JS variable (if you held scheduleDate variable)
    dateInput.value = "";
  }

  // Setup one-time change handler
  const onChange = async () => {
    const chosen = dateInput.value; // YYYY-MM-DD or ''
    if (!chosen) {
      dateInput.removeEventListener("change", onChange);
      return;
    }

    // update badge UI
    updateScheduleBadge(chosen);

    // keep ISO on badge for next prefill
    if (badge) badge.dataset.iso = chosen;

    // Save to server
    try {
      const res = await fetch("/capstoneweb/api/save_schedule.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          userId: currentUserId,
          date: chosen,
        }),
      });
      const json = await res.json();
      if (!json.success) {
        console.warn("Server error saving schedule", json);
        alert("Failed to save schedule. Please try again.");
      }
    } catch (err) {
      console.error("Save schedule failed:", err);
      alert("Network error saving schedule.");
    }

    dateInput.removeEventListener("change", onChange);
  };

  dateInput.addEventListener("change", onChange);

  // If browser supports showPicker() (Chromium), prefer that
  if (typeof dateInput.showPicker === "function") {
    try {
      dateInput.showPicker();
      return;
    } catch (err) {
      // ignore and fallback to click()
      console.debug("showPicker failed, falling back to click()", err);
    }
  }

  // Otherwise, using click() should open the picker — for Firefox ensure input is not display:none.
  try {
    dateInput.focus();
    dateInput.click();
  } catch (err) {
    console.error("Opening date picker failed", err);
    // final fallback: open a simple prompt (very degraded fallback)
    const manual = prompt("Enter schedule date (YYYY-MM-DD):");
    if (manual && /^\d{4}-\d{2}-\d{2}$/.test(manual)) {
      dateInput.value = manual;
      // manually call onChange handler actions:
      updateScheduleBadge(manual);
      if (badge) badge.dataset.iso = manual;
      // Save to server
      try {
        await fetch("/capstoneweb/api/save_schedule.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            userId: currentUserId,
            date: manual,
          }),
        });
      } catch (e) {
        console.error(e);
      }
    } else {
      alert("Invalid date entered.");
    }
    dateInput.removeEventListener("change", onChange);
  }
}

/* Fetch routines using current user id */
async function fetchNotificationCount() {
  try {
    const resp = await fetch(
      "/capstoneweb/api/notif_count.php?user=" + currentUserId,
      {
        cache: "no-store",
      }
    );
    const data = await resp.json();
    updateNotificationBadge(Number(data.count || 0));
  } catch (err) {
    console.error("Failed to fetch notifications:", err);
    updateNotificationBadge(0);
  }
}

async function fetchCurrentScheduleDate() {
  try {
    const resp = await fetch(
      "/capstoneweb/api/get_schedule.php?user=" + currentUserId,
      {
        cache: "no-store",
      }
    );
    const data = await resp.json();
    updateScheduleBadge(data.date || null);
  } catch (err) {
    console.error("Failed to fetch schedule:", err);
    updateScheduleBadge(null);
  }
}

/* Init */
document.addEventListener("DOMContentLoaded", () => {
  fetchNotificationCount();
  fetchCurrentScheduleDate();
  setInterval(fetchNotificationCount, 30000);
});

// Ensure Bootstrap Modal is available
const scheduleModalEl = document.getElementById("scheduleModal");
const scheduleModal = scheduleModalEl
  ? new bootstrap.Modal(scheduleModalEl)
  : null;
const modalDateInput = document.getElementById("modalDateInput");
const saveScheduleBtn = document.getElementById("saveScheduleBtn");
const clearScheduleBtn = document.getElementById("clearScheduleBtn");
const modalPreview = document.getElementById("modalPreview");

function showScheduleModal() {
  // Prefill current schedule from badge/data attribute if available
  const badge = document.getElementById("scheduleBadge");
  const iso =
    badge && badge.dataset && badge.dataset.iso ? badge.dataset.iso : "";
  if (modalDateInput) modalDateInput.value = iso || "";

  // update preview
  updateModalPreview();

  if (scheduleModal) scheduleModal.show();
}

// update the textual preview next to input
function updateModalPreview() {
  if (!modalPreview || !modalDateInput) return;
  const val = modalDateInput.value;
  modalPreview.textContent = val ? formatDateStr(val) : "No date selected";
}

// clear button: remove chosen value (and optionally delete on server)
clearScheduleBtn &&
  clearScheduleBtn.addEventListener("click", async () => {
    if (!modalDateInput) return;
    // Clear UI immediately
    modalDateInput.value = "";
    updateModalPreview();

    try {
      const res = await fetch("/capstoneweb/api/save_schedule.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        // send explicit null so server knows we want to remove the schedule
        body: JSON.stringify({
          userId: currentUserId,
          date: null,
        }),
      });
      const json = await res.json();

      if (json.success) {
        // update UI badge & data
        updateScheduleBadge(null);
        const badge = document.getElementById("scheduleBadge");
        if (badge) delete badge.dataset.iso;
        // close modal
        scheduleModal && scheduleModal.hide();
      } else {
        console.warn("Clear schedule failed:", json);
        alert("Failed to clear schedule: " + (json.error || "Unknown error"));
      }
    } catch (err) {
      console.error("Network/JS error clearing schedule:", err);
      alert("Network error while clearing schedule.");
    }
  });

// live preview when input changes
modalDateInput && modalDateInput.addEventListener("change", updateModalPreview);

// Save button: persist to server
saveScheduleBtn &&
  saveScheduleBtn.addEventListener("click", async (ev) => {
    ev.preventDefault();
    if (!modalDateInput) return;

    const chosen = modalDateInput.value; // '' or YYYY-MM-DD
    if (!chosen) {
      if (!confirm("No date selected. Do you want to remove the schedule?"))
        return;
    }

    try {
      const res = await fetch("/capstoneweb/api/save_schedule.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          userId: currentUserId,
          date: chosen || null,
        }),
      });
      const json = await res.json();
      if (json.success) {
        // update UI badge
        updateScheduleBadge(chosen || null);
        // store ISO on badge for prefill next time
        const badge = document.getElementById("scheduleBadge");
        if (badge) {
          if (chosen) badge.dataset.iso = chosen;
          else delete badge.dataset.iso;
        }
        scheduleModal && scheduleModal.hide();
      } else {
        console.warn("Server failed to save schedule", json);
        alert("Failed to save schedule. Try again.");
      }
    } catch (err) {
      console.error("Save schedule error", err);
      alert("Network error while saving schedule.");
    }
  });

// Optional: automatically open modal on schedule button click (if you didn't change onclick)
document.getElementById("scheduleBtn")?.addEventListener("click", (e) => {
  e.preventDefault();
  showScheduleModal();
});
