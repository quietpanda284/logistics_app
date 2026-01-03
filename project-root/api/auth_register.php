<?php
session_start();
include '../config/db_connect.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid Request'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $secret_code = $_POST['secret_code'];
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit();
    }

    if ($secret_code !== "LOGISTICS_SECURE_2026") {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Company Access Code!']);
        exit();
    }

    $stmt = mysqli_stmt_init($conn);
    $check_sql = "SELECT username FROM users WHERE username = ?";

    if (mysqli_stmt_prepare($stmt, $check_sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit();
    }

    $role = 'staff';

    $sql = "INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed_password, $full_name, $role);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Account created successfully!']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error (Insert).']);
        exit();
    }
}
