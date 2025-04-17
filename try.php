
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

        if (move_uploaded_file($tmp, __DIR__ . $dest)) {
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
        <form method="POST" action="" class="modal-content p-3">
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
  
$('.edit-category-btn').on('click', function() {
  $('#editCatId').val($(this).data('id'));
  $('#editCatName').val($(this).data('name'));
  $('#editCatImage').val($(this).data('image'));
  $('#currentCatImage').val($(this).data('image'));
});

$('.delete-category-btn').on('click', function() {
  $('#deleteCatId').val($(this).data('id'));
});

    </script>

</main>
<?php
include_once 'footer.php';
?>
