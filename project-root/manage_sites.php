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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="card bg-dark border-secondary shadow">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <h4 class="text-white mb-0">Manage Sites</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSiteModal">
                        + Add New Site
                    </button>
                </div>

                <div class="modal fade" id="addSiteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content bg-dark text-white border-secondary">
                            <div class="modal-header border-secondary">
                                <h5 class="modal-title">Add New Site</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form action="actions/insert_site_logic.php" method="POST">
                                    <div class="mb-3">
                                        <label>Site Name</label>
                                        <input type="text" name="site_name" class="form-control bg-secondary text-white border-0" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Address</label>
                                        <textarea name="address" class="form-control bg-secondary text-white border-0" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Add Site</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                // 1. FETCH DATA FIRST
                // We store all rows in an array ($sites) so we can loop through them twice:
                // once for the table rows, and once for the modals outside the table.
                
                $sql = "SELECT s.*, 
                            (SELECT COUNT(*) FROM vehicles v WHERE v.site_id = s.site_id AND v.type_id = 1) as lwb,
                            (SELECT COUNT(*) FROM vehicles v WHERE v.site_id = s.site_id AND v.type_id = 2) as luton,
                            (SELECT COUNT(*) FROM vehicles v WHERE v.site_id = s.site_id AND v.type_id = 3) as curtainside
                            FROM sites s";

                $result = mysqli_query($conn, $sql);
                $sites = []; // Initialize array
                while ($row = mysqli_fetch_assoc($result)) {
                    $sites[] = $row;
                }
                ?>

                <div class="table-responsive">
                    <table class="table table-dark table-hover border-secondary align-middle text-nowrap">
                        <thead>
                            <tr>
                                <th>Site Name</th>
                                <th>Address</th>
                                <th class="text-center">LWB</th>
                                <th class="text-center">Luton</th>
                                <th class="text-center">Curtainside</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sites as $row): 
                                $site_id = $row['site_id'];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['site_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['address']); ?></td>

                                    <td class="text-center">
                                        <p><?php echo $row['lwb']; ?></p>
                                    </td>

                                    <td class="text-center">
                                        <p><?php echo $row['luton']; ?></p>
                                    </td>

                                    <td class="text-center">
                                        <p><?php echo $row['curtainside']; ?></p>
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $site_id; ?>">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div> </div>
        </div>
    </div>

    <?php foreach ($sites as $row): 
        $site_id = $row['site_id'];
    ?>
        <div class="modal fade" id="editModal_<?php echo $site_id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white border-secondary">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title">Edit Site</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="actions/update_site_logic.php" method="POST">
                            <input type="hidden" name="site_id" value="<?php echo $site_id; ?>">

                            <div class="mb-3">
                                <label>Site Name</label>
                                <input type="text" name="site_name" class="form-control bg-secondary text-white" value="<?php echo htmlspecialchars($row['site_name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Address</label>
                                <textarea name="address" class="form-control bg-secondary text-white"><?php echo htmlspecialchars($row['address']); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 mt-3">Save Changes</button>
                        </form>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteSiteModal_<?php echo $site_id; ?>">
                                    Delete Site
                                </button>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteSiteModal_<?php echo $site_id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-white border-secondary">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-danger">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $site_id; ?>"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($row['site_name']); ?></strong>?</p>
                        <span class="text-danger small">This action cannot be undone.</span>
                    </div>
                    <div class="modal-footer border-secondary justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $site_id; ?>">Cancel</button>

                        <form action="actions/delete_sites.php" method="POST">
                            <input type="hidden" name="site_id" value="<?php echo $site_id; ?>">
                            <button type="submit" class="btn btn-danger">Delete Site</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>