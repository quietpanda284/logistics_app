<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $reg = $_POST['reg_plate'];
    $type = (int)$_POST['type_id'];
    $site = (int)$_POST['site_id'];

    $sql = "INSERT INTO vehicles (registration_plate, type_id, site_id) VALUES (?, ?, ?)";

    $stmt = mysqli_stmt_init($conn);
    
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "sii", $reg, $type, $site);


        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../manage_vehicles.php");
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