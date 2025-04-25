
<?php
include_once 'landingheader.php';
include_once 'links.php';
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/user.class.php';
require_once __DIR__ . '/classes/encdec.class.php';
require_once './email/verification_token.class.php';

$userObj = new User();
$adminObj = new Admin();
if (isset($_SESSION['user'])) {
    if ($userObj->isVerified($_SESSION['user']['id']) != 1) {
        header('Location: email/verify_email.php');
        exit();
    }

    if ($user['role'] != 'Admin') {
        header('Location: index.php');
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
        $role = htmlspecialchars(trim($_POST['edit_role']));
        $update = $userObj->updateUser($user_id, $first_name, $middle_name, $last_name, $birth_date, $sex, $role);
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
        <a href="#all" class="nav-link" data-target="all">Dashboard</a>
        <a href="#accounts" class="nav-link" data-target="accounts">Accounts</a>
        <a href="#applications" class="nav-link" data-target="applications">Applications</a>
        <a href="#reports" class="nav-link" data-target="reports">Reports</a>
        <a href="#categories" class="nav-link" data-target="reports">Categories</a>
    </div>

    <!-- Dashboard Section -->
    <div id="all" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div>
            <h5 class="fw-bold mb-2">Dashboard</h5>
            <span class="small"><?= $currentDateTime ?></span>
        </div>
        
        <div class="d-flex align-items-center gap-3 mt-3">
            <div class="p-3 rounded-2 border bg-white w-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="m-0">Total Accounts</p>
                        <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                    </div>
                    <i class="fa-solid fa-user dashicon fs-5"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h2 class="fw-bold m-0">10</h2>
                    <div class="d-flex align-items-center small text-danger gap-1">
                        <i class="fa-solid fa-arrow-down"></i>
                        <span class="text-danger">11%</span>
                    </div>
                </div>
            </div>
            <div class="p-3 rounded-2 border bg-white w-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="m-0">Total Parks</p>
                        <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                    </div>
                    <i class="fa-solid fa-parachute-box dashicon fs-5"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h2 class="fw-bold m-0">20</h2>
                    <div class="d-flex align-items-center small text-success gap-1">
                        <i class="fa-solid fa-arrow-up"></i>
                        <span class="text-success">11%</span>
                    </div>
                </div>
            </div>
            <div class="p-3 rounded-2 border bg-white w-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="m-0">Total Stalls</p>
                        <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                    </div>
                    <i class="fa-solid fa-store dashicon fs-5"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h2 class="fw-bold m-0">20</h2>
                    <div class="d-flex align-items-center small text-success gap-1">
                        <i class="fa-solid fa-arrow-up"></i>
                        <span class="text-success">11%</span>
                    </div>
                </div>
            </div>
            <div class="p-3 rounded-2 border bg-white w-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="m-0">Total Products</p>
                        <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                    </div>
                    <i class="fa-solid fa-burger dashicon fs-5"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h2 class="fw-bold m-0">10</h2>
                    <div class="d-flex align-items-center small text-success gap-1">
                        <i class="fa-solid fa-arrow-up"></i>
                        <span class="text-success">11%</span>
                    </div>
                </div>
            </div>
            <div class="p-3 rounded-2 border bg-white w-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="m-0">Total Orders</p>
                        <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                    </div>
                    <i class="fa-solid fa-utensils dashicon fs-5"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h2 class="fw-bold m-0">10</h2>
                    <div class="d-flex align-items-center small text-success gap-1">
                        <i class="fa-solid fa-arrow-up"></i>
                        <span class="text-success">11%</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <script src="assets/js/pagination.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/navigation.js?v=<?php echo time(); ?>"></script>
    <br><br><br><br>

</main>

<?php
include_once 'footer.php';
?>


 <div class="d-flex gap-3 my-3">
            <div class="w-75">
                <div class="table-responsive py-2 px-3 border rounded-2 mb-3">
                    <h6 class="mb-3">Pending Applications <span class="fw-bold">(10)</span></h6>
                    <table class="salestable w-100 text-center border-top">
                        <tr>
                            <th class="text-truncate">Owner</th>
                            <th class="text-truncate">Business Name</th>
                            <th class="text-truncate">Location</th>
                            <th class="text-truncate">Other Info</th>
                            <th class="text-truncate">Date Applied</th>
                            <th class="text-truncate">Status</th>
                            <th class="text-truncate">Action</th>
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
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . htmlspecialchars($business['owner_name']) . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . $business['business_name'] . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . htmlspecialchars($business['region_province_city']) . ', ' . htmlspecialchars($business['barangay']) . ', ' . htmlspecialchars($business['street_building_house']) . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">
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
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . htmlspecialchars($business['created_at']) . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 status-cell">' . $statusDisplay . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">';
                                    echo '<div class="d-flex gap-2 justify-content-center">';
                                    echo '<button class="approve-btn bg-success text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px">Approve</button>';
                                    echo '<button class="deny-btn bg-danger text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px">Deny</button>';
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
                <div class="table-responsive py-2 px-3 border rounded-2">
                    <h6 class="mb-3">Pending Reports <span class="fw-bold">(10)</span></h6>
                    <table class="salestable w-100 text-center border-top">
                        <tr>
                            <th class="text-truncate">Reported By</th>
                            <th class="text-truncate">Reported Park</th>
                            <th class="text-truncate">Reason</th>
                            <th class="text-truncate">Date Reported</th>
                            <th class="text-truncate">Status</th>
                            <th class="text-truncate">Action</th>
                        </tr>
                        <tbody id="reportsTableBody">
                            <?php
                            $reports = $adminObj->getReports();
                            if ($reports) {
                                foreach ($reports as $report) {
                                    $fullReporter = htmlspecialchars($report['reporter_first'] . ' ' . $report['reporter_last']);
                                    $reportedParkName = htmlspecialchars($report['reported_park_name']);
                                    echo '<tr>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . $fullReporter . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . $reportedParkName . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . htmlspecialchars($report['reason']) . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . htmlspecialchars($report['created_at']) . '</td>';
                                    $status = $report['status'];
                                    if ($status == 'Pending') {
                                        $statusHTML = '<span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">Pending</span>';
                                    } elseif ($status == 'Rejected') {
                                        $statusHTML = '<span class="small rounded-5 text-danger border border-danger p-1 border-2 fw-bold">Rejected</span>';
                                    } elseif ($status == 'Resolved') {
                                        $statusHTML = '<span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">Resolved</span>';
                                    }
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">' . $statusHTML . '</td>';
                                    echo '<td class="fw-normal small py-3 px-4 text-truncate">';
                                    echo '<div class="d-flex gap-2 justify-content-center">';
                                    echo '<button class="approve-btn bg-success text-white border-0 small py-1 rounded-1" style="width:60px">Resolve</button>';
                                    echo '<button class="deny-btn bg-danger text-white border-0 small py-1 rounded-1" style="width:60px">Reject</button>';
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
            </div>
            <div class="w-25 py-2 px-3 border rounded-2">
                <h6 class="mb-3">User Activity</h6>
                <div class="d-flex gap-2 border-bottom py-2">
                    <img src="assets/images/profile.jpg" width="35" height="35" style="border-radius: 50%">
                    <div>
                        <p class="small m-0">Naila Haliluddin searched on GitGud</p>
                        <p class="small text-muted m-0">"cheese"</p>
                        <p class="small text-muted m-0">7:43 PM</p>
                    </div>
                </div>
                <div class="d-flex gap-2 border-bottom py-2">
                    <img src="assets/images/profile.jpg" width="35" height="35" style="border-radius: 50%">
                    <div>
                        <p class="small m-0">Naila Haliluddin searched on GitGud</p>
                        <p class="small text-muted m-0">"cheese"</p>
                        <p class="small text-muted m-0">7:43 PM</p>
                    </div>
                </div>
            </div>
        </div>
