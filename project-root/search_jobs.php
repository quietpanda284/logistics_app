<?php
session_start();

// The Guard: If no ID in session, kick them out
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
          <li class="nav-item"><a class="nav-link active" href="search_jobs.php">Search Jobs</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Account
            </a>
            <ul class="dropdown-menu">
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

                // Column 2: Goods (Name + Qty combined)
                echo "<td>
                            <strong>" . $row['goods_name'] . "</strong><br>
                            <small class='text-white'>Qty: " . $row['goods_quantity'] . "</small>
                          </td>";

                // Column 3: The Route (Using the Joined Names!)
                echo "<td>" . $row['start_name'] . " <br>⬇<br> " . $row['end_name'] . "</td>";

                // Column 4: Dates
                echo "<td><small>" . $row['start_date'] . " to <br>" . $row['deadline'] . "</small></td>";

                echo "<td>" . $hazText . "</td>";

                echo "<td>" . $row['status'] . "</td>";

                // Column 7: Delete Button (We will build the logic next)
                echo "<td>
                            <a href='actions/delete_jobs.php?id=" . $row['job_id'] . "' 
                               class='btn btn-danger btn-sm'
                               onclick='return confirm(\"Are you sure?\");'>
                               X
                            </a>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>