<?php
include 'config/db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Fetch User Details
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

// 2. NEW: Fetch Job Statistics for the Dashboard
$stats = [
    'Outstanding' => 0,
    'In Progress' => 0,
    'Completed'   => 0,
    'Cancelled'   => 0
];

$sql_stats = "SELECT status, COUNT(*) as count FROM jobs 
              WHERE status IN ('Outstanding', 'In Progress') 
                 OR start_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
              GROUP BY status";
$result_stats = mysqli_query($conn, $sql_stats);
while ($row = mysqli_fetch_assoc($result_stats)) {
    $stats[$row['status']] = $row['count'];
}

// 3. Fetch Sites (For Modal)
$sites = [];
$sql = "SELECT * FROM sites";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $sites[] = $row;
}

// 4. Fetch Available Vehicles (For Modal)
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Interactive Hover Effect for Cards */
        .action-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3) !important;
            border-color: #0d6efd !important;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-5 mb-5">
        <div class="text-center mb-5">
            <h3 class="fw-bold">Welcome, <span class="text-primary"><?php echo htmlspecialchars($user['full_name']); ?></span></h3>
            <p class="text-muted">Here is your fleet overview for <strong>the last 30 days</strong>.</p>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <div class="card bg-dark text-white border-secondary shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-secondary text-uppercase small">Outstanding</h6>
                        <h2 class="display-6 fw-bold text-white"><?php echo $stats['Outstanding']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 text-uppercase small">In Progress</h6>
                        <h2 class="display-6 fw-bold"><?php echo $stats['In Progress']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 text-uppercase small">Completed</h6>
                        <h2 class="display-6 fw-bold"><?php echo $stats['Completed']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 text-uppercase small">Cancelled</h6>
                        <h2 class="display-6 fw-bold"><?php echo $stats['Cancelled']; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <h5 class="text-muted mb-3"><i class="bi bi-lightning-charge-fill"></i> Quick Actions</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card action-card text-center p-4 border-secondary h-100" data-bs-toggle="modal" data-bs-target="#createJobModal">
                            <div class="card-body">
                                <i class="bi bi-plus-circle display-4 text-primary mb-3"></i>
                                <h3>Enter New Job</h3>
                                <p class="text-muted small">Create a new delivery consignment.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="jobs_report.php" class="text-decoration-none">
                            <div class="card action-card text-center p-4 border-secondary h-100">
                                <div class="card-body">
                                    <i class="bi bi-search display-4 text-info mb-3"></i>
                                    <h3 class="text-dark">Search Jobs</h3>
                                    <p class="text-muted small">Find and track existing shipments.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-12">
                        <a href="manage_sites.php" class="text-decoration-none">
                            <div class="card action-card p-4 border-secondary">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="text-start">
                                        <h3 class="text-dark mb-1">Manage Fleet</h3>
                                        <p class="text-muted small mb-0">Manage and update vehicle information.</p>
                                    </div>
                                    <i class="bi bi-truck display-5 text-warning"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <h5 class="text-muted mb-3"><i class="bi bi-pie-chart-fill"></i> Performance</h5>
                <div class="card border-secondary shadow-sm">
                    <div class="card-body">
                        <canvas id="jobChart"></canvas>
                    </div>
                    <div class="card-footer bg-white border-0 text-center text-muted small">
                        Total Jobs in System
                    </div>
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
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();
                const url = new URL(window.location);
                url.searchParams.delete('status');
                url.searchParams.delete('job_id');
                window.history.replaceState({}, '', url);
            });
            </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const ctx = document.getElementById('jobChart').getContext('2d');
        const jobChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Outstanding', 'In Progress', 'Completed', 'Cancelled'],
                datasets: [{
                    label: ' # of Jobs',
                    data: [
                        <?php echo $stats['Outstanding']; ?>, 
                        <?php echo $stats['In Progress']; ?>, 
                        <?php echo $stats['Completed']; ?>,
                        <?php echo $stats['Cancelled']; ?>
                    ],
                    backgroundColor: [
                        '#6c757d', // Grey (Outstanding)
                        '#0d6efd', // Blue (In Progress)
                        '#198754', // Green (Completed)
                        '#dc3545'  // Red (Cancelled)
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>

</html>