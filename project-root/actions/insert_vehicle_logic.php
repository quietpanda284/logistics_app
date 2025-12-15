<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg = mysqli_real_escape_string($conn, $_POST['reg_plate']);
    $type = (int)$_POST['type_id'];
    $site = (int)$_POST['site_id'];

    $sql = "INSERT INTO vehicles (registration_plate, type_id, site_id) VALUES ('$reg', '$type', '$site')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../manage_vehicles.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>