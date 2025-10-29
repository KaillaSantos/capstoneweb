<?php
require_once __DIR__ . "../conn/dbconn.php";

if (isset($_POST['archive_selected'])) {
  if (!empty($_POST['archive_ids'])) {
    $archive_ids = $_POST['archive_ids'];
    $id_list = implode(",", array_map('intval', $archive_ids));

    $archive_query = "UPDATE announcement SET status = 'Archived' WHERE announce_id IN ($id_list)";
    if (mysqli_query($conn, $archive_query)) {
      echo "<script>alert('Selected announcements archived successfully.');</script>";
      $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
      header("Location: ../admin/pages/announcement.php?userid={$userid}");
      exit();
    } else {
      echo "<script>alert('Failed to archive announcements.');</script>";
      $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
      header("Location: ../admin/pages/announcement.php?userid={$userid}");
      exit();
    }
  } else {
    echo "<script>alert('No announcements selected.');</script>";
    $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
    header("Location: ..admin/pages/announcement.php?userid={$userid}");
    exit();
  }
}
?>