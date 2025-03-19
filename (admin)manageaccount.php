
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
$users = $adminObj->getUsers($searchTerm);
$verificationObj = new Verification();
$first_name = $last_name = $phone = $email = $dob = $sex = $password = $confirm_password = '';
$first_name_err = $last_name_err = $phone_err = $email_err = $dob_err = $sex_err = $password_err = $confirm_password_err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['edit_user_id'];
        $first_name = htmlspecialchars(trim($_POST['edit_first_name']));
        $last_name = htmlspecialchars(trim($_POST['edit_last_name']));
        $birth_date = $_POST['edit_birth_date'];
        $sex = $_POST['edit_sex'];
        $update = $userObj->updateUser($user_id, $first_name, $last_name, $birth_date, $sex);
        if ($update) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo '<script>alert("Update failed")</script>';
        }
    }
    if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['dob']) && isset($_POST['sex']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $first_name = htmlspecialchars(trim($_POST['firstname']));
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
        if ($first_name_err == '' && $last_name_err == '' && $phone_err == '' && $email_err == '' && $dob_err == '' && $sex_err == '' && $password_err == '' && $confirm_password_err == '') {
            $userObj->first_name = $first_name;
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
                        echo '<script>alert("Failed to send verification email")</script>';
                    }
                } else {
                    echo '<script>alert("Failed to sign up")</script>';
                }
            } else if ($add == 'email') {
                echo '<script>alert("Email is already taken")</script>';
            } else if ($add == 'phone') {
                echo '<script>alert("Phone number is already taken")</script>';
            } else {
                echo '<script>alert("Failed to sign up")</script>';
            }
        }
    }
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    main{ padding: 20px 120px; }
    .salestable th{ padding-top: 10px; width: 10%; }
    .dropdown-menu-center { left: 50% !important; transform: translateX(-50%) !important; }
    .acchead a{ text-decoration: none; color: black; margin-bottom: 8px; }
</style>
<main>
    <div class="nav-container d-flex gap-3 my-2">
        <a href="#all" class="nav-link" data-target="all">Accounts</a>
        <a href="#applications" class="nav-link" data-target="applications">Applications</a>
        <a href="#reports" class="nav-link" data-target="reports">Reports</a>
        <a href="#onlinepayment" class="nav-link" data-target="onlinepayment">Online Payment</a>
    </div>
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
                $users = $adminObj->getUsers();
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
                        echo '<li><a class="dropdown-item edit-user" href="#" data-bs-toggle="modal" data-bs-target="#edituser" data-user-id="' . $user['id'] . '" data-first-name="' . htmlspecialchars($user['first_name']) . '" data-last-name="' . htmlspecialchars($user['last_name']) . '" data-email="' . htmlspecialchars($user['email']) . '" data-phone="' . htmlspecialchars($user['phone']) . '" data-birth-date="' . $user['birth_date'] . '" data-sex="' . htmlspecialchars($user['sex']) . '">Edit</a></li>';
                        echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteuser" data-user-id="' . $user['id'] . '">Delete</a></li>';
                        echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deactivateuser">Deactivate</a></li>';
                        echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#activitylog">Activity</a></li>';
                        echo '<li><a class="dropdown-item" href="parkregistration.php?user_id=' . $user['id'] . '">Create Park</a></li>';
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

    <div id="applications" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Applications</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="#" method="get" class="searchmenu rounded-2">
                <input type="text" name="search" placeholder="Search account" style="width: 230px;" value="<?= htmlspecialchars($searchTerm) ?>">
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

            <?php
                $getBusinesses = $adminObj->getBusinesses();

                foreach ($getBusinesses as $business) {
                    $status = '';
                    if (htmlspecialchars($business['business_status']) == 'Pending Approval') {
                        $status = 'Pending';
                    
                        echo '<tr>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['owner_name']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['business_name']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">' . 
                            htmlspecialchars($business['region_province_city']) . ', ' . 
                            htmlspecialchars($business['barangay']) . ', ' . 
                            htmlspecialchars($business['street_building_house']) . 
                            '</td>';
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
                        echo '<td class="fw-normal small py-3 px-4"><span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">' . $status . '</span></td>';
                        echo '<td class="fw-normal small py-3 px-4">';
                        echo '<div class="d-flex gap-2 justify-content-center">';
                        echo '<button class="approve-btn bg-success text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px">Approve</button>';
                        echo '<button class="deny-btn bg-danger text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px">Deny</button>';
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }
            ?>
           
        </table>
        <!-- Approve/Deny Confirmation Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to <span id="actionText"></span> this application?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmAction">Yes, Proceed</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3">
            <!-- Pagination will be dynamically generated -->
        </div>
    </div>

    <div id="reports" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Report</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="#" method="get" class="searchmenu rounded-2">
                <input type="text" name="search" placeholder="Search account" style="width: 230px;" value="<?= htmlspecialchars($searchTerm) ?>">
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

            <tr>
                <td class="fw-normal small py-3 px-4">Athena Casino</td>
                <td class="fw-normal small py-3 px-4">Athena Casino</td>
                <td class="fw-normal small py-3 px-4">Self Report lang</td>
                <td class="fw-normal small py-3 px-4">07/29/2024</td>
                <td class="fw-normal small py-3 px-4"><span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">Pending</span></td>
                <td class="fw-normal small py-3 px-4">
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="bg-success text-white border-0 small py-1 rounded-1" style="width:60px">Resolve</button>
                        <button class="bg-danger text-white border-0 small py-1 rounded-1" style="width:60px">Reject</button>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fw-normal small py-3 px-4">Athena Casino</td>
                <td class="fw-normal small py-3 px-4">Athena Casino</td>
                <td class="fw-normal small py-3 px-4">Self Report lang</td>
                <td class="fw-normal small py-3 px-4">07/29/2024</td>
                <td class="fw-normal small py-3 px-4"><span class="small rounded-5 text-danger border border-danger p-1 border-2 fw-bold">Rejected</span></td>
                <td class="fw-normal small py-3 px-4">
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px">Resolve</button>
                        <button class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px">Reject</button>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fw-normal small py-3 px-4">Athena Casino</td>
                <td class="fw-normal small py-3 px-4">Athena Casino</td>
                <td class="fw-normal small py-3 px-4">Self Report lang</td>
                <td class="fw-normal small py-3 px-4">07/29/2024</td>
                <td class="fw-normal small py-3 px-4"><span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">Resolved</span></td>
                <td class="fw-normal small py-3 px-4">
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px">Resolve</button>
                        <button class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px">Reject</button>
                    </div>
                </td>
            </tr>
           
        </table>
        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3">
            <!-- Pagination will be dynamically generated -->
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

    <script>
        const modal = document.getElementById('moreparkinfo');

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            // Get data attributes
            const email = button.getAttribute('data-email');
            const phone = button.getAttribute('data-phone');
            const hours = button.getAttribute('data-hours');
            const permit = button.getAttribute('data-permit'); // Permit file path
            const logo = button.getAttribute('data-logo'); // Logo file path

            // Populate modal fields
            modal.querySelector('.modal-body span[data-email]').textContent = email || 'N/A';
            modal.querySelector('.modal-body span[data-phone]').textContent = phone || 'N/A';

            // Populate operating hours
            const hoursContainer = modal.querySelector('.modal-body div[data-hours]');
            hoursContainer.innerHTML = hours 
                ? hours.split('; ').map(hour => `<p>${hour}</p>`).join('') 
                : '<p>No operating hours available</p>';

            // Populate permit link
            const permitLink = modal.querySelector('.modal-body a[data-permit]');
            if (permit) {
                permitLink.textContent = permit.split('/').pop(); // Extract filename
                permitLink.href = permit; // Set file path
                permitLink.target = '_blank'; // Open in new tab
            } else {
                permitLink.textContent = 'No permit file';
                permitLink.removeAttribute('href');
                permitLink.removeAttribute('target');
            }

            // Populate business logo link
            const logoLink = modal.querySelector('.modal-body a[data-logo]');
            if (logo) {
                logoLink.textContent = logo.split('/').pop(); // Extract filename
                logoLink.href = logo; // Set file path
                logoLink.target = '_blank'; // Open in new tab
            } else {
                logoLink.textContent = 'No logo file';
                logoLink.removeAttribute('href');
                logoLink.removeAttribute('target');
            }
        });
    </script>

    <script>
    $('#edituser').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var firstName = button.data('first-name');
        var lastName = button.data('last-name');
        var email = button.data('email');
        var phone = button.data('phone');
        var birthDate = button.data('birth-date');
        var sex = button.data('sex');
        $('#editUserId').val(userId);
        $('#editFirstName').val(firstName);
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
