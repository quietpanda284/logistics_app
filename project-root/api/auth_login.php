<?php

session_start();
include '../config/db_connect.php';

header('Content-Type: application/json');

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = ?";
    
    $stmt = mysqli_stmt_init($conn);
    if(mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if($row['password'] == $_POST['password']) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                
                echo json_encode(['status' => 'success']);
                exit();
            }
            else {
                echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
                exit();
            }
        }
        else {
            echo json_encode(['status' => 'error', 'message' => 'Username is not registered.']);
            exit();
        }
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit();
    }
 }