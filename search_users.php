<?php
session_start();
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';

$adminObj = new Admin();

$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';

$users = $adminObj->getUsers($searchTerm);

$output = '';
$totalAccounts = count($users);

if ($users) {
    foreach ($users as $user) {
        $output .= '<tr>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['first_name']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['last_name']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['email']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . "+63" . htmlspecialchars($user['phone']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['birth_date']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['sex']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['status']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-1"><span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">' . htmlspecialchars($user['role']) . '</span></td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['created_at']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">';
        $output .= '<div class="dropdown position-relative">';
        $output .= '<i class="fa-solid fa-ellipsis small rename py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>';
        $output .= '<ul class="dropdown-menu dropdown-menu-center p-0" style="box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">';
        $output .= '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edituser">Edit</a></li>';
        $output .= '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteuser">Delete</a></li>';
        $output .= '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deactivateuser">Deactivate</a></li>';
        $output .= '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#activitylog">Activity</a></li>';
        $output .= '<li><a class="dropdown-item" href="parkregistration.php">Create Park</a></li>';
        $output .= '</ul>';
        $output .= '</div>';
        $output .= '</td>';
        $output .= '</tr>';
    }
} else {
    $output .= '<tr><td colspan="10" class="text-center py-5">No result found</td></tr>';
}

echo $output;
echo '<script>document.getElementById("totalAccounts").textContent = ' . $totalAccounts . ';</script>';