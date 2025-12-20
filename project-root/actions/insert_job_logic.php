<?php
include '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $goods_name = mysqli_real_escape_string($conn, $_POST['goods_name']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $deadline   = mysqli_real_escape_string($conn, $_POST['deadline']);

    $quantity      = (int) $_POST['goods_quantity'];
    $weight        = (int) $_POST['weight'];
    $size          = (int) $_POST['size'];
    $start_site_id = (int) $_POST['start_site_id'];
    $end_site_id   = (int) $_POST['end_site_id'];
    $vehicle_id    = (int) $_POST['vehicle_id'];
    $hazardous     = isset($_POST['hazardous']) ? 1 : 0;

    $sql = "INSERT INTO jobs (goods_name, goods_quantity, weight, size, hazardous, start_date, deadline, start_site_id, end_site_id, status, assigned_vehicle_id) 
            VALUES ('$goods_name', '$quantity', '$weight', '$size', '$hazardous', '$start_date', '$deadline', '$start_site_id', '$end_site_id', 'Outstanding', $vehicle_id)";

    if (mysqli_query($conn, $sql)) {
        // 1. Get the ID of the new job
        $new_job_id = mysqli_insert_id($conn);
        
        // 2. Redirect back to enter_job.php with success flag and ID
        header("Location: ../enter_job.php?status=success&job_id=" . $new_job_id);
        exit();
    } else {
        echo "Error: " . mysqli_error($conn); 
    }

    mysqli_close($conn);
}
?>