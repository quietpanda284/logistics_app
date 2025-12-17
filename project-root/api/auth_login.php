<?php
session_start();
include '../config/db_connect.php';

header('Content-Type: application/json');

// Default response
$response = ['status' => 'error', 'message' => 'Invalid Request'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        if ($password === $row['password']) {
            
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
    
            echo json_encode(['status' => 'success']);
            exit();
    
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Wrong Password']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit();
    }
}
?>