<?php
include '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $goods_name = $_POST['goods_name'];
    $quantity = $_POST['goods_quantity'];
    $weight = $_POST['weight'];
    $size = $_POST['size'];
    $start_date = $_POST['start_date'];
    $deadline = $_POST['deadline'];

    $start_site_id = $_POST['start_site_id'];
    $end_site_id = $_POST['end_site_id'];

    $hazardous = isset($_POST['hazardous']) ? 1 : 0;

    $sql = "INSERT INTO jobs (goods_name, goods_quantity, weight, size, hazardous, start_date, deadline, start_site_id, end_site_id, status) 
            VALUES ('$goods_name', '$quantity', '$weight', '$size', '$hazardous', '$start_date', '$deadline', '$start_site_id', '$end_site_id', 'Outstanding')";

    if (mysqli_query($conn, $sql)) {
        echo "<h1>Success!</h1><p>Redirecting...</p>";
    header("refresh:1;url=../index.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>