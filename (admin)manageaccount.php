<?php
session_start();
include_once 'landingheader.php';
include_once 'links.php';
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';
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
                    $_SESSION['user'] = [];
                    $_SESSION['user']['id'] = $user['id'];
                    $verification = $verificationObj->sendVerificationEmail($user['id'], $user['email'], $user['first_name']);
                    if ($verification) {
                        header('Location: ./email/verify_email.php');
                        exit();
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
    } else {
        if (empty($_POST['firstname'])) {
            $first_name_err = 'First name is required';
        }
        if (empty($_POST['lastname'])) {
            $last_name_err = 'Last name is required';
        }
        if (empty($_POST['phone'])) {
            $phone_err = 'Phone is required';
        }
        if (empty($_POST['email'])) {
            $email_err = 'Email is required';
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $email_err = 'Invalid email format';
        }
        if (empty($_POST['dob'])) {
            $dob_err = 'Date of birth is required';
        }
        if (empty($_POST['sex'])) {
            $sex_err = "Sex is required";
        }
        if (empty($_POST['password'])) {
            $password_err = 'Password is required';
        }
        if (empty($_POST['confirm_password'])) {
            $confirm_password_err = 'Confirm password is required';
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['firstname']))
        $first_name = $_GET['firstname'];
    if (isset($_GET['lastname']))
        $last_name = $_GET['lastname'];
    if (isset($_GET['phone']))
        $phone = $_GET['phone'];
    if (isset($_GET['email']))
        $email = $_GET['email'];
    if (isset($_GET['dob']))
        $dob = $_GET['dob'];
    if (isset($_GET['sex']))
        $sex = $_GET['sex'];
    if (isset($_GET['password']))
        $password = $_GET['password'];
    if (isset($_GET['confirm_password']))
        $confirm_password = $_GET['confirm_password'];
}

if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $result = $userObj->deleteUser($user_id);
    if ($result) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo '<script>alert("Failed to delete user")</script>';
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
                            echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edituser">Edit</a></li>';
                            echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteuser" data-user-id="' . $user['id'] . '">Delete</a></li>';
                            echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deactivateuser">Deactivate</a></li>';
                            echo '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#activitylog">Activity</a></li>';
                            echo '<li><a class="dropdown-item" href="parkregistration.php">Create Park</a></li>';
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
    <script>
    $('#deleteuser').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const userId = button.data('user-id');
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
