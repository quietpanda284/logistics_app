<?php
session_start();
include '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_id']) && isset($_POST['status'])) {
    $job_id = $_POST['job_id'];
    $status = $_POST['status'];

    $sql = "UPDATE jobs SET status = ? WHERE job_id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $status, $job_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: ../jobs_report.php");
exit();
?>