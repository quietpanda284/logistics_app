<?php
include 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Check if an ID was provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 2. Fetch the existing data for this site
    $sql = "SELECT * FROM sites WHERE site_id = $id";
    $result = mysqli_query($conn, $sql);
    $site = mysqli_fetch_assoc($result); // We get just one row
} else {
    // If no ID, kick them back to the list
    header("Location: manage_sites.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-secondary border-0">
                    <div class="card-header">
                        <h3>Edit Site Details</h3>
                    </div>
                    <div class="card-body">
                        
                        <form action="update_site_logic.php" method="POST">
                            
                            <input type="hidden" name="site_id" value="<?php echo $site['site_id']; ?>">

                            <div class="mb-3">
                                <label>Site Name</label>
                                <input type="text" name="site_name" class="form-control" 
                                       value="<?php echo $site['site_name']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="3"><?php echo $site['address']; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success">Save Changes</button>
                            <a href="manage_sites.php" class="btn btn-outline-light">Cancel</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>