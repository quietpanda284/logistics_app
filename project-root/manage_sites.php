<?php
include 'config/db_connect.php';
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
    <title>Manage Sites</title>
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
                    <li class="nav-item"><a class="nav-link active" href="manage_sites.php">Manage Sites</a></li>
                    <li class="nav-item"><a class="nav-link" href="search_jobs.php">Search Jobs</a></li>
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
        <h2>Manage Sites</h2>
        <div class="card bg-dark text-white mt-4">
            <div class="card-body">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Site Name</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 1. Fetch Data
                        $sql = "SELECT * FROM sites";
                        $result = mysqli_query($conn, $sql);

                        // 2. The Loop
                        while ($row = mysqli_fetch_assoc($result)) {
                            $site_id = $row['site_id']; // Store ID in a variable for easier use

                            echo "<tr>";
                            echo "<td>" . $row['site_name'] . "</td>";
                            echo "<td>" . $row['address'] . "</td>";
                            echo "<td>";

                            // --- THE TRIGGER BUTTON ---
                            // Notice the target: #editModal_1, #editModal_2, etc.
                            echo '<button type="button" class="btn btn-warning btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editModal_' . $site_id . '">
                        Edit
                      </button>';

                            echo "</td>";
                            echo "</tr>";

                            // --- THE MODAL (Generated INSIDE the loop) ---
                            // We create a hidden modal immediately after the row
                        ?>

                            <div class="modal fade" id="editModal_<?php echo $site_id; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content bg-dark text-white border-secondary">

                                        <div class="modal-header border-secondary">
                                            <h5 class="modal-title">Edit Site: <?php echo $row['site_name']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form action="actions/update_site_logic.php" method="POST">

                                                <input type="hidden" name="site_id" value="<?php echo $site_id; ?>">

                                                <div class="mb-3">
                                                    <label>Site Name</label>
                                                    <input type="text" name="site_name" class="form-control bg-secondary text-white border-0"
                                                        value="<?php echo $row['site_name']; ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Address</label>
                                                    <textarea name="address" class="form-control bg-secondary text-white border-0" rows="3"><?php echo $row['address']; ?></textarea>
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                                </div>

                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php
                        } // End of While Loop
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>