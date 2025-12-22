<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../manage_sites.php?error=Access Denied");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['site_id'])) {
    $site_id = (int)$_POST['site_id'];

    $sql = "DELETE FROM sites WHERE site_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $site_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../manage_sites.php?msg=Site Deleted");
    } else {
        // This error flags if the site is being used by a vehicle or job
        $error = "Cannot delete site. It may still have vehicles or jobs assigned.";
        header("Location: ../manage_sites.php?error=" . urlencode($error));
    }
    
    mysqli_stmt_close($stmt);
} else {
    header("Location: ../manage_sites.php");
}
?>