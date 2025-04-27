
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


    $today     = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    $entities = [
      'users'    => ['label'=>'Total Accounts','icon'=>'fa-user'],
      'business' => ['label'=>'Total Parks','icon'=>'fa-parachute-box'],
      'stalls'   => ['label'=>'Total Stalls','icon'=>'fa-store'],
      'products' => ['label'=>'Total Products','icon'=>'fa-burger'],
      'orders'   => ['label'=>'Total Orders','icon'=>'fa-utensils'],
    ];

    $stats = [];
    foreach ($entities as $table => $meta) {
        $total    = $adminObj->getTotalCount($table);
        $todayNew = $adminObj->getDailyCount($table, $today);
        $yestNew  = $adminObj->getDailyCount($table, $yesterday);

        if ($yestNew > 0) {
            $pct = round((($todayNew - $yestNew) / $yestNew) * 100);
        } else {
            $pct = $todayNew > 0 ? 100 : 0;
        }

        $trend = $pct >= 0 ? 'up' : 'down';
        $stats[$table] = compact('total','pct','trend');
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
            <?php foreach ($entities as $table => $meta): 
                    $s = $stats[$table];
            ?>
                <div class="p-3 rounded-2 border bg-white w-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                    <p class="m-0"><?= $meta['label'] ?></p>
                    <span class="small" style="color: #ccc;"><?= date('M d, Y') ?></span>
                    </div>
                    <i class="fa-solid <?= $meta['icon'] ?> dashicon fs-5"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h2 class="fw-bold m-0"><?= $s['total'] ?></h2>
                    <div class="d-flex align-items-center small <?= $s['trend']==='up' ? 'text-success' : 'text-danger' ?> gap-1">
                    <i class="fa-solid fa-arrow-<?= $s['trend'] ?>"></i>
                    <span class="<?= $s['trend']==='up' ? 'text-success' : 'text-danger' ?>">
                        <?= abs($s['pct']) ?>%
                    </span>
                    </div>
                </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex gap-3 my-3">
            <div class="w-75">
                <div class="table-responsive py-2 px-3 border rounded-2 mb-3">
                    <?php
                        $pendingApps = $adminObj->getPendingBusinesses();
                        $countPending = count($pendingApps);
                    ?>
                    <h6 class="mb-3">Pending Applications <span class="fw-bold">(<?= $countPending ?>)</span></h6>
                    <table class="salestable w-100 text-center border-top">
                        <tr>
                            <th>Owner</th>
                            <th>Business Name</th>
                            <th>Location</th>
                            <th>Other Info</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <tbody>
                        <?php if ($pendingApps): ?>
                            <?php foreach ($pendingApps as $b): ?>
                            <tr>
                                <td class="fw-normal small py-3 px-4"><?= htmlspecialchars($b['owner_name']) ?></td>
                                <td class="fw-normal small py-3 px-4"><?= htmlspecialchars($b['business_name']) ?></td>
                                <td class="fw-normal small py-3 px-4 text-truncate">
                                <?= htmlspecialchars($b['region_province_city']) ?>,
                                <?= htmlspecialchars($b['barangay']) ?>,
                                <?= htmlspecialchars($b['street_building_house']) ?>
                                </td>
                                <td class="fw-normal small py-3 px-4">
                                <i class="fa-solid fa-chevron-down rename small"
                                    data-bs-toggle="modal"
                                    data-bs-target="#moreparkinfo"
                                    data-email="<?= htmlspecialchars($b['business_email']) ?>"
                                    data-phone="<?= htmlspecialchars($b['business_phone']) ?>"
                                    data-hours="<?= htmlspecialchars($b['operating_hours']) ?>"
                                    data-permit="<?= htmlspecialchars($b['business_permit']) ?>"
                                    data-logo="<?= htmlspecialchars($b['business_logo']) ?>">
                                </i>
                                </td>
                                <td class="fw-normal small py-3 px-4"><?= htmlspecialchars($b['created_at']) ?></td>
                                <td class="fw-normal small py-3 px-4">
                                <span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">Pending</span>
                                </td>
                                <td class="fw-normal small py-3 px-4">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="approve-btn bg-success text-white border-0 small py-1 rounded-1"
                                            data-id="<?= htmlspecialchars($b['id']) ?>"
                                            style="width:60px">Approve
                                    </button>
                                    <button class="deny-btn    bg-danger  text-white border-0 small py-1 rounded-1"
                                            data-id="<?= htmlspecialchars($b['id']) ?>"
                                            style="width:60px">Deny
                                    </button>
                                </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                            <td colspan="7" class="text-center py-5">No pending applications</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive py-2 px-3 border rounded-2">
                    <?php
                        $pendingReports      = $adminObj->getPendingReports();
                        $countPendingReports = count($pendingReports);
                    ?>

                    <h6 class="mb-3">Pending Reports <span class="fw-bold">(<?= $countPendingReports ?>)</span></h6>
                    <table class="salestable w-100 text-center border-top">
                        <tr>
                            <th>Reported By</th>
                            <th>Reported Park</th>
                            <th>Reason</th>
                            <th>Date Reported</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <tbody>
                            <?php if ($pendingReports): ?>
                                <?php foreach ($pendingReports as $r): ?>
                                    <tr>
                                    <td class="fw-normal small py-3 px-4"><?= htmlspecialchars($r['reporter_first'].' '.$r['reporter_last']) ?></td>
                                    <td class="fw-normal small py-3 px-4"><?= htmlspecialchars($r['reported_park_name']) ?></td>
                                    <td class="fw-normal small py-3 px-4"><?= htmlspecialchars($r['reason']) ?></td>
                                    <td class="fw-normal small py-3 px-4"><?= htmlspecialchars($r['created_at']) ?></td>
                                    <td class="fw-normal small py-3 px-4">
                                        <span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">Pending</span>
                                    </td>
                                    <td class="fw-normal small py-3 px-4">
                                        <div class="d-flex gap-2 justify-content-center mb-1">
                                        <form method="POST" action="" style="display:inline-block; margin-right:5px;">
                                            <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                            <input type="hidden" name="action" value="resolve">
                                            <input type="submit" name="report_update" value="Resolve"
                                                class="bg-success text-white border-0 small py-1 rounded-1"
                                                style="width:60px;">
                                        </form>
                                        <form method="POST" action="" style="display:inline-block;">
                                            <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="submit" name="report_update" value="Reject"
                                                class="bg-danger text-white border-0 small py-1 rounded-1"
                                                style="width:60px;">
                                        </form>
                                        </div>
                                    </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">No pending reports</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="w-25 py-2 px-3 border rounded-2 mb-3">
                <?php
                    $recentActivities = $adminObj->getAllActivities(10);
                ?>
                <h6 class="mb-3">User Activity (last 10)</h6>
                <?php if (!empty($recentActivities)): ?>
                    <?php foreach ($recentActivities as $act): 
                    $time = date("g:i A", strtotime($act['created_at']));
                    ?>
                    <div class="d-flex gap-2 border-bottom py-2">
                        <img src="assets/images/profile.jpg" width="35" height="35" style="border-radius: 50%">
                        <div>
                        <p class="small m-0">
                            <strong><?= htmlspecialchars($act['user_fullname']) ?></strong>
                            <?= htmlspecialchars($act['message']) ?>
                        </p>
                        <p class="small text-muted m-0">"<?= htmlspecialchars($act['detail']) ?>"</p>
                        <p class="small text-muted m-0"><?= $time ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="small text-center py-2">No activity found</p>
                <?php endif; ?>
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

