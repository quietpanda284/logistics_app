<?php
session_start();
include 'config/db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $secret_code = $_POST['secret_code'];

    if ($secret_code !== "LOGISTICS_SECURE_2025") {
        $error = "Invalid Company Access Code! Access Denied.";
    } else {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password']; // Plain text for now (Task C report note: mention hashing here)
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

        $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username already exists.";
        } else {
            $sql = "INSERT INTO users (username, password, full_name, role) VALUES ('$username', '$password', '$full_name', 'staff')";

            if (mysqli_query($conn, $sql)) {
                $success = "Account created!";
            } else {
                $error = "Database error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Staff Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container vh-100 d-flex justify-content-center align-items-center">

        <div class="col-12 col-md-5 col-lg-4">

            <div class="card border-secondary shadow-lg">
                <div class="card-body p-4">
                    <h3 class="text-center mb-3">Staff Register</h3>

                    <?php if ($error): ?>
                        <div class="alert alert-danger p-2 text-center"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success p-2 text-center">
                            <?php echo $success; ?>
                            <br>
                            <a href="login.php" class="alert-link">Go to Login</a>
                        </div>
                    <?php endif; ?>

                    <?php if (!$success): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <hr class="border-secondary my-4">

                            <div class="mb-4">
                                <label class="form-label text-warning fw-bold">Company Access Code</label>
                                <input type="password" name="secret_code" class="form-control border-warning" placeholder="Required" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Create Account</button>
                                <a href="login.php" class="btn btn-outline-dark">Back to Login</a>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

</body>

</html>