<?php
session_start();
include 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ... (Keep your existing Data Fetching for the Add Job Modal here) ... 
// (For brevity, I'm skipping the sites/vehicles fetching code, keep it as is)
$sites = [];
$vehicles = []; 
// ...
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

                <div class="row mb-3 g-2">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary border-secondary text-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="search_input" class="form-control bg-dark text-white border-secondary" placeholder="Start typing to search (Goods, Job ID, Plate No)...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="sort_select" class="form-select bg-dark text-white border-secondary">
                            <option value="newest" selected>Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="deadline">Deadline (Soonest)</option>
                            <option value="status">Status</option>
                            <option value="goods">Goods Name</option>
                        </select>
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
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-3.7.1.min.js"></script>

    <script>
        // MODIFIED: Search + Sort Logic
        function loadJobs() {
            var searchText = $("#search_input").val();
            var sortOption = $("#sort_select").val(); // Get sort value
            
            $.ajax({
                url: "actions/fetch_jobs_results.php",
                method: "POST",
                data: {
                    query: searchText,
                    sort: sortOption // Send to backend
                },
                success: function(data) {
                    $("#jobs_table_body").html(data);
                }
            });
        }

        $(document).ready(function() {
            // Load immediately on page ready
            loadJobs();

            // Bind listeners
            $("#search_input").on("keyup", function() {
                loadJobs();
            });
            
            // New Listener for Sort Dropdown
            $("#sort_select").on("change", function() {
                loadJobs();
            });
        });

        // ... (Keep your existing Modal JS logic) ...
        // ...
    </script>
</body>
</html>