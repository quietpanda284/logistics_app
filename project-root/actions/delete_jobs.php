<?php
// 1. Handshake
include '../config/db_connect.php';

// 2. Check if ID exists in the URL
if (isset($_GET['id'])) {
    
    $id = $_GET['id'];

    // 3. The SQL Command
    $sql = "DELETE FROM jobs WHERE job_id = $id";

    // 4. Run it
    if (mysqli_query($conn, $sql)) {
        // Success: Go back to the list instantly
        header("Location: ../search_jobs.php"); 
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

} else {
    // If someone tries to open this file without an ID, kick them out
    header("Location: ../search_jobs.php");
}
?>