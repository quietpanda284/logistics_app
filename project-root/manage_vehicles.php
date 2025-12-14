<?php
session_start();
include 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT v.vehicle_id, v.registration_plate, s.site_name, vt.type_name, vt.max_weight 
        FROM vehicles v
        JOIN sites s ON v.site_id = s.site_id
        JOIN vehicle_types vt ON v.type_id = vt.type_id
        ORDER BY s.site_name ASC";

$result = mysqli_query($conn, $sql);

// We also need lists for the 'Add Modal' dropdowns later
$sites = mysqli_query($conn, "SELECT * FROM sites");
$types = mysqli_query($conn, "SELECT * FROM vehicle_types");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fleet Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Logistics Co.</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="enter_job.php">Create Job</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_sites.php">Manage Sites</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_vehicles.php">Manage Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link" href="search_jobs.php">Search Jobs</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                            <li><a class="dropdown-item" href="actions/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card bg-dark border-secondary shadow">
            <div class="card-body">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title text-white">Fleet Management</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                        + Add New Vehicle
                    </button>
                </div>

                <table class="table table-dark table-hover">
                    <thead>
                        <tr class="text-muted">
                            <th>Plate Number</th>
                            <th>Type</th>
                            <th>Capacity (kg)</th>
                            <th>Current Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($row['registration_plate']); ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo $row['type_name']; ?></span></td>
                            <td><?php echo $row['max_weight']; ?> kg</td>
                            <td><?php echo $row['site_name']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade" id="addVehicleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Register New Vehicle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="actions/insert_vehicle_logic.php" method="POST">
                        <div class="mb-3">
                            <label class="mb-2">Registration Plate</label>
                            <input type="text" name="reg_plate" class="form-control bg-secondary text-white border-0" required placeholder="e.g. LDN-55-REG">
                        </div>
                        
                        <div class="mb-3">
                            <label class="mb-2">Vehicle Type</label>
                            <select name="type_id" class="form-select bg-secondary text-white border-0">
                                <?php foreach($types as $type): ?>
                                    <option value="<?php echo $type['type_id']; ?>">
                                        <?php echo $type['type_name']; ?> (Max <?php echo $type['max_weight']; ?>kg)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="mb-2">Parked At (Site)</label>
                            <select name="site_id" class="form-select bg-secondary text-white border-0">
                                <?php foreach($sites as $site): ?>
                                    <option value="<?php echo $site['site_id']; ?>">
                                        <?php echo $site['site_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Add to Fleet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>