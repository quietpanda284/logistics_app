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
          <li class="nav-item"><a class="nav-link" href="manage_vehicles.php">Manage Vehicles</a></li>
          <li class="nav-item"><a class="nav-link active" href="search_jobs.php">Search Jobs</a></li>
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
    <h2 class="mb-4">Job Status Report</h2>
    <div class="card bg-dark border-secondary">
      <div class="card-body">
        <form action="" method="GET" class="mb-3">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by goods name...">
            <button class="btn btn-outline-light" type="submit">Search</button>
          </div>
        </form>

        <table class="table table-dark table-hover">
          <thead>
            <tr>
              <th>Job ID</th>
              <th>Goods</th>
              <th>Route</th>
              <th>Dates</th>
              <th>Hazardous</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            include 'config/db_connect.php';

            $sql = "SELECT 
                        j.job_id, 
                        j.goods_name, 
                        j.goods_quantity, 
                        j.hazardous, 
                        j.start_date, 
                        j.deadline, 
                        j.status,
                        s1.site_name AS start_name, 
                        s2.site_name AS end_name
                    FROM jobs j
                    JOIN sites s1 ON j.start_site_id = s1.site_id
                    JOIN sites s2 ON j.end_site_id = s2.site_id";

            // If user used the search bar, add a filter
            if (isset($_GET['search']) && !empty($_GET['search'])) {
              $search = $_GET['search'];
              $sql .= " WHERE j.goods_name LIKE '%$search%'";
            }

            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {

                // Formatting Logic
                $formatted_id = sprintf("JN%03d", $row['job_id']);
                $hazText = ($row['hazardous'] == 1) ? "<span class='badge bg-danger'>HAZ</span>" : "<span class='badge bg-success'>SAFE</span>";

                echo "<tr>";
                echo "<td>" . $formatted_id . "</td>";

                echo "<td>
                            <strong>" . $row['goods_name'] . "</strong><br>
                            <small class='text-white'>Qty: " . $row['goods_quantity'] . "</small>
                          </td>";

                echo "<td>" . $row['start_name'] . " <br>⬇<br> " . $row['end_name'] . "</td>";

                echo "<td><small>" . $row['start_date'] . " to <br>" . $row['deadline'] . "</small></td>";

                echo "<td>" . $hazText . "</td>";

                echo "<td>" . $row['status'] . "</td>";

                echo "<td>
                            <button type='button' class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='" . $row['job_id'] . "'>
                                X
                            </button>
                      </td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='7' class='text-center'>No jobs found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white border-secondary">
        <div class="modal-header border-secondary">
          <h5 class="modal-title text-warning">Confirm Deletion</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this job?
          <br>
          <span class="text-muted small">This action cannot be undone.</span>
        </div>
        <div class="modal-footer border-secondary">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete Job</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    var deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
      var button = event.relatedTarget;
      var jobId = button.getAttribute('data-id');
      var confirmBtn = deleteModal.querySelector('#confirmDeleteBtn');
      confirmBtn.href = 'actions/delete_jobs.php?id=' + jobId;
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>