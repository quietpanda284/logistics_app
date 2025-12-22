<?php

session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $goods_name = $_POST['goods_name'];
    $start_date = $_POST['start_date'];
    $deadline   = $_POST['deadline'];

    $quantity      = (int) $_POST['goods_quantity'];
    $weight        = (int) $_POST['weight'];
    $size          = (int) $_POST['size'];
    $start_site_id = (int) $_POST['start_site_id'];
    $end_site_id   = (int) $_POST['end_site_id'];
    $vehicle_id    = (int) $_POST['vehicle_id'];
    $hazardous     = isset($_POST['hazardous']) ? 1 : 0;

    $sql = "INSERT INTO jobs (goods_name, goods_quantity, weight, size, hazardous, start_date, deadline, start_site_id, end_site_id, status, assigned_vehicle_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Outstanding', ?)";

    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {

        mysqli_stmt_bind_param($stmt, "siiisssiii", $goods_name, $quantity, $weight, $size, $hazardous, $start_date, $deadline, $start_site_id, $end_site_id, $vehicle_id);

        if (mysqli_stmt_execute($stmt)) {
            $new_job_id = mysqli_insert_id($conn);
            header("Location: ../enter_job.php?status=success&job_id=" . $new_job_id);
            exit();
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>