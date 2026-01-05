<?php
session_start();
include 'config/db_connect.php';

// Security Check: Ideally only Admins should access this tool
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $count = intval($_POST['num_records']);
    
    if ($count > 0) {
        // 1. Fetch valid IDs to ensure foreign key integrity
        // We cannot assign a job to a site that doesn't exist!
        $site_ids = [];
        $res = mysqli_query($conn, "SELECT site_id FROM sites");
        while ($row = mysqli_fetch_assoc($res)) $site_ids[] = $row['site_id'];

        $vehicle_ids = [];
        $res = mysqli_query($conn, "SELECT vehicle_id FROM vehicles");
        while ($row = mysqli_fetch_assoc($res)) $vehicle_ids[] = $row['vehicle_id'];

        // Check if we have enough base data
        if (empty($site_ids) || count($site_ids) < 2) {
            $message = "Error: You need at least 2 Sites in the database to generate routes.";
            $msg_type = "danger";
        } elseif (empty($vehicle_ids)) {
            $message = "Error: You need at least 1 Vehicle in the database to assign jobs.";
            $msg_type = "danger";
        } else {
            // 2. Prepared Statement for Insertion
            $sql = "INSERT INTO jobs (goods_name, goods_quantity, weight, size, hazardous, start_date, deadline, start_site_id, end_site_id, assigned_vehicle_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            
            $inserted = 0;
            
            // 3. Loop to Generate Data
            for ($i = 0; $i < $count; $i++) {
                // Random Data Generation
                $goods_list = ['Electronics', 'Furniture', 'Chemicals', 'Foodstuffs', 'Textiles', 'Machinery', 'Paper', 'Steel', 'Medical Supplies', 'Auto Parts'];
                $goods_name = $goods_list[array_rand($goods_list)] . " Batch-" . rand(100, 999);
                
                $quantity = rand(10, 500);
                $weight = rand(50, 5000);
                $size = rand(1, 20);
                $hazardous = (rand(1, 100) <= 20) ? 1 : 0; // 20% chance of being hazardous
                
                // Random Dates (Last 6 months to Next 2 months)
                $start_timestamp = strtotime("-" . rand(0, 180) . " days"); 
                $start_date = date("Y-m-d", $start_timestamp);
                // Deadline is always 2-10 days after start
                $deadline = date("Y-m-d", strtotime($start_date . " + " . rand(2, 10) . " days"));
                
                // Random Route (Start != End)
                $start_site = $site_ids[array_rand($site_ids)];
                do {
                    $end_site = $site_ids[array_rand($site_ids)];
                } while ($start_site == $end_site);
                
                // Random Vehicle
                $vehicle = $vehicle_ids[array_rand($vehicle_ids)];
                
                // Random Status
                // Weighted: More 'Completed' jobs to simulate history
                $rand_stat = rand(1, 10);
                if ($rand_stat <= 6) $status = 'Completed'; // 60%
                elseif ($rand_stat <= 8) $status = 'In Progress'; // 20%
                elseif ($rand_stat <= 9) $status = 'Outstanding'; // 10%
                else $status = 'Cancelled'; // 10%
                
                mysqli_stmt_bind_param($stmt, "siiiissiiis", $goods_name, $quantity, $weight, $size, $hazardous, $start_date, $deadline, $start_site, $end_site, $vehicle, $status);
                
                if (mysqli_stmt_execute($stmt)) {
                    $inserted++;
                }
            }
            $message = "Success! Generated $inserted random job records.";
            $msg_type = "success";
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Sample Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-dark text-white">

    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-secondary border-0 shadow-lg text-white">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-database-add display-1 text-warning mb-3"></i>
                        <h3 class="card-title mb-3">Sample Data Generator</h3>
                        <p class="text-light mb-4">Task D: Populate the database with random job records for performance testing.</p>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-4 text-start">
                                <label class="form-label">Number of Records to Generate</label>
                                <input type="number" name="num_records" class="form-control form-control-lg text-center fw-bold" value="50" min="1" max="1000">
                                <div class="form-text text-light opacity-75">Recommended: 50-100 per batch.</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark">
                                    <i class="bi bi-gear-wide-connected"></i> Generate Data
                                </button>
                                <a href="index.php" class="btn btn-outline-light">Return to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>