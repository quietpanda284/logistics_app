<?php
include '../config/db_connect.php';

$output = '';

// Check if a search term was sent
if (isset($_POST['query'])) {
    $search = mysqli_real_escape_string($conn, $_POST['query']);
    
    // The main query (Same as jobs_report.php)
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
            WHERE j.goods_name LIKE '%$search%' 
               OR v.registration_plate LIKE '%$search%'
               OR j.job_id LIKE '%$search%'
            ORDER BY j.job_id DESC"; // Added ordering for better UX
} else {
    $sql = "SELECT * FROM jobs WHERE 1=0"; // Return nothing if no query
}

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        // Formatting Logic
        $formatted_id = sprintf("JN%03d", $row['job_id']);
        $hazText = ($row['hazardous'] == 1) ? "<span class='badge bg-danger'>HAZ</span>" : "<span class='badge bg-success'>SAFE</span>";
        $regNo = $row['registration_plate'] ? $row['registration_plate'] : "Unassigned";
        $vehType = $row['type_name'] ? $row['type_name'] : "N/A";
        $statusOptions = ['Outstanding', 'Completed', 'Cancelled'];

        $output .= "<tr>";
        $output .= "<td>" . $formatted_id . "</td>";
        $output .= "<td><strong>" . $row['goods_name'] . "</strong><br><small class='text-white-50'>Qty: " . $row['goods_quantity'] . "</small></td>";
        $output .= "<td>" . $row['start_name'] . " <br>⬇<br> " . $row['end_name'] . "</td>";
        $output .= "<td><span title='Type: " . $vehType . "' style='cursor: help; text-decoration: underline dotted;'>" . $regNo . "</span></td>";
        $output .= "<td><small>" . $row['start_date'] . " to <br>" . $row['deadline'] . "</small></td>";
        $output .= "<td>" . $hazText . "</td>";
        $output .= "<td>" . $row['status'] . "</td>";

        // Action Column Logic (Preserving your 'frozen' status logic)
        $output .= "<td>";

        if ($row['status'] === 'Completed') {
            $output .= "<span class='text-success fw-bold'><i class='bi bi-check-circle-fill'></i> Finalized</span>";
        } elseif ($row['status'] === 'Cancelled') {
            $output .= "<span class='text-danger fw-bold'><i class='bi bi-x-circle-fill'></i> Cancelled</span>";
        } else {
            // The Dropdown with triggers
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