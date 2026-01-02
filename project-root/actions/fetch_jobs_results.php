<?php
include '../config/db_connect.php';

$output = '';

if (isset($_POST['query'])) {
    $search = $_POST['query'];
    $search_term = "%" . $search . "%";

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
            LEFT JOIN vehicle_types vt ON v.type_id = vt.type_id
            WHERE j.goods_name LIKE ? 
               OR v.registration_plate LIKE ?
               OR j.job_id LIKE ?
            ORDER BY j.job_id DESC";
            
    // 2. Init and Bind
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // "sss" means String, String, String
        mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo "Query Error";
        exit();
    }
} else {
    exit();
}

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        $formatted_id = sprintf("JN%03d", $row['job_id']);
        $hazText = ($row['hazardous'] == 1) ? "<span class='badge bg-danger'>HAZ</span>" : "<span class='badge bg-success'>SAFE</span>";
        $regNo = $row['registration_plate'] ? $row['registration_plate'] : "Unassigned";
        $vehType = $row['type_name'] ? $row['type_name'] : "N/A";
        $statusOptions = ['Outstanding', 'In Progress', 'Completed', 'Cancelled'];

        $output .= "<tr>";
        $output .= "<td>" . $formatted_id . "</td>";
        $output .= "<td><strong>" . $row['goods_name'] . "</strong><br><small class='text-white-50'>Qty: " . $row['goods_quantity'] . "</small></td>";
        $output .= "<td>" . $row['start_name'] . " <br>⬇<br> " . $row['end_name'] . "</td>";
        $output .= "<td><span title='Type: " . $vehType . "' style='cursor: help; text-decoration: underline dotted;'>" . $regNo . "</span></td>";
        $output .= "<td><small>" . $row['start_date'] . " to <br>" . $row['deadline'] . "</small></td>";
        $output .= "<td>" . $hazText . "</td>";
        $output .= "<td>" . $row['status'] . "</td>";

        $output .= "<td>";
        if ($row['status'] === 'Completed') {
            $output .= "<span class='text-success fw-bold'><i class='bi bi-check-circle-fill'></i> Finalized</span>";
        } elseif ($row['status'] === 'Cancelled') {
            $output .= "<span class='text-danger fw-bold'><i class='bi bi-x-circle-fill'></i> Cancelled</span>";
        } else {
            $output .= "<select 
                        class='form-select form-select-sm bg-dark text-white border-secondary' 
                        style='width: 130px;'
                        data-job-id='" . $row['job_id'] . "'
                        data-job-ref='" . $formatted_id . "'
                        data-prev-val='" . $row['status'] . "' 
                        onchange='triggerUpdateModal(this)'>";
            foreach ($statusOptions as $opt) {
                $selected = ($row['status'] == $opt) ? 'selected' : '';
                $output .= "<option value='$opt' $selected>$opt</option>";
            }
            $output .= "</select>";
        }
        $output .= "</td>";
        $output .= "</tr>";
    }
} else {
    $output .= "<tr><td colspan='8' class='text-center text-muted py-4'>No jobs found matching your search.</td></tr>";
}

echo $output;
?>