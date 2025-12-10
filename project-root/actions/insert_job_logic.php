<?php
include '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Capture the New Inputs
    $goods_name = $_POST['goods_name'];       // Renamed from description
    $quantity = $_POST['goods_quantity'];     // NEW
    $weight = $_POST['weight'];
    $size = $_POST['size'];
    $start_date = $_POST['start_date'];
    $deadline = $_POST['deadline'];
    
    // Capture the Site IDs from the dropdowns
    $start_site_id = $_POST['start_site_id']; // NEW
    $end_site_id = $_POST['end_site_id'];     // NEW

    // Checkbox Logic
    $hazardous = isset($_POST['hazardous']) ? 1 : 0;

    // 2. The Updated SQL Command
    // We default the status to 'Outstanding'
    $sql = "INSERT INTO jobs (goods_name, goods_quantity, weight, size, hazardous, start_date, deadline, start_site_id, end_site_id, status) 
            VALUES ('$goods_name', '$quantity', '$weight', '$size', '$hazardous', '$start_date', '$deadline', '$start_site_id', '$end_site_id', 'Outstanding')";

    if (mysqli_query($conn, $sql)) {
        echo "<h1>Success!</h1><p>Redirecting...</p>";
        header("refresh:2;url=index.php"); 
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>