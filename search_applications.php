<?php
session_start();
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';

$adminObj = new Admin();
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';
$applications = $adminObj->searchBusinesses($searchTerm);
$output = '';

if ($applications) {
    foreach ($applications as $business) {
        $businessStatus = htmlspecialchars($business['business_status']);
        if ($businessStatus == 'Pending Approval') {
            $statusDisplay = '<span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">Pending</span>';
        } else if ($businessStatus == 'Approved') {
            $statusDisplay = '<span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">Accepted</span>';
        } else if ($businessStatus == 'Rejected') {
            $statusDisplay = '<span class="small rounded-5 text-danger border border-danger p-1 border-2 fw-bold">Rejected</span>';
        } else {
            $statusDisplay = '<span class="small rounded-5 text-muted border border-muted p-1 border-2 fw-bold">' . $businessStatus . '</span>';
        }
        $location = htmlspecialchars($business['region_province_city']) . ', ' . htmlspecialchars($business['barangay']) . ', ' . htmlspecialchars($business['street_building_house']);
        $output .= '<tr>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['owner_name']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['business_name']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . $location . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">
                        <i class="fa-solid fa-chevron-down rename small" 
                            data-bs-toggle="modal" 
                            data-bs-target="#moreparkinfo" 
                            data-email="' . htmlspecialchars($business['business_email']) . '"
                            data-phone="' . htmlspecialchars($business['business_phone']) . '"
                            data-hours="' . htmlspecialchars($business['operating_hours']) . '"
                            data-permit="' . htmlspecialchars($business['business_permit']) . '"
                            data-logo="' . htmlspecialchars($business['business_logo']) . '">
                        </i>
                    </td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['created_at']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . $statusDisplay . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">';
        $disabled = ($businessStatus == 'Approved' || $businessStatus == 'Rejected') ? 'disabled' : '';
        $output .= '<div class="d-flex gap-2 justify-content-center">';
        $output .= '<button class="approve-btn bg-success text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px" ' . $disabled . '>Approve</button>';
        $output .= '<button class="deny-btn bg-danger text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px" ' . $disabled . '>Deny</button>';
        $output .= '</div>';
        $output .= '</td>';
        $output .= '</tr>';
    }
} else {
    $output .= '<tr><td colspan="7" class="text-center py-5">No result found</td></tr>';
}

echo $output;
?>
