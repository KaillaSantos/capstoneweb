<?php
 require_once __DIR__ . '/../../conn/dbconn.php';


    session_start();
    unset($_SESSION['userName']);
    session_destroy();
    header("location:/capstoneweb/pages/LandingPage.php");
    exit();
?>