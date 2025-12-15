<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $sql = "DELETE FROM vehicles WHERE vehicle_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../manage_vehicles.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: ../manage_vehicles.php");
}
?>