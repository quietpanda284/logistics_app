<?php
session_start();
include 'config/db_connect.php'; // Moved to top for global access

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// FETCH DATA FOR THE "ADD JOB" MODAL DROPDOWNS
$sites = [];
$sql_sites = "SELECT * FROM sites";
$result_sites = mysqli_query($conn, $sql_sites);
while ($row = mysqli_fetch_assoc($result_sites)) {
    $sites[] = $row;
}

$vehicles = [];
$sql_vehicles = "SELECT * FROM vehicles";
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
    <title>Job Status Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        #search_input::placeholder {
            color: #adb5bd !important;
            opacity: 1;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        
        <div class="card bg-dark border-secondary">
            <div class="card-body">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-white mb-0">Job Status Report</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJobModal">
                        <i class="bi bi-plus-lg"></i> Add New Job
                    </button>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="search_input" class="form-control bg-dark text-white border-secondary" placeholder="Start typing to search (Goods, Job ID, Plate No)...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle text-nowrap">
                        <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Goods</th>
                                <th>Route</th>
                                <th>Assigned Vehicle</th>
                                <th>Dates</th>
                                <th>Hazardous</th>
                                <th>Current Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="jobs_table_body">
                            <?php
                            // SQL query remains the same
                            $sql = "SELECT 
                                    j.job_id, 
                                    j.goods_name, 
                                    j.goods_quantity, 
                                    j.hazardous, 
                                    j.start_date, 
                                    j.deadline, 
                                    j.status,
                                    s1.site_name AS start_name, 
                                    s2.site_name AS end_name,
                                    v.registration_plate,
                                    vt.type_name            
                                FROM jobs j
                                JOIN sites s1 ON j.start_site_id = s1.site_id
                                JOIN sites s2 ON j.end_site_id = s2.site_id
                                LEFT JOIN vehicles v ON j.assigned_vehicle_id = v.vehicle_id
                                LEFT JOIN vehicle_types vt ON v.type_id = vt.type_id
                                ORDER BY j.job_id DESC";

                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $formatted_id = sprintf("JN%03d", $row['job_id']);
                                    $hazText = ($row['hazardous'] == 1) ? "<span class='badge bg-danger'>HAZ</span>" : "<span class='badge bg-success'>SAFE</span>";
                                    $regNo = $row['registration_plate'] ? $row['registration_plate'] : "Unassigned";
                                    $vehType = $row['type_name'] ? $row['type_name'] : "N/A";
                                    $statusOptions = ['Outstanding', 'In Progress', 'Completed', 'Cancelled'];

                                    echo "<tr>";
                                    echo "<td>" . $formatted_id . "</td>";
                                    echo "<td><strong>" . $row['goods_name'] . "</strong><br><small class='text-white-50'>Qty: " . $row['goods_quantity'] . "</small></td>";
                                    echo "<td>" . $row['start_name'] . " <br>⬇<br> " . $row['end_name'] . "</td>";
                                    echo "<td><span title='Type: " . $vehType . "' style='cursor: help; text-decoration: underline dotted;'>" . $regNo . "</span></td>";
                                    echo "<td><small>" . $row['start_date'] . " to <br>" . $row['deadline'] . "</small></td>";
                                    echo "<td>" . $hazText . "</td>";
                                    echo "<td>" . $row['status'] . "</td>";
                                    echo "<td>";
                                    if ($row['status'] === 'Completed') {
                                        echo "<span class='text-success fw-bold'><i class='bi bi-check-circle-fill'></i> Completed</span>";
                                    } elseif ($row['status'] === 'Cancelled') {
                                        echo "<span class='text-danger fw-bold'><i class='bi bi-x-circle-fill'></i> Cancelled</span>";
                                    } else {
                                        echo "<select class='form-select form-select-sm bg-dark text-white border-secondary' style='width: 130px;' data-job-id='" . $row['job_id'] . "' data-job-ref='" . $formatted_id . "' data-prev-val='" . $row['status'] . "' onchange='triggerUpdateModal(this)'>";
                                        foreach ($statusOptions as $opt) {
                                            $selected = ($row['status'] == $opt) ? 'selected' : '';
                                            echo "<option value='$opt' $selected>$opt</option>";
                                        }
                                        echo "</select>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addJobModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Create New Job</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

                        <div class="d-grid gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">Submit Job</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Confirm Status Update</h5>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to update Job <strong class="text-info" id="modalJobRef"></strong>?</p>
                    <p class="mb-0">New Status: <strong class="text-warning" id="modalNewStatus"></strong></p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" onclick="cancelUpdate()">Cancel</button>
                    
                    <input type="hidden" id="hiddenJobId">
                    <input type="hidden" id="hiddenStatus">
                    
                    <button type="button" class="btn btn-success" onclick="confirmUpdateAjax()">Confirm Update</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-3.7.1.min.js"></script>

    <script>
        // 1. Search Logic
        function loadJobs() {
            var searchText = $("#search_input").val();
            $.ajax({
                url: "actions/fetch_jobs_results.php",
                method: "POST",
                data: { query: searchText },
                success: function(data) {
                    $("#jobs_table_body").html(data);
                }
            });
        }

        $(document).ready(function() {
            $("#search_input").on("keyup", function() {
                loadJobs();
            });
        });

        // 2. Status Modal Logic
        let currentSelectElement = null;
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));

        function triggerUpdateModal(selectElement) {
            currentSelectElement = selectElement;
            const jobId = selectElement.getAttribute('data-job-id');
            const jobRef = selectElement.getAttribute('data-job-ref');
            const newStatus = selectElement.value;

            document.getElementById('modalJobRef').textContent = jobRef;
            document.getElementById('modalNewStatus').textContent = newStatus;
            document.getElementById('hiddenJobId').value = jobId;
            document.getElementById('hiddenStatus').value = newStatus;

            statusModal.show();
        }

        function cancelUpdate() {
            if (currentSelectElement) {
                const originalValue = currentSelectElement.getAttribute('data-prev-val');
                currentSelectElement.value = originalValue;
            }
            statusModal.hide();
        }

        // 3. AJAX Update Function
        function confirmUpdateAjax() {
            const jobId = document.getElementById('hiddenJobId').value;
            const status = document.getElementById('hiddenStatus').value;

            $.ajax({
                url: "actions/update_job_status.php",
                method: "POST",
                dataType: "json",
                data: {
                    job_id: jobId,
                    status: status
                },
                success: function(response) {
                    if (response.status === 'success') {
                        statusModal.hide();
                        loadJobs(); 
                        currentSelectElement = null;
                    } else {
                        alert("Error: " + response.message);
                        cancelUpdate();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert("System error occurred. Please try again.");
                    cancelUpdate();
                }
            });
        }
    </script>
</body>
</html>