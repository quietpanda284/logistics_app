<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    die("ACCESS DENIED: Only Administrators can delete jobs.");
}

include '../config/db_connect.php';

if (isset($_GET['id'])) {
    
    $id = (int)$_GET['id'];

    $sql = "DELETE FROM jobs WHERE job_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../jobs_report.php"); 
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

} else {
    header("Location: ../jobs_report.php");
}
?>