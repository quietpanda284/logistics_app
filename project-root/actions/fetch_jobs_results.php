<?php
include '../config/db_connect.php';

$output = '';

// 1. Default inputs to prevent undefined variable errors
$search_input = $_POST['query'] ?? '';
$sort_option  = $_POST['sort'] ?? 'newest';
$status_filter = $_POST['status_filter'] ?? 'All';

// 2. Prepare the wildcard search term
$search_term = "%" . $search_input . "%";

// 3. Prepare Status Filter logic
// If filter is 'All', use wildcard '%' to match anything. Otherwise match exact status.
$status_search = ($status_filter === 'All') ? "%" : $status_filter;

// 4. Prepare Sorting Logic (Whitelist allowed columns to prevent SQL injection)
$allowed_sorts = [
    'newest'   => 'j.job_id DESC',
    'oldest'   => 'j.job_id ASC',
    'deadline' => 'j.deadline ASC',
    'status'   => 'j.status ASC',
    'goods'    => 'j.goods_name ASC'
];
$order_sql = "ORDER BY " . ($allowed_sorts[$sort_option] ?? 'j.job_id DESC');

// 5. Build the SQL Query
// Note: We use 4 placeholders (?) total: 3 for search, 1 for status
$sql = "SELECT 
            j.job_id, j.goods_name, j.goods_quantity, j.hazardous, j.start_date, j.deadline, j.status,
            s1.site_name AS start_name, s2.site_name AS end_name,
            v.registration_plate, vt.type_name            
        FROM jobs j
        JOIN sites s1 ON j.start_site_id = s1.site_id
        JOIN sites s2 ON j.end_site_id = s2.site_id
        LEFT JOIN vehicles v ON j.assigned_vehicle_id = v.vehicle_id
        LEFT JOIN vehicle_types vt ON v.type_id = vt.type_id
        WHERE (j.goods_name LIKE ? OR v.registration_plate LIKE ? OR j.job_id LIKE ?)
        AND j.status LIKE ? 
        " . $order_sql;

// 6. Execute Query
if ($stmt = mysqli_prepare($conn, $sql)) {
    // Bind 4 string parameters ("ssss")
    mysqli_stmt_bind_param($stmt, "ssss", $search_term, $search_term, $search_term, $status_search);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        // 7. Check if we found rows
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $formatted_id = sprintf("JN%03d", $row['job_id']);
                $hazText = ($row['hazardous'] == 1) ? "<span class='badge bg-danger'>HAZ</span>" : "<span class='badge bg-success'>SAFE</span>";
                $regNo = $row['registration_plate'] ? $row['registration_plate'] : "Unassigned";
                $vehType = $row['type_name'] ? $row['type_name'] : "N/A";
                
                $output .= "<tr>";
                $output .= "<td>" . $formatted_id . "</td>";
                $output .= "<td><strong>" . htmlspecialchars($row['goods_name']) . "</strong><br><small class='text-white-50'>Qty: " . $row['goods_quantity'] . "</small></td>";
                $output .= "<td>" . htmlspecialchars($row['start_name']) . " <br>⬇<br> " . htmlspecialchars($row['end_name']) . "</td>";
                $output .= "<td><span title='Type: " . $vehType . "' style='cursor: help; text-decoration: underline dotted;'>" . $regNo . "</span></td>";
                $output .= "<td><small>" . $row['start_date'] . " to <br>" . $row['deadline'] . "</small></td>";
                $output .= "<td>" . $hazText . "</td>";
                $output .= "<td>" . $row['status'] . "</td>";
                
                // Status Dropdown Logic
                $output .= "<td>";
                if ($row['status'] === 'Completed') {
                    $output .= "<span class='text-success fw-bold'><i class='bi bi-check-circle-fill'></i> Finalized</span>";
                } elseif ($row['status'] === 'Cancelled') {
                    $output .= "<span class='text-danger fw-bold'><i class='bi bi-x-circle-fill'></i> Cancelled</span>";
                } else {
                    $opts = ['Outstanding', 'In Progress', 'Completed', 'Cancelled'];
                    $output .= "<select class='form-select form-select-sm bg-dark text-white border-secondary' style='width: 130px;' data-job-id='".$row['job_id']."' data-job-ref='".$formatted_id."' data-prev-val='".$row['status']."' onchange='triggerUpdateModal(this)'>";
                    foreach($opts as $opt){
                        $sel = ($row['status'] == $opt) ? 'selected' : '';
                        $output .= "<option value='$opt' $sel>$opt</option>";
                    }
                    $output .= "</select>";
                }
                $output .= "</td></tr>";
            }
        } else {
            // No results found (not an error)
            $output .= "<tr><td colspan='8' class='text-center text-white py-4'>No jobs found matching your criteria.</td></tr>";
        }
    } else {
        // SQL Execution Error
        $output .= "<tr><td colspan='8' class='text-danger text-center'>Error executing query: " . mysqli_error($conn) . "</td></tr>";
    }
    mysqli_stmt_close($stmt);
} else {
    // SQL Preparation Error
    $output .= "<tr><td colspan='8' class='text-danger text-center'>Database Error: " . mysqli_error($conn) . "</td></tr>";
}

echo $output;
?>