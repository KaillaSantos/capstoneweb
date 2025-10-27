<?php
 
$conn = mysqli_connect("localhost", "root", "", "capstone");

if(!$conn) {
    header("Connection Failed.").mysqli_connect_error();
}

?>