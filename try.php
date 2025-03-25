<?php
session_start();
include_once 'landingheader.php';
include_once 'links.php';
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/user.class.php';
require_once './email/verification_token.class.php';

$userObj = new User();
$adminObj = new Admin();
$isLoggedIn = false;
if (isset($_SESSION['user'])) {
    if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
        $isLoggedIn = true;
    } else {
        header('Location: email/verify_email.php');
        exit();
    }
}
date_default_timezone_set('Asia/Manila');
$currentDateTime = date("l, F j, Y h:i A");
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    main{ padding: 20px 120px; }
    .salestable th{ padding-top: 10px; width: 10%; }
    .dropdown-menu-center { left: 50% !important; transform: translateX(-50%) !important; }
    .acchead a{ text-decoration: none; color: black; margin-bottom: 8px; }
    button:disabled { background-color: #D3d3d3 !important; }
</style>
<main>
    <div class="nav-container d-flex gap-3 my-2">
        <a href="#all" class="nav-link" data-target="all">Accounts</a>
        <a href="#applications" class="nav-link" data-target="applications">Applications</a>
        <a href="#reports" class="nav-link" data-target="reports">Reports</a>
    </div>
    <!-- Accounts Section -->
    <div id="all" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Manage Accounts</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
            <button class="disatc m-0 small" data-bs-toggle="modal" data-bs-target="#adduser">+ Add User</button>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="#" method="get" class="searchmenu rounded-2">
                <input type="text" name="search" placeholder="Search account" style="width: 230px;" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="m-0 ms-2"><i class="fas fa-search fa-lg small"></i></button>
            </form>
        </div>
        <table class="salestable w-100 text-center border-top">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Birthday</th>
                <th>Sex</th>
                <th>Status</th>
                <th>Role</th>
                <th>Date Created</th>
                <th>Action</th>
            </tr>
            <tbody id="userTableBody">
                <?php
                $users = $adminObj->getUsers($searchTerm);
                if ($users) {
                    foreach ($users as $user) {
                        echo '<tr>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['first_name']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['last_name']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['email']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . "+63" . htmlspecialchars($user['phone']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['birth_date']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['sex']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['status']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-1"><span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">' . htmlspecialchars($user['role']) . '</span></td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($user['created_at']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">';
                        echo '<div class="dropdown position-relative">';
                        echo '<i class="fa-solid fa-ellipsis small rename py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>';
                        echo '<ul class="dropdown-menu dropdown-menu-center p-0" style="box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">';
                        echo '<li><a class="dropdown-item edit-user" href="#" data-bs-toggle="modal" data-bs-target="#edituser" data-user-id="' . $user['id'] . '" data-first-name="' . htmlspecialchars($user['first_name']) . '" data-middle-name="' . htmlspecialchars($user['middle_name']) . '" data-last-name="' . htmlspecialchars($user['last_name']) . '" data-email="' . htmlspecialchars($user['email']) . '" data-phone="' . htmlspecialchars($user['phone']) . '" data-birth-date="' . $user['birth_date'] . '" data-sex="' . htmlspecialchars($user['sex']) . '">Edit</a></li>';
                        echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteuser" data-user-id="' . $user['id'] . '">Delete</a></li>';
                        echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deactivateuser">Deactivate</a></li>';
                        echo '<li><a class="dropdown-item activity-log" href="#" data-bs-toggle="modal" data-bs-target="#activitylog" data-user-id="' . $user['id'] . '">Activity</a></li>';
                        if ($user['role'] == 'Customer') {
                            echo '<li><a class="dropdown-item" href="parkregistration.php?user_id=' . $user['id'] . '">Create Park</a></li>';
                        }                        
                        echo '</ul>';
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="10" class="text-center py-5">No result found</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3"></div>
    </div>

    <!-- Applications Section -->
    <div id="applications" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Applications</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="#" method="get" class="searchmenu rounded-2">
                <input type="text" name="search_application" placeholder="Search application" style="width: 230px;" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="m-0 ms-2"><i class="fas fa-search fa-lg small"></i></button>
            </form>
        </div>
        <table class="salestable w-100 text-center border-top">
            <tr>
                <th style="width: 17%;">Owner</th>
                <th style="width: 18%;">Business Name</th>
                <th style="width: 25%;">Location</th>
                <th style="width: 10%;">Other Info</th>
                <th style="width: 10%;">Date Applied</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Action</th>
            </tr>
            <tbody id="applicationsTableBody">
                <?php
                $getBusinesses = $adminObj->getBusinesses();
                if ($getBusinesses) {
                    foreach ($getBusinesses as $business) {
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
                        echo '<tr>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['owner_name']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['business_name']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['region_province_city']) . ', ' . htmlspecialchars($business['barangay']) . ', ' . htmlspecialchars($business['street_building_house']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">
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
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['created_at']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4 status-cell">' . $statusDisplay . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">';
                        echo '<div class="d-flex gap-2 justify-content-center">';
                        $disabled = ($businessStatus == 'Approved' || $businessStatus == 'Rejected') ? 'disabled' : '';
                        echo '<button class="approve-btn bg-success text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px" ' . $disabled . '>Approve</button>';
                        echo '<button class="deny-btn bg-danger text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px" ' . $disabled . '>Deny</button>';
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center py-5">No result found</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3"></div>
    </div>

    <!-- Reports Section -->
    <div id="reports" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Report</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="#" method="get" class="searchmenu rounded-2">
                <input type="text" name="search_report" placeholder="Search report" style="width: 230px;" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="m-0 ms-2"><i class="fas fa-search fa-lg small"></i></button>
            </form>
        </div>
        <table class="salestable w-100 text-center border-top">
            <tr>
                <th>Reported By</th>
                <th>Reported User</th>
                <th>Reason</th>
                <th>Date Reported</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <tbody id="reportsTableBody">
                <?php
                $reports = $adminObj->getReports();
                if ($reports) {
                    foreach ($reports as $report) {
                        $fullReporter = htmlspecialchars($report['reporter_first'] . ' ' . $report['reporter_last']);
                        $fullReported = htmlspecialchars($report['reported_first'] . ' ' . $report['reported_last']);
                        echo '<tr>';
                        echo '<td class="fw-normal small py-3 px-4">' . $fullReporter . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . $fullReported . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($report['reason']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($report['created_at']) . '</td>';
                        $status = $report['status'];
                        if ($status == 'Pending') {
                            $statusHTML = '<span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">Pending</span>';
                        } elseif ($status == 'Rejected') {
                            $statusHTML = '<span class="small rounded-5 text-danger border border-danger p-1 border-2 fw-bold">Rejected</span>';
                        } elseif ($status == 'Resolved') {
                            $statusHTML = '<span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">Resolved</span>';
                        }
                        echo '<td class="fw-normal small py-3 px-4">' . $statusHTML . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">';
                        if ($report['status'] == 'Pending') {
                            echo '<form method="POST" action="" style="display:inline-block; margin-right:5px;">
                                    <input type="hidden" name="report_id" value="' . $report['id'] . '">
                                    <input type="hidden" name="action" value="resolve">
                                    <input type="submit" name="report_update" value="Resolve" class="bg-success text-white border-0 small py-1 rounded-1" style="width:60px;">
                                  </form>';
                            echo '<form method="POST" action="" style="display:inline-block;">
                                    <input type="hidden" name="report_id" value="' . $report['id'] . '">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="submit" name="report_update" value="Reject" class="bg-danger text-white border-0 small py-1 rounded-1" style="width:60px;">
                                  </form>';
                        } else {
                            echo '<input type="button" value="Resolve" class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px;" disabled>
                                  <input type="button" value="Reject" class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px;" disabled>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center py-5">No reports found</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3"></div>
    </div>

    <script>
    // For Accounts search (search_users.php)
    $('input[name="search"]').on('keyup', function() {
        var searchValue = $(this).val();
        $.ajax({
            url: 'search_users.php',
            type: 'POST',
            data: { search: searchValue },
            success: function(response) {
                $('#userTableBody').html(response);
            },
            error: function() {
                console.error('An error occurred while fetching user search results.');
            }
        });
    });

    // For Applications search (search_applications.php)
    $('input[name="search_application"]').on('keyup', function() {
        var searchValue = $(this).val();
        $.ajax({
            url: 'search_applications.php',
            type: 'POST',
            data: { search: searchValue },
            success: function(response) {
                $('#applicationsTableBody').html(response);
            },
            error: function() {
                console.error('An error occurred while fetching application search results.');
            }
        });
    });

    // For Reports search (search_reports.php)
    $('input[name="search_report"]').on('keyup', function() {
        var searchValue = $(this).val();
        $.ajax({
            url: 'search_reports.php',
            type: 'POST',
            data: { search: searchValue },
            success: function(response) {
                $('#reportsTableBody').html(response);
            },
            error: function() {
                console.error('An error occurred while fetching report search results.');
            }
        });
    });
    </script>
    <script src="assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/adminresponse.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/navigation.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/pagination.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/activate.js?v=<?php echo time(); ?>"></script>
    <br><br><br><br>
</main>
<?php
include_once 'footer.php';
?>
