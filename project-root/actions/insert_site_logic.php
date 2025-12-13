<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $site_name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "INSERT INTO sites (site_name, address) VALUES ('$site_name', '$address')";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_sites.php"); 
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>