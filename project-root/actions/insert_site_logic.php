<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $site_name = $_POST['site_name'];
    $address = $_POST['address'];

    $sql = "INSERT INTO sites (site_name, address) VALUES (?, ?)";

    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        
        mysqli_stmt_bind_param($stmt, "ss", $site_name, $address);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: manage_sites.php");
            exit();
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);

    } else {
        echo "SQL Error: " . mysqli_error($conn);
    }
}
?>