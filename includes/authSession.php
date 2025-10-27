<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once __DIR__ . '/../conn/dbconn.php';

// Check login session
if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Unauthorized access. Please login.');
          window.location.href='../pages/login.php';</script>";
    exit();
}

// Store user ID for page use
$userid = $_SESSION['userid'];
?>