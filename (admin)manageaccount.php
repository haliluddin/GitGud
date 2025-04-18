
<?php
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

$verificationObj = new Verification();

$first_name = $middle_name = $last_name = $phone = $email = $dob = $sex = $password = $confirm_password = '';
$first_name_err = $middle_name_err = $last_name_err = $phone_err = $email_err = $dob_err = $sex_err = $password_err = $confirm_password_err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['edit_user_id'];
        $first_name = htmlspecialchars(trim($_POST['edit_first_name']));
        $middle_name = htmlspecialchars(trim($_POST['edit_middle_name']));
        $last_name = htmlspecialchars(trim($_POST['edit_last_name']));
        $birth_date = $_POST['edit_birth_date'];
        $sex = $_POST['edit_sex'];
        $update = $userObj->updateUser($user_id, $first_name, $middle_name, $last_name, $birth_date, $sex);
        if ($update) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "
            <script>
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to update user.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                    }
                });
            </script>";
        }
    }
    if (isset($_POST['firstname']) && isset($_POST['middlename']) && isset($_POST['lastname']) && isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['dob']) && isset($_POST['sex']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $first_name = htmlspecialchars(trim($_POST['firstname']));
        $middle_name = htmlspecialchars(trim($_POST['middlename']));
        $last_name = htmlspecialchars(trim($_POST['lastname']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $dob = htmlspecialchars(trim($_POST['dob']));
        $sex = htmlspecialchars(trim($_POST['sex']));
        $password = htmlspecialchars(trim($_POST['password']));
        $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));
        if (!preg_match("/^[a-zA-Z-' ]*$/", $first_name)) {
            $first_name_err = "Only letters and white space allowed";
        }
        if (!preg_match("/^[a-zA-Z-' ]*$/", $last_name)) {
            $last_name_err = "Only letters and white space allowed";
        }
        if ($password !== $confirm_password) {
            $password_err = 'Passwords do not match';
        } else if (strlen($password) < 8) {
            $password_err = 'Password must be at least 8 characters';
        }
        if ($first_name_err == '' && $middle_name_err == '' && $last_name_err == '' && $phone_err == '' && $email_err == '' && $dob_err == '' && $sex_err == '' && $password_err == '' && $confirm_password_err == '') {
            $userObj->first_name = $first_name;
            $userObj->middle_name = $middle_name;
            $userObj->last_name = $last_name;
            $userObj->phone = $phone;
            $userObj->email = $email;
            $userObj->birth_date = $dob;
            $userObj->sex = $sex;
            $userObj->password = $password;
            $add = $userObj->addUser();
            if ($add == 'success') {
                $userObj->email = $email;
                $userObj->password = $password;
                $user = $userObj->checkUser();
                if ($user == true) {
                    $verification = $verificationObj->sendVerificationEmail($user['id'], $user['email'], $user['first_name']);
                    if ($verification) {
                        echo "
                        <script>
                            Swal.fire({
                                title: 'Email Verification Sent',
                                text: 'A verification link has been sent to your email: $email',
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        </script>";
                    } else {
                        echo "ERROR: " . $verification;
                        echo "
                        <script>
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to send verification email.',
                                icon: 'error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        </script>";
                    }
                } else {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to retrieve user information.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
            } else if ($add == 'email') {
                echo "
                <script>
                    Swal.fire({
                        title: 'Email Already Taken',
                        text: 'The email address is already registered.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                </script>";
            } else if ($add == 'phone') {
                echo "
                <script>
                    Swal.fire({
                        title: 'Phone Number Already Taken',
                        text: 'The phone number is already registered.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                </script>";
            } else {
                echo "
                <script>
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to add user.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
        }
    }
}
if (isset($_POST['report_update'])) {
    $report_id = $_POST['report_id'];
    $action = $_POST['action'];
    $newStatus = ($action == 'resolve') ? 'Resolved' : 'Rejected';
    $adminObj->updateReportStatus($report_id, $newStatus);
    header("Location: " . $_SERVER['PHP_SELF'] . "#reports");
    exit();
}

// ADD CATEGORY
if (isset($_POST['add_category'])) {
    $imgUrl = !empty($_FILES['cat_image']['name'])
        ? 'uploads/categories/'.basename($_FILES['cat_image']['name'])
        : null;
    if ($imgUrl) move_uploaded_file($_FILES['cat_image']['tmp_name'], __DIR__.$imgUrl);
    $adminObj->addCategory($_POST['cat_name'], $imgUrl);
    header('Location: '.$_SERVER['PHP_SELF'].'#categories');
    exit();
}

// EDIT CATEGORY
if (isset($_POST['edit_category'])) {
    $id     = $_POST['edit_cat_id'];
    $name   = $_POST['edit_cat_name'];
    $imgUrl = $_POST['current_cat_image'];

    if (isset($_FILES['edit_cat_image_file'])
        && $_FILES['edit_cat_image_file']['error'] === UPLOAD_ERR_OK
    ) {
        $tmp  = $_FILES['edit_cat_image_file']['tmp_name'];
        $name = basename($_FILES['edit_cat_image_file']['name']);
        $dest = 'uploads/categories/' . $name;

        if (move_uploaded_file($tmp, __DIR__ . '/' . $dest)) {
            $imgUrl = $dest;
        }
    }

    $adminObj->updateCategory($_POST['edit_cat_id'], $_POST['edit_cat_name'], $imgUrl);

    header('Location: '.$_SERVER['PHP_SELF'].'#categories');
    exit();
}

// DELETE CATEGORY
if (isset($_POST['delete_category'])) {
    $adminObj->deleteCategory($_POST['delete_cat_id']);
    header('Location: '.$_SERVER['PHP_SELF'].'#categories');
    exit();
}
// for category search
$searchCategory = isset($_GET['search_category'])
    ? trim($_GET['search_category'])
    : '';

?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    .nav-main{ padding: 20px 120px; }
    .salestable th{ padding-top: 10px; }
    .dropdown-menu-center { left: 50% !important; transform: translateX(-50%) !important; }
    .acchead a{ text-decoration: none; color: black; margin-bottom: 8px; }
    button:disabled { background-color: #D3d3d3 !important; }
</style>
<main class="nav-main">
    <div class="nav-container d-flex gap-3 my-2 flex-wrap">
        <a href="#all" class="nav-link" data-target="all">Accounts</a>
        <a href="#applications" class="nav-link" data-target="applications">Applications</a>
        <a href="#reports" class="nav-link" data-target="reports">Reports</a>
        <a href="#categories" class="nav-link" data-target="reports">Categories</a>
    </div>

    <!-- Accounts Section -->
    <div id="all" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Manage Accounts</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
            <button class="disatc m-0 small text-nowrap" data-bs-toggle="modal" data-bs-target="#adduser">+ Add User</button>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="#" method="get" class="searchmenu rounded-2">
                <input type="text" name="search" placeholder="Search account" style="width: 230px;" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="m-0 ms-2"><i class="fas fa-search fa-lg small"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="salestable w-100 text-center border-top">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Birthday</th>
                    <th>Sex</th>
                    <th>Status</th>
                    <th>Deactivated Until</th>
                    <th>Role</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
                <tbody id="userTableBody">
                    <?php
                    $users = $adminObj->getUsers($searchTerm);
                    $getStatusRecords = $adminObj->getDeactivationRecords();

                    $statusMap = [];
                    foreach ($getStatusRecords as $record) {
                        $statusMap[$record['user_id']] = [
                            'status' => !empty($record['deactivated_until']) ? 'Deactivated' : 'Active',
                            'deactivated_until' => $record['deactivated_until']
                        ];
                    }

                    
                    if ($users) {
                        foreach ($users as $user) {
                            echo '<tr>';
                            echo '<td class="fw-normal small py-3 px-2">' . htmlspecialchars($user['first_name']) . '</td>';
                            echo '<td class="fw-normal small py-3 px-2">' . htmlspecialchars($user['last_name']) . '</td>';
                            echo '<td class="fw-normal small py-3 px-2">' . htmlspecialchars($user['email']) . '</td>';
                            echo '<td class="fw-normal small py-3 px-2">' . "+63" . htmlspecialchars($user['phone']) . '</td>';
                            echo '<td class="fw-normal small py-3 px-2">' . htmlspecialchars($user['birth_date']) . '</td>';
                            echo '<td class="fw-normal small py-3 px-2">' . htmlspecialchars($user['sex']) . '</td>';
                            $status = isset($statusMap[$user['id']]) ? $statusMap[$user['id']]['status'] : 'Active';
                            $deactivatedUntil = isset($statusMap[$user['id']]) ? $statusMap[$user['id']]['deactivated_until'] : 'N/A';

                            echo '<td class="fw-normal small py-3 px-2">' . htmlspecialchars($status) . '</td>';
                            echo '<td class="fw-normal small py-3 px-2">' . htmlspecialchars($deactivatedUntil) . '</td>';
                            echo '<td class="fw-normal small py-3 px-2"> 
                                <span class="small rounded-pill text-success border border-success p-1 fw-bold d-inline-flex align-items-center text-wrap">' . 
                                htmlspecialchars($user['role']) . 
                                '</span>
                            </td>';

                            echo '<td class="fw-normal small py-3 px-2">' . htmlspecialchars($user['created_at']) . '</td>';
                            echo '<td class="fw-normal small py-3 px-2">';
                            echo '<div class="dropdown position-relative">';
                            echo '<i class="fa-solid fa-ellipsis small rename py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>';
                            echo '<ul class="dropdown-menu dropdown-menu-center p-0" style="box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">';
                            echo '<li><a class="dropdown-item edit-user" href="#" data-bs-toggle="modal" data-bs-target="#edituser" data-user-id="' . $user['id'] . '" data-first-name="' . htmlspecialchars($user['first_name']) . '" data-middle-name="' . htmlspecialchars($user['middle_name']) . '" data-last-name="' . htmlspecialchars($user['last_name']) . '" data-email="' . htmlspecialchars($user['email']) . '" data-phone="' . htmlspecialchars($user['phone']) . '" data-birth-date="' . $user['birth_date'] . '" data-sex="' . htmlspecialchars($user['sex']) . '">Edit</a></li>';
                            echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteuser" data-user-id="' . $user['id'] . '">Delete</a></li>';
                            
                            if (isset($statusMap[$user['id']]) && $statusMap[$user['id']]['status'] == 'Deactivated') {
                                echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#activateuser" data-user-id="' . htmlspecialchars($user['id']) . '">Activate</a></li>';
                            } else {
                                echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deactivateuser" data-user-id="' . htmlspecialchars($user['id']) . '">Deactivate</a></li>';
                            }
                            
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
        </div>
        
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
        <div class="table-responsive">
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
                            echo '<div class="d-flex gap-2 justify-content-center mt-1">';
                            echo '<button class="update-app-btn bg-primary text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" data-bs-toggle="modal" data-bs-target="#updateApplicationModal" data-business-name="' . htmlspecialchars($business['business_name']) . '" data-status="' . htmlspecialchars($business['business_status']) . '" data-rejection-reason="' . htmlspecialchars($business['rejection_reason'] ?? '') . '" style="width:60px" ' . ($businessStatus == 'Pending Approval' ? 'disabled' : '') . '>Update</button>';
                            echo '<button class="delete-app-btn text-white border-0 small py-1 rounded-1" style="background-color:#dc3545; width:60px" data-id="' . htmlspecialchars($business['id']) . '" data-bs-toggle="modal" data-bs-target="#deleteApplicationModal" data-business-name="' . htmlspecialchars($business['business_name']) . '">Delete</button>';
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
        </div>
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
        <div class="table-responsive">
            <table class="salestable w-100 text-center border-top">
                <tr>
                    <th>Reported By</th>
                    <th>Reported Park</th>
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
                            $reportedParkName = htmlspecialchars($report['reported_park_name']);
                            echo '<tr>';
                            echo '<td class="fw-normal small py-3 px-4">' . $fullReporter . '</td>';
                            echo '<td class="fw-normal small py-3 px-4">' . $reportedParkName . '</td>';
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
                                echo '<div class="d-flex gap-2 justify-content-center mb-1">';
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
                                echo '</div>';
                            } else {
                                echo '<div class="d-flex gap-2 justify-content-center mb-1">';
                                echo '<input type="button" value="Resolve" class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px;" disabled>
                                    <input type="button" value="Reject" class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px;" disabled>';
                                echo '</div>';
                            }
                            echo '<div class="d-flex gap-2 justify-content-center">';
                            echo '<button class="update-report-btn bg-primary text-white border-0 small py-1 rounded-1" data-id="' . $report['id'] . '" data-reporter="' . $fullReporter . '" data-park="' . $reportedParkName . '" data-reason="' . htmlspecialchars($report['reason']) . '" data-status="' . htmlspecialchars($report['status']) . '" data-bs-toggle="modal" data-bs-target="#updateReportModal" style="width:60px"' . ($report['status'] == 'Pending' ? 'disabled' : '') . '>Update</button>';
                            echo '<button class="delete-report-btn text-white border-0 small py-1 rounded-1" style="background-color:#dc3545; width:60px"' . 
                            ' data-id="' . $report['id'] . '" data-reporter="' . $fullReporter . '" data-park="' . $reportedParkName . 
                            '" data-bs-toggle="modal" data-bs-target="#deleteReportModal">Delete</button>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" class="text-center py-5">No reports found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3"></div>
    </div>

    <!-- Categories Section -->
    <div id="categories" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Stall Categories</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
            <button class="disatc m-0 small text-nowrap" data-bs-toggle="modal" data-bs-target="#addstallcat">+ Add Category</button>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#categories" method="get" class="searchmenu rounded-2">
                <input type="text" name="search_category" placeholder="Search category" style="width: 230px;" value="<?= htmlspecialchars($searchCategory) ?>">
                <button type="submit" class="m-0 ms-2"><i class="fas fa-search fa-lg small"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="salestable w-100 text-center border-top">
                <tr>
                    <th>Category Image</th>
                    <th>Category Name</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
                <tbody id="categoriesTableBody">
                <?php
                $cats = $adminObj->getCategories($searchCategory);
                if ($cats) {
                    foreach ($cats as $cat) {
                        echo '<tr>';
                        echo '  <td><img src="'.htmlspecialchars($cat['image_url']).'" height="60" width="60" class="rounded-2"></td>';
                        echo '  <td class="fw-normal small">'.htmlspecialchars($cat['name']).'</td>';
                        echo '  <td class="fw-normal small">'.htmlspecialchars($cat['created_at']).'</td>';
                        echo '  <td class="fw-normal small">';
                        echo '    <div class="d-flex gap-2 justify-content-center">';
                        echo '      <button class="btn btn-sm btn-warning edit-category-btn"
                                        data-id="'. $cat['id'] . '"
                                        data-name="'. htmlspecialchars($cat['name'], ENT_QUOTES) . '"
                                        data-image="'. htmlspecialchars($cat['image_url'], ENT_QUOTES) . '"
                                        data-bs-toggle="modal" data-bs-target="#editCategory">
                                    Update
                                </button>';
                        echo '      <button class="btn btn-sm btn-danger delete-category-btn"
                                        data-id="'. $cat['id'] . '"
                                        data-bs-toggle="modal" data-bs-target="#deleteCategory">
                                    Delete
                                </button>';
                        echo '    </div>';
                        echo '  </td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center py-5">No categories found</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3"></div>
    </div>

    <!-- Approval Confirmation Modal (for Approve action) -->
    <div class="modal fade" id="approvalConfirmModal" tabindex="-1" aria-labelledby="approvalConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalConfirmModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to <span id="approvalActionText"></span> this application?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmApproval">Yes, Proceed</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal (for Deny action) -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" role="dialog" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectReasonModalLabel">Select Rejection Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Select the eligibility criteria that were not met:</p>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="reasonName">
                <label class="form-check-label" for="reasonName">Name</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="reasonEmail">
                <label class="form-check-label" for="reasonEmail">Email</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="reasonPhone">
                <label class="form-check-label" for="reasonPhone">Phone</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="reasonLogo">
                <label class="form-check-label" for="reasonLogo">Logo</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="reasonHours">
                <label class="form-check-label" for="reasonHours">Operating Hours</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="reasonBarangay">
                <label class="form-check-label" for="reasonBarangay">Barangay</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="reasonStreet">
                <label class="form-check-label" for="reasonStreet">Street, Building, House</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="reasonPermit">
                <label class="form-check-label" for="reasonPermit">Permit</label>
                </div>
                <div class="form-group mt-3">
                    <label for="customRejectionReason">Additional Rejection Details:</label>
                    <textarea class="form-control" id="customRejectionReason" rows="3" placeholder="Please provide additional details about the rejection reason"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveRejection">Save changes</button>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adduser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header p-0 border-0 m-0">
                    <h5 class="m-0">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 m-0">
                    <form action="#" class="form w-100 mt-3" method="POST">
                        <div class="progressbar">
                            <div class="progress" id="progress"></div>
                            <div class="progress-step progress-step-active" data-title="Name"></div>
                            <div class="progress-step" data-title="Contact"></div>
                            <div class="progress-step" data-title="Other"></div>
                            <div class="progress-step" data-title="Password"></div>
                        </div>
                        <div class="form-step form-step-active">
                            <div class="input-group">
                                <label for="firstname">First Name</label>
                                <input type="text" name="firstname" id="firstname" placeholder="Enter your first name" value="<?= $first_name ?>" required/>
                                <span class="text-danger small"><?= $first_name_err ?></span>
                            </div>
                            <div class="input-group">
                                <label for="middlename">Middle Name (Optional)</label>
                                <input type="text" name="middlename" id="middlename" placeholder="Enter your middle name" value="<?= $middle_name ?>"/>
                                <span class="text-danger small"><?= $middle_name_err ?></span>
                            </div>
                            <div class="input-group">
                                <label for="lastname">Last Name</label>
                                <input type="text" name="lastname" id="lastname" placeholder="Enter your last name" value="<?= $last_name ?>" required/>
                                <span class="text-danger small"><?= $last_name_err ?></span>
                            </div>
                            <div class="btns-group d-block text-center">
                                <input type="button" value="Next" class="button btn-next">
                            </div>
                        </div>
                        <div class="form-step">
                            <div class="form-group">
                                <label for="phone" class="mb-2">Phone Number</label>
                                <div class="input-group mt-0">
                                    <span class="input-group-text">+63</span>
                                    <input type="tel" name="phone" id="phone" class="form-control phone-input" value="<?= $phone ?>" maxlength="10" placeholder="Enter your phone number" required>
                                    <span class="text-danger small"><?= $phone_err ?></span>
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" placeholder="Enter your email" value="<?= $email ?>" required/>
                                <span class="text-danger small"><?= $email_err ?></span>
                            </div>
                            <div class="btns-group">
                                <a href="#" class="button btn-prev">Previous</a>
                                <a href="#" class="button btn-next">Next</a>
                            </div>
                        </div>
                        <div class="form-step">
                            <div class="input-group">
                                <label for="dob">Date of Birth</label>
                                <input type="date" name="dob" id="dob" value="<?= $dob ?>" required/>
                                <span class="text-danger small"><?= $dob_err ?></span>
                            </div>
                            <div class="input-group">
                                <label for="sex">Sex</label>
                                <select name="sex" id="sex" required style="padding: 12px 0.75rem">
                                    <option value="" disabled <?php echo empty($sex) ? "selected" : ""; ?>>Select your sex</option>
                                    <option value="male" <?php echo ($sex == 'male') ? "selected" : ""; ?>>Male</option>
                                    <option value="female" <?php echo ($sex == 'female') ? "selected" : ""; ?>>Female</option>
                                </select>
                                <span class="text-danger small"><?= $sex_err ?></span>
                            </div>
                            <div class="btns-group">
                                <a href="#" class="button btn-prev">Previous</a>
                                <a href="#" class="button btn-next">Next</a>
                            </div>
                        </div>
                        <div class="form-step">
                            <div class="input-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" placeholder="Enter your password" required/>
                                <span class="text-danger small"><?= $password_err ?></span>
                            </div>
                            <div class="input-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required/>
                                <span class="text-danger small"><?= $confirm_password_err ?></span>
                            </div>
                            <div class="btns-group">
                                <a href="#" class="button btn-prev">Previous</a>
                                <input type="submit" value="Add" class="button"/>
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edituser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-2">
                <form action="" method="POST" id="editUserForm">
                    <input type="hidden" name="edit_user_id" id="editUserId" value="">
                    <div class="modal-body">
                        <div class="modal-header p-0 border-0 mb-4">
                            <h4 class="m-0">Edit User</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="mb-2">
                            <label for="editFirstName" class="form-label">First Name</label>
                            <input type="text" name="edit_first_name" id="editFirstName" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label for="editMiddleName" class="form-label">Middle Name</label>
                            <input type="text" name="edit_middle_name" id="editMiddleName" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label for="editLastName" class="form-label">Last Name</label>
                            <input type="text" name="edit_last_name" id="editLastName" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" name="edit_email" id="editEmail" class="form-control" disabled>
                        </div>
                        <div class="mb-2">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="text" name="edit_phone" id="editPhone" class="form-control" disabled>
                        </div>
                        <div class="mb-2">
                            <label for="editBirthDate" class="form-label">Birth Date</label>
                            <input type="date" name="edit_birth_date" id="editBirthDate" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label for="editSex" class="form-label">Sex</label>
                            <select name="edit_sex" id="editSex" class="form-control" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <button type="submit" name="update_user" class="btn btn-primary w-100">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteuser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="text-center">
                            <h4 class="fw-bold mb-4">Delete User</h4>
                            <span>You are about to delete this user.<br>Are you sure?</span><br><br>
                            <strong>This action cannot be undone.</strong>
                            <input type="hidden" name="user_id" id="modalUserId" value="">
                            <div class="mt-5 mb-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_user" class="btn btn-primary">Delete</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="activitylog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-custom-width">
            <div class="modal-content">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <h1 class="modal-title fs-5 fw-bold" id="staticBackdropLabel">Activity Log</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-scrollable pt-0">
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivate User -->
    <div class="modal fade" id="deactivateuser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deactivateUserForm" action="" method="POST">
                    <input type="hidden" name="deactivate_user_id" id="deactivateUserId" value="">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="modal-title m-0 fw-bold">Select Duration of Deactivation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deactivation_duration" id="3days" value="3days">
                            <label class="form-check-label" for="3days">3 Days</label>
                        </div><br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deactivation_duration" id="7days" value="7days">
                            <label class="form-check-label" for="7days">7 Days</label>
                        </div><br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deactivation_duration" id="1month" value="1month">
                            <label class="form-check-label" for="1month">1 Month</label>
                        </div><br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deactivation_duration" id="forever" value="forever">
                            <label class="form-check-label" for="forever">Forever</label>
                        </div><br>
                        <div class="mb-3">
                            <label for="deactivation_reason" class="form-label">Reason for Deactivation</label>
                            <textarea class="form-control" name="deactivation_reason" id="deactivation_reason" rows="3" required></textarea>
                        </div>
                        <div class="text-center mt-4">
                            <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Close</button>
                            <button type="button" id="submitDeactivation" class="btn btn-primary">Deactivate</button> 
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Activate User -->
    <div class="modal fade" id="activateuser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-bold mb-4"><i class="fa-solid fa-check"></i> Activate User</h4>
                        <span>You are about to activate this user.<br>Are you sure?</span>
                        <div class="mt-5 mb-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary">Activate</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- More Park Info -->
    <div class="modal fade" id="moreparkinfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold m-0">More Info</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <h5 class="fw-bold mb-3">Business Contact</h5>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Business Email</span>
                            <span data-email></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Business Phone Number</span>
                            <span data-phone class="text-muted"></span>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3">Business Logo</h5>
                    <div class="mb-4">
                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                        <a data-logo href="#" target="_blank"></a>
                    </div>

                    <h5 class="fw-bold mb-3">Operating Hours</h5>
                    <div class="mb-4" data-hours>
                        <!-- Dynamically added operating hours -->
                    </div>

                    <h5 class="fw-bold mb-3">Business Permit</h5>
                    <div class="mb-4">
                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                        <a data-permit href="#" target="_blank"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addstallcat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" enctype="multipart/form-data" class="modal-content p-3">
        <div class="modal-header border-0">
            <h5 class="modal-title">Add Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="cat_name" class="form-control" required>
            </div>
            <div class="mb-3">
            <label class="form-label">Image</label>
            <input type="file" name="cat_image" class="form-control">
            </div>
        </div>
        <div class="modal-footer border-0">
            <button type="submit" name="add_category" class="btn btn-primary w-100">Add</button>
        </div>
        </form>
    </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategory" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" enctype="multipart/form-data" class="modal-content p-3">
        <input type="hidden" name="edit_cat_id" id="editCatId">
        <input type="hidden" name="current_cat_image" id="currentCatImage">
        <div class="modal-header border-0">
            <h5 class="modal-title">Edit Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="edit_cat_name" id="editCatName" class="form-control" required>
            </div>
            <div class="mb-3">
            <label class="form-label">Change Image</label>
            <input type="file" name="edit_cat_image_file" id="editCatImageFile" class="form-control">
            </div>
            <small class="text-muted">Leave empty to keep the existing image.</small>

        </div>
        <div class="modal-footer border-0">
            <button type="submit" name="edit_category" class="btn btn-warning w-100">Update</button>
        </div>
        </form>
    </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategory" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content p-3">
        <input type="hidden" name="delete_cat_id" id="deleteCatId">
        <div class="modal-header border-0">
            <h5 class="modal-title text-danger">Delete Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
            <p>Are you sure you want to delete this category?</p>
        </div>
        <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="delete_category" class="btn btn-danger">Delete</button>
        </div>
        </form>
    </div>
    </div>


    <script>
        $('#moreparkinfo').on('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const email = button.getAttribute('data-email');
            const phone = button.getAttribute('data-phone');
            const hours = button.getAttribute('data-hours');
            const permit = button.getAttribute('data-permit');
            const logo = button.getAttribute('data-logo');
            $(this).find('.modal-body span[data-email]').text(email || 'N/A');
            $(this).find('.modal-body span[data-phone]').text(phone || 'N/A');
            const hoursContainer = $(this).find('.modal-body div[data-hours]');
            hoursContainer.html(hours ? hours.split('; ').map(hour => `<p>${hour}</p>`).join('') : '<p>No operating hours available</p>');
            const permitLink = $(this).find('.modal-body a[data-permit]');
            if (permit) {
                permitLink.text(permit.split('/').pop());
                permitLink.attr('href', permit).attr('target', '_blank');
            } else {
                permitLink.text('No permit file').removeAttr('href').removeAttr('target');
            }
            const logoLink = $(this).find('.modal-body a[data-logo]');
            if (logo) {
                logoLink.text(logo.split('/').pop());
                logoLink.attr('href', logo).attr('target', '_blank');
            } else {
                logoLink.text('No logo file').removeAttr('href').removeAttr('target');
            }
        });
        
    </script>

    <script>
    $(document).ready(function () {
        $('.activity-log').on('click', function () {
            var userId = $(this).data('user-id');
            $('#activitylog .modal-body').html('<p class="text-center py-5">Loading...</p>');
            $.ajax({
                url: 'getActivityLog.php',
                type: 'GET',
                data: { user_id: userId },
                success: function (data) {
                    $('#activitylog .modal-body').html(data);
                },
                error: function () {
                    $('#activitylog .modal-body').html('<p class="text-center py-5">Error loading activity.</p>');
                }
            });
        });
    });
    $('#edituser').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var firstName = button.data('first-name');
        var middleName = button.data('middle-name');
        var lastName = button.data('last-name');
        var email = button.data('email');
        var phone = button.data('phone');
        var birthDate = button.data('birth-date');
        var sex = button.data('sex');
        $('#editUserId').val(userId);
        $('#editFirstName').val(firstName);
        $('#editMiddleName').val(middleName);
        $('#editLastName').val(lastName);
        $('#editEmail').val(email);
        $('#editPhone').val(phone);
        $('#editBirthDate').val(birthDate);
        $('#editSex').val(sex);
    });
    $('#deleteuser').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        $('#modalUserId').val(userId);
    });

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

    $('#deactivateuser').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        $('#deactivateUserId').val(userId);
    });

  
$('.edit-category-btn').on('click', function() {
  $('#editCatId').val($(this).data('id'));
  $('#editCatName').val($(this).data('name'));
  $('#currentCatImage').val($(this).data('image'));
});

$('.delete-category-btn').on('click', function() {
  $('#deleteCatId').val($(this).data('id'));
});

    </script>
    <script src="assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/adminresponse.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/pagination.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/navigation.js?v=<?php echo time(); ?>"></script>

    <!-- ACTIVATE AND DEACTIVATE USER -->
    <script src="assets/js/activate.js?v=<?php echo time(); ?>"></script>
    
    <!-- Update Application Modal -->
    <div class="modal fade" id="updateApplicationModal" tabindex="-1" aria-labelledby="updateApplicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header p-0 border-0 m-0">
                    <h5 class="m-0">Update Application Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 m-0">
                    <form id="updateApplicationForm" method="POST" action="update_application.php">
                        <input type="hidden" id="updateAppId" name="application_id">
                        <div class="input-group">
                            <label for="updateBusinessName">Business Name</label>
                            <input type="text" id="updateBusinessName" class="form-control" readonly>
                        </div>
                        <div class="input-group">
                            <label for="updateAppStatus">Status</label>
                            <select name="status" id="updateAppStatus" class="form-control" required>
                                <option value="Pending Approval">Pending Approval</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div id="rejectionReasonGroup">
                            <div class="input-group mb-2">
                                <label>Rejection Reasons</label>
                            </div>
                            <div class="form-check ms-3 mb-2">
                                <input class="form-check-input" type="checkbox" value="Name" id="updateReasonName" name="rejection_reasons[]">
                                <label class="form-check-label" for="updateReasonName">Name</label>
                            </div>
                            <div class="form-check ms-3 mb-2">
                                <input class="form-check-input" type="checkbox" value="Email" id="updateReasonEmail" name="rejection_reasons[]">
                                <label class="form-check-label" for="updateReasonEmail">Email</label>
                            </div>
                            <div class="form-check ms-3 mb-2">
                                <input class="form-check-input" type="checkbox" value="Phone" id="updateReasonPhone" name="rejection_reasons[]">
                                <label class="form-check-label" for="updateReasonPhone">Phone</label>
                            </div>
                            <div class="form-check ms-3 mb-2">
                                <input class="form-check-input" type="checkbox" value="Logo" id="updateReasonLogo" name="rejection_reasons[]">
                                <label class="form-check-label" for="updateReasonLogo">Logo</label>
                            </div>
                            <div class="form-check ms-3 mb-2">
                                <input class="form-check-input" type="checkbox" value="Operating Hours" id="updateReasonHours" name="rejection_reasons[]">
                                <label class="form-check-label" for="updateReasonHours">Operating Hours</label>
                            </div>
                            <div class="form-check ms-3 mb-2">
                                <input class="form-check-input" type="checkbox" value="Barangay" id="updateReasonBarangay" name="rejection_reasons[]">
                                <label class="form-check-label" for="updateReasonBarangay">Barangay</label>
                            </div>
                            <div class="form-check ms-3 mb-2">
                                <input class="form-check-input" type="checkbox" value="Street, Building, House" id="updateReasonStreet" name="rejection_reasons[]">
                                <label class="form-check-label" for="updateReasonStreet">Street, Building, House</label>
                            </div>
                            <div class="form-check ms-3 mb-2">
                                <input class="form-check-input" type="checkbox" value="Permit" id="updateReasonPermit" name="rejection_reasons[]">
                                <label class="form-check-label" for="updateReasonPermit">Permit</label>
                            </div>
                            <div class="input-group mt-3">
                                <label for="updateRejectionReason">Other Rejection Reason</label>
                                <textarea name="rejection_reason" id="updateRejectionReason" class="form-control" rows="3" placeholder="Please provide additional details about the rejection reason"></textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Application Modal -->
    <div class="modal fade" id="deleteApplicationModal" tabindex="-1" aria-labelledby="deleteApplicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header p-0 border-0 m-0">
                    <h5 class="m-0">Delete Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 m-0">
                    <p>Are you sure you want to delete the application for <span id="deleteBusinessName"></span>?</p>
                    <form id="deleteApplicationForm" method="POST" action="delete_application.php">
                        <input type="hidden" id="deleteAppId" name="application_id">
                        <div class="d-flex gap-2 justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Report Modal -->
    <div class="modal fade" id="updateReportModal" tabindex="-1" aria-labelledby="updateReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header p-0 border-0 m-0">
                    <h5 class="m-0">Update Report Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 m-0">
                    <form id="updateReportForm" method="POST" action="update_report.php">
                        <input type="hidden" id="updateReportId" name="report_id">
                        <div class="input-group">
                            <label for="updateReporter">Reported By</label>
                            <input type="text" id="updateReporter" class="form-control" readonly>
                        </div>
                        <div class="input-group">
                            <label for="updateReportedPark">Reported Park</label>
                            <input type="text" id="updateReportedPark" class="form-control" readonly>
                        </div>
                        <div class="input-group">
                            <label for="updateReportReason">Reason</label>
                            <textarea id="updateReportReason" class="form-control" readonly></textarea>
                        </div>
                        <div class="input-group">
                            <label for="updateReportStatus">Status</label>
                            <select name="status" id="updateReportStatus" class="form-control" required>
                                <option value="Pending">Pending</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Report Modal -->
    <div class="modal fade" id="deleteReportModal" tabindex="-1" aria-labelledby="deleteReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header p-0 border-0 m-0">
                    <h5 class="m-0">Delete Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 m-0">
                    <p>Are you sure you want to delete the report from <span id="deleteReporter"></span> about <span id="deleteReportedPark"></span>?</p>
                    <form id="deleteReportForm" method="POST" action="delete_report.php">
                        <input type="hidden" id="deleteReportId" name="report_id">
                        <div class="d-flex gap-2 justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        // Application update modal
        $('.update-app-btn').click(function() {
            var id = $(this).data('id');
            var businessName = $(this).data('business-name');
            var status = $(this).data('status');
            var rejectionReason = $(this).data('rejection-reason') || '';
            
            // Reset form
            $('#updateApplicationForm')[0].reset();
            $('input[name="rejection_reasons[]"]').prop('checked', false);
            
            // Set basic values
            $('#updateAppId').val(id);
            $('#updateBusinessName').val(businessName);
            $('#updateAppStatus').val(status);
            
            // Process existing rejection reasons
            if (rejectionReason) {
                // Check for standard rejection reasons in the string
                var reasonChecks = {
                    'Name': '#updateReasonName',
                    'Email': '#updateReasonEmail',
                    'Phone': '#updateReasonPhone',
                    'Logo': '#updateReasonLogo',
                    'Operating Hours': '#updateReasonHours',
                    'Barangay': '#updateReasonBarangay',
                    'Street, Building, House': '#updateReasonStreet',
                    'Permit': '#updateReasonPermit'
                };
                
                // Check boxes that match existing reasons
                var additionalText = rejectionReason;
                
                $.each(reasonChecks, function(reason, selector) {
                    if (rejectionReason.includes(reason)) {
                        $(selector).prop('checked', true);
                        // Remove this reason from additional text
                        additionalText = additionalText.replace(reason, '');
                    }
                });
                
                // Extract any additional details
                var additionalDetails = '';
                var match = additionalText.match(/Additional details:\s*(.+)/i);
                if (match && match[1]) {
                    additionalDetails = match[1].trim();
                }
                
                $('#updateRejectionReason').val(additionalDetails);
            } else {
                $('#updateRejectionReason').val('');
            }
            
            // Show/hide rejection reason based on status
            if (status === 'Rejected') {
                $('#rejectionReasonGroup').show();
            } else {
                $('#rejectionReasonGroup').hide();
            }
            
            // Add change handler for status dropdown
            $('#updateAppStatus').off('change').on('change', function() {
                if ($(this).val() === 'Rejected') {
                    $('#rejectionReasonGroup').show();
                } else {
                    $('#rejectionReasonGroup').hide();
                }
            });
        });
        
        // Application delete modal
        $('.delete-app-btn').click(function() {
            var id = $(this).data('id');
            var businessName = $(this).data('business-name');
            
            $('#deleteAppId').val(id);
            $('#deleteBusinessName').text(businessName);
        });
        
        // Report update modal
        $('.update-report-btn').click(function() {
            var id = $(this).data('id');
            var reporter = $(this).data('reporter');
            var park = $(this).data('park');
            var reason = $(this).data('reason');
            var status = $(this).data('status');
            
            $('#updateReportId').val(id);
            $('#updateReporter').val(reporter);
            $('#updateReportedPark').val(park);
            $('#updateReportReason').val(reason);
            $('#updateReportStatus').val(status);
        });
        
        // Report delete modal
        $('.delete-report-btn').click(function() {
            var id = $(this).data('id');
            var reporter = $(this).data('reporter');
            var park = $(this).data('park');
            
            $('#deleteReportId').val(id);
            $('#deleteReporter').text(reporter);
            $('#deleteReportedPark').text(park);
        });
    });
</script>
</script>
    <br><br><br><br>
</main>
<?php
include_once 'footer.php';
?>
