<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Logistics Co.</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'manage_sites.php') ? 'active' : ''; ?>" href="manage_sites.php">Sites</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'manage_vehicles.php') ? 'active' : ''; ?>" href="manage_vehicles.php">Vehicles</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'jobs_report.php') ? 'active' : ''; ?>" href="jobs_report.php">Jobs</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo ($current_page == 'account.php') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                        <li><a class="dropdown-item" href="account.php">Profile</a></li>
                        <li><a class="dropdown-item" href="actions/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>