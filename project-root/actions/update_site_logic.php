<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['site_id'];
    $name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $small = (int)$_POST['target_small'];
    $med   = (int)$_POST['target_medium'];
    $hgv   = (int)$_POST['target_hgv'];

    $sql = "UPDATE sites SET 
            site_name = '$name',
            address = '$address',
            target_small_van = '$small',
            target_medium_van = '$med',
            target_hgv = '$hgv'
            WHERE site_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../manage_sites.php?success=updated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>