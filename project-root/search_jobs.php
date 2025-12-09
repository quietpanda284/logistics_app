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
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5">
    <h2 class="mb-4">Job Status Report</h2>
    <div class="card bg-dark border-secondary">
        <div class="card-body">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Weight (kg)</th>
                        <th>Size (m³)</th>
                        <th>Hazardous</th>
                        <th>Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'config/db_connect.php';

                    $sql = "SELECT * FROM jobs";
                    $result = mysqli_query($conn, $sql);
                    
                    while($row = mysqli_fetch_assoc($result)) {
                        $formatted_id = sprintf("JN%03d", $row['job_id']);
                        $hazText = ($row['hazardous'] == 1) ? "<span class='text-danger'>YES</span>" : "<span class='text-success'>NO</span>";
                        echo "<tr>";
                        //echo "<td>" . $row['job_id'] . "</td>";
                        echo "<td>" . $formatted_id . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['weight'] . "</td>";
                        echo "<td>" . $row['size'] . "</td>";
                        echo "<td>" . $hazText . "</td>";
                        echo "<td>" . $row['deadline'] . "</td>";
                        echo "</tr>";
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