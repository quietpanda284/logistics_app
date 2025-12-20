<?php
session_start();

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
  <title>Logistics Co. Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <th>Update Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              include 'config/db_connect.php';

              // Updated Query to include Vehicle details
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

                  echo "<td>
                          <strong>" . $row['goods_name'] . "</strong><br>
                          <small class='text-white-50'>Qty: " . $row['goods_quantity'] . "</small>
                        </td>";

                  echo "<td>" . $row['start_name'] . " <br>⬇<br> " . $row['end_name'] . "</td>";

                  echo "<td>
                          <span title='Type: " . $vehType . "' style='cursor: help; text-decoration: underline dotted;'>" .
                    $regNo .
                    "</span>
                        </td>";

                  echo "<td><small>" . $row['start_date'] . " to <br>" . $row['deadline'] . "</small></td>";

                  echo "<td>" . $hazText . "</td>";

                  echo "<td>" . $row['status'] . "</td>";

                  // Action Column
                  echo "<td>";

                  // CHECK: Is the job already completed?
                  if ($row['status'] === 'Completed') {
                    // If yes, show a "Locked" message instead of the form
                    echo "<span class='text-success fw-bold'><i class='bi bi-check-circle-fill'></i> Finalized</span>";
                  } else {
                    // If no, show the update form as usual
                    echo "<form action='actions/update_job_status.php' method='POST' class='d-flex gap-2'>
            <input type='hidden' name='job_id' value='" . $row['job_id'] . "'>
            <select name='status' class='form-select form-select-sm bg-dark text-white border-secondary' style='width: 130px;'>";

                    foreach ($statusOptions as $opt) {
                      $selected = ($row['status'] == $opt) ? 'selected' : '';
                      echo "<option value='$opt' $selected>$opt</option>";
                    }

                    echo "  </select>
            <button type='submit' class='btn btn-outline-warning btn-sm'>Update</button>
        </form>";
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>