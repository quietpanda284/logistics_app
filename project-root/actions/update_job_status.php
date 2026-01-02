<?php
session_start();
include '../config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_id']) && isset($_POST['status'])) {
    $job_id = $_POST['job_id'];
    $status = $_POST['status'];

    $sql = "UPDATE jobs SET status = ? WHERE job_id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $status, $job_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Success! Send JSON back
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Query preparation failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
exit();
?>