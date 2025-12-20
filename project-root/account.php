<?php
session_start();
include 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from database
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, full_name, role FROM users WHERE user_id = ?";
$stmt = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
} else {
    echo "Database error";
    exit();
}

// Set default avatar color based on role
$avatarColor = ($user['role'] === 'admin') ? 'bg-danger' : 'bg-primary';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Logistics Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(to right, #212529, #343a40);
            border-bottom: 1px solid #495057;
        }
        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: white;
        }
    </style>
</head>

<body class="bg-light">

    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card shadow border-0">
                    <div class="card-header profile-header text-white pt-4 pb-4 text-center">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="avatar-circle <?php echo $avatarColor; ?>">
                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                            </div>
                        </div>
                        <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <span class="badge bg-secondary border border-light"><?php echo ucfirst($user['role']); ?></span>
                    </div>

                    <div class="card-body p-4 bg-dark text-white">
                        <h5 class="mb-4 text-muted border-bottom border-secondary pb-2">Account Details</h5>
                        
                        <div class="row mb-3">
                            <div class="col-sm-4 text-secondary">Full Name</div>
                            <div class="col-sm-8 fw-bold"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 text-secondary">Username</div>
                            <div class="col-sm-8"><?php echo htmlspecialchars($user['username']); ?></div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-4 text-secondary">Role Access</div>
                            <div class="col-sm-8">
                                <?php if($user['role'] == 'admin'): ?>
                                    <span class="text-danger">Full Administrative Access</span>
                                <?php else: ?>
                                    <span class="text-info">Standard Staff Access</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="actions/logout.php" class="btn btn-outline-danger ">
                                Log Out
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>