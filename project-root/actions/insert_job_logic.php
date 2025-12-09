<?php
include '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $weight = $_POST['weight'];
    $size = $_POST['size'];
    $start_date = $_POST['start_date'];
    $deadline = $_POST['deadline'];
    
    if(isset($_POST['hazardous'])) {
        $hazardous = 1;
    } else {
        $hazardous = 0;
    }

    $sql = "INSERT INTO jobs (description, weight, size, hazardous, start_date, deadline) 
            VALUES ('$description', '$weight', '$size', '$hazardous', '$start_date', '$deadline')";

    if (mysqli_query($conn, $sql)) {
        echo "<div style='padding: 20px; background-color: #d4edda; color: #155724; text-align: center;'>";
        echo "<h1>Success!</h1>";
        echo "<p>New job added to the database.</p>";
        echo "<p>Redirecting you back to the dashboard...</p>";
        echo "</div>";

        header("refresh:3;url=index.php"); 
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>