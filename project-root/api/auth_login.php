<?php

session_start();
include '../config/db_connect.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid Request'];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $input_password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = ?";
    
    $stmt = mysqli_stmt_init($conn);
    if(mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $stored_password = $row['password'];

            if (password_verify($input_password, $stored_password)) {

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                
                echo json_encode(['status' => 'success']);
                exit();
            }
            // MIGRATION CHECK: Is it an old Plain Text password?
            elseif ($stored_password == $input_password) {
                // It matches! Log them in, but upgrade them to a hash first.
                
                $new_hash = password_hash($input_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
                
                if ($up_stmt = mysqli_prepare($conn, $update_sql)) {
                    mysqli_stmt_bind_param($up_stmt, "si", $new_hash, $row['user_id']);
                    mysqli_stmt_execute($up_stmt);
                }

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
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
?>