<?php
session_start();
include 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
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
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Job Status Report</h2>

        <div class="card bg-dark border-secondary">
            <div class="card-body">
                <form action="" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by goods name...">
                        <button class="btn btn-outline-light" type="submit">Search</button>
                    </div>
                </form>

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
                                <th>Action</th> </tr>
                        </thead>
                        <tbody>
                            <?php
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
                    LEFT JOIN vehicle_types vt ON v.type_id = vt.type_id";

                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                $search = $_GET['search'];
                                $sql .= " WHERE j.goods_name LIKE '%$search%'";
                            }

                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {

                                    $formatted_id = sprintf("JN%03d", $row['job_id']);
                                    $hazText = ($row['hazardous'] == 1) ? "<span class='badge bg-danger'>HAZ</span>" : "<span class='badge bg-success'>SAFE</span>";
                                    $regNo = $row['registration_plate'] ? $row['registration_plate'] : "Unassigned";
                                    $vehType = $row['type_name'] ? $row['type_name'] : "N/A";
                                    $statusOptions = ['Outstanding', 'Completed', 'Cancelled'];

                                    echo "<tr>";
                                    echo "<td>" . $formatted_id . "</td>";
                                    echo "<td><strong>" . $row['goods_name'] . "</strong><br><small class='text-white-50'>Qty: " . $row['goods_quantity'] . "</small></td>";
                                    echo "<td>" . $row['start_name'] . " <br>⬇<br> " . $row['end_name'] . "</td>";
                                    echo "<td><span title='Type: " . $vehType . "' style='cursor: help; text-decoration: underline dotted;'>" . $regNo . "</span></td>";
                                    echo "<td><small>" . $row['start_date'] . " to <br>" . $row['deadline'] . "</small></td>";
                                    echo "<td>" . $hazText . "</td>";
                                    echo "<td>" . $row['status'] . "</td>";

                                    // --- NEW ACTION COLUMN LOGIC ---
                                    echo "<td>";

                                    if ($row['status'] === 'Completed') {
                                        // Frozen State
                                        echo "<span class='text-success fw-bold'><i class='bi bi-check-circle-fill'></i> Finalized</span>";
                                    } else {
                                        // Live Dropdown (No Form Button)
                                        echo "<select 
                                                class='form-select form-select-sm bg-dark text-white border-secondary' 
                                                style='width: 130px;'
                                                data-job-id='" . $row['job_id'] . "'
                                                data-job-ref='" . $formatted_id . "'
                                                data-prev-val='" . $row['status'] . "' 
                                                onchange='triggerUpdateModal(this)'>";

                                        foreach ($statusOptions as $opt) {
                                            $selected = ($row['status'] == $opt) ? 'selected' : '';
                                            echo "<option value='$opt' $selected>$opt</option>";
                                        }

                                        echo "</select>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No jobs found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
                    <p class="text-danger text-sm">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" onclick="cancelUpdate()">Cancel</button>
                    
                    <form action="actions/update_job_status.php" method="POST">
                        <input type="hidden" name="job_id" id="hiddenJobId">
                        <input type="hidden" name="status" id="hiddenStatus">
                        <button type="submit" class="btn btn-success">Confirm Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentSelectElement = null;
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));

        function triggerUpdateModal(selectElement) {
            // 1. Store the element so we can revert if cancelled
            currentSelectElement = selectElement;

            // 2. Get Data
            const jobId = selectElement.getAttribute('data-job-id');
            const jobRef = selectElement.getAttribute('data-job-ref');
            const newStatus = selectElement.value;

            // 3. Populate Modal
            document.getElementById('modalJobRef').textContent = jobRef;
            document.getElementById('modalNewStatus').textContent = newStatus;
            
            // 4. Populate Hidden Form Fields
            document.getElementById('hiddenJobId').value = jobId;
            document.getElementById('hiddenStatus').value = newStatus;

            // 5. Show Modal
            statusModal.show();
        }

        function cancelUpdate() {
            // 1. Revert the dropdown to its original value (stored in data-prev-val)
            if (currentSelectElement) {
                const originalValue = currentSelectElement.getAttribute('data-prev-val');
                currentSelectElement.value = originalValue;
            }
            
            // 2. Hide Modal
            statusModal.hide();
        }
    </script>
</body>

</html>