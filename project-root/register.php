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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container min-vh-100 d-flex justify-content-center align-items-center py-4">

        <div class="col-12 col-md-6 col-lg-4">

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
                                <input type="text" name="full_name" class="form-control border-secondary" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control border-secondary" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control border-secondary" required>
                            </div>

                            <hr class="border-secondary my-4">

                            <div class="mb-4">
                                <label class="form-label text-warning fw-bold">Company Access Code</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-warning border-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5z"/>
                                        </svg>
                                    </span>
                                    <input type="password" name="secret_code" class="form-control border-warning" placeholder="Required" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-dark">Create Account</button>
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