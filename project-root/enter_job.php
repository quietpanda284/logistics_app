<?php
include 'config/db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Fetch Sites for Dropdowns
$sites = [];
$sql_sites = "SELECT * FROM sites";
$result_sites = mysqli_query($conn, $sql_sites);
while ($row = mysqli_fetch_assoc($result_sites)) {
    $sites[] = $row;
}

// 2. Fetch Vehicles for Dropdown
$vehicles = [];
$sql_vehicles = "SELECT * FROM vehicles"; // Assuming your table is named 'vehicles'
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
    <title>Enter New Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card shadow bg-dark text-white border-secondary">
                <div class="card-header border-secondary">
                    <h4 class="pt-2">Create New Job</h4>
                </div>
                <div class="card-body">
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

                        <div class="d-grid gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">Submit Job</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>