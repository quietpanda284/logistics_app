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
    $hazardous = isset($_POST['hazardous']) ? 1 : 0;

    $sql = "INSERT INTO jobs (goods_name, goods_quantity, weight, size, hazardous, start_date, deadline, start_site_id, end_site_id, status) 
            VALUES ('$goods_name', '$quantity', '$weight', '$size', '$hazardous', '$start_date', '$deadline', '$start_site_id', '$end_site_id', 'Outstanding')";

    if (mysqli_query($conn, $sql)) {
        echo "<h1>Success!</h1><p>Redirecting...</p>";
        header("refresh:1;url=../index.php");
    } else {
        //UPDATE SECURITY
        echo "Error: " . mysqli_error($conn); 
    }

    mysqli_close($conn);
}
?>