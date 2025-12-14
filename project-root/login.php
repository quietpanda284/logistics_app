<?php
session_start();
include 'config/db_connect.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if ($password == $row['password']) {

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login - Logistics Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-card {
            border: 1px solid #333;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        #reglink {
            text-decoration: none;
            color: black;
        }

        #reglink:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="login-card">
        <h2 class="text-center mb-4">Logistics Co.</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control border-secondary mt-2" required>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password" class="form-control border-secondary mt-2" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
        <div class="mt-3 text-center"><a id="reglink" href="register.php">Register Account</a></div>
    </div>

</body>

</html>