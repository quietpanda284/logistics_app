<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../config/db_connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "UPDATE jobs SET status = 'Completed' WHERE job_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location:../jobs_report.php");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    header("Location:../jobs_report.php");
}
