<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../manage_sites.php?error=Access Denied");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['site_id'];
    $name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "UPDATE sites SET 
            site_name = '$name',
            address = '$address'
            WHERE site_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../manage_sites.php?success=updated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>