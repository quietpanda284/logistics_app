<?php
include '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['site_id'];
    $name = $_POST['site_name'];
    $address = $_POST['address'];

    $sql = "UPDATE sites SET site_name='$name', address='$address' WHERE site_id=$id";

    if (mysqli_query($conn, $sql)) {
        echo "Updated successfully. Redirecting...";
        header("refresh:2;url=../manage_sites.php"); 
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>