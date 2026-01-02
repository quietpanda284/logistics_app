<?php
include 'config/db_connect.php';

session_start();

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


$sites = [];
$sql = "SELECT * FROM sites";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $sites[] = $row;
}

$vehicles = [];

$sql_vehicles = "SELECT v.* FROM vehicles v
                 WHERE v.vehicle_id NOT IN (
                     SELECT j.assigned_vehicle_id 
                     FROM jobs j 
                     WHERE j.status IN ('Outstanding', 'In Progress') 
                     AND j.assigned_vehicle_id IS NOT NULL
                 )";

$result_vehicles = mysqli_query($conn, $sql_vehicles);
while ($row = mysqli_fetch_assoc($result_vehicles)) {
    $vehicles[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics Co. Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container text-center mt-5">
        <h3 class="pb-4">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h3>

        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card text-center p-4">
                    <h3>Enter New Job</h3>
                    <button type="button" class="btn btn-dark mt-2" data-bs-toggle="modal" data-bs-target="#createJobModal">
                        Go
                    </button>
                    <!-- <a class="btn btn-primary mt-2" style="text-decoration: none; color: white;" href="enter_job.php">Go</a> -->
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center p-4">
                    <h3>Manage Sites</h3>
                    <a class="btn btn-dark mt-2" style="text-decoration: none; color: white;" href="manage_sites.php">Go</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center p-4">
                    <h3>Search Jobs</h3>
                    <a class="btn btn-dark mt-2" style="text-decoration: none; color: white;" href="jobs_report.php">Go</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createJobModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white border-secondary">

                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Create New Job</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="actions/insert_job_logic.php" method="POST">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Goods Name</label>
                                <input type="text" name="goods_name" class="form-control bg-secondary text-white border-0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Goods Quantity</label>
                                <input type="number" name="goods_quantity" class="form-control bg-secondary text-white border-0" required>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" name="weight" class="form-control bg-secondary text-white border-0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Size (m³)</label>
                                <input type="number" name="size" class="form-control bg-secondary text-white border-0" required>
                            </div>
                        </div>

                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Assign Vehicle</label>
                                <select name="vehicle_id" class="form-select bg-secondary text-white border-0" required>
                                    <option value="" selected disabled>Select Vehicle...</option>
                                    <?php
                                    foreach ($vehicles as $vehicle) {
                                        echo "<option value='" . $vehicle['vehicle_id'] . "'>" . $vehicle['registration_plate'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 pt-4">
                                <div class="form-check">
                                    <input type="checkbox" name="hazardous" class="form-check-input" id="hazCheck">
                                    <label class="form-check-label text-warning" for="hazCheck">Hazardous Cargo</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Location</label>
                                <select name="start_site_id" class="form-select bg-secondary text-white border-0" required>
                                    <option value="" selected disabled>Select Origin Site...</option>
                                    <?php
                                    foreach ($sites as $site) {
                                        echo "<option value='" . $site['site_id'] . "'>" . $site['site_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Location</label>
                                <select name="end_site_id" class="form-select bg-secondary text-white border-0" required>
                                    <option value="" selected disabled>Select Destination...</option>
                                    <?php
                                    foreach ($sites as $site) {
                                        echo "<option value='" . $site['site_id'] . "'>" . $site['site_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control bg-secondary text-white border-0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Deadline</label>
                                <input type="date" name="deadline" class="form-control bg-secondary text-white border-0" required>
                            </div>
                        </div>

                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit Job</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-success">
                        <i class="bi bi-check-circle-fill"></i> Job Created
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <h3 class="fw-bold">Success!</h3>
                    <p class="mb-0">Job <span class="text-info fw-bold">JN<?php echo isset($_GET['job_id']) ? sprintf("%03d", $_GET['job_id']) : '000'; ?></span> has been added.</p>
                </div>
                <div class="modal-footer border-secondary">
                    <a href="jobs_report.php" class="btn btn-outline-light">View Jobs</a>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Create Another</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <script>
            // Wait for the page to fully load
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();
                
                // Clean the URL so a refresh doesn't show the modal again
                const url = new URL(window.location);
                url.searchParams.delete('status');
                url.searchParams.delete('job_id');
                window.history.replaceState({}, '', url);
            });
            </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>