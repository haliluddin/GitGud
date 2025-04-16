<?php
include_once 'header.php';
include_once 'links.php';
include_once 'nav.php';
include_once 'modals.php';

if (!isset($_SESSION['user'])) {
    header('Location: ./signin.php');
    exit();
}

$userObj = new User();
$user = $userObj->getUser($_SESSION['user']['id']);

$firstNameErr = $lastNameErr = $phoneErr = $dobErr = $sexErr = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hasErrors = false;
    $first_name = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
    $middle_name = filter_input(INPUT_POST, 'middlename', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $birth_date = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
    $sex = filter_input(INPUT_POST, 'sex', FILTER_SANITIZE_STRING);
    $current_password = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_STRING);
    if (empty($first_name)) {
        $firstNameErr = 'First name is required.';
        $hasErrors = true;
    }
    if (empty($last_name)) {
        $lastNameErr = 'Last name is required.';
        $hasErrors = true;
    }
    if (empty($phone)) {
        $phoneErr = 'Phone is required.';
        $hasErrors = true;
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $phoneErr = 'Invalid phone format.';
        $hasErrors = true;
    }
    if (empty($birth_date)) {
        $dobErr = 'Date of birth is required.';
        $hasErrors = true;
    } else {
        $dobTimestamp = strtotime($birth_date);
        $minDOB = strtotime('-18 years');
        if ($dobTimestamp > $minDOB) {
            $dobErr = 'You must be at least 18 years old.';
            $hasErrors = true;
        }
    }
    if (empty($sex)) {
        $sexErr = 'Sex is required.';
        $hasErrors = true;
    }
    $uploadDir = 'uploads/profiles/';
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    $maxFileSize = 5 * 1024 * 1024;
    $profile_img = $user['profile_img'];
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] != UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_img']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_img']['tmp_name'];
            $fileSize = $_FILES['profile_img']['size'];
            $fileType = strtolower(pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION));
            if ($fileSize > $maxFileSize) {
                echo '<script>alert("File size exceeds 5MB limit.");</script>';
                $hasErrors = true;
            } elseif (!in_array($fileType, $allowedTypes)) {
                echo '<script>alert("Invalid file type. Only JPG and PNG are allowed.");</script>';
                $hasErrors = true;
            } else {
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $destPath = $uploadDir . $_SESSION['user']['id'] . '.' . $fileType;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $profile_img = $destPath;
                } else {
                    echo '<script>alert("Failed to move uploaded file.");</script>';
                    $hasErrors = true;
                }
            }
        }
    }
    if (!$hasErrors) {
        $userObj->first_name = $first_name;
        $userObj->middle_name = $middle_name;
        $userObj->last_name = $last_name;
        $userObj->phone = $phone;
        $userObj->birth_date = $birth_date;
        $userObj->sex = $sex;
        $userObj->profile_img = $profile_img;
        $update = $userObj->editUser($_SESSION['user']['id'], $current_password);
        if ($update === true) {
            echo '<script>alert("Account updated successfully.");</script>';
            $user = $userObj->getUser($_SESSION['user']['id']);
        } elseif ($update === 'phone') {
            $phoneErr = 'Phone update failed. The new phone may be already in use.';
        } else {
            echo '<script>alert("Failed to update account. Please check your password.");</script>';
        }
    } else {
        echo '<script>alert("Please fix the errors on the form.");</script>';
    }
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .nav-main {
        padding: 20px 120px;
    }
    .profileImage {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #ddd;
    }
    .errormessage {
        color: red;
        font-size: 0.9rem;
    }
</style>
<main class="nav-main">
    <form id="accountForm" class="bg-white rounded-2 p-5" method="POST" enctype="multipart/form-data">
        <div class="dropdown position-relative">
            <i class="fa-solid fa-gear rename text-dark fs-5" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>
            <ul class="dropdown-menu dropdown-menu-center p-0" style="box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changepassword">Change Password</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteaccount">Delete Account</a></li>
            </ul>
        </div>
        <div class="d-flex acc-tm">
            <div class="d-flex justify-content-center" style="width: 40%;">
                <div class="text-center">
                    <img src="<?= htmlspecialchars(!empty($user['profile_img']) ? $user['profile_img'] : 'default_profile_image.png'); ?>" class="profileImage" id="profilePreview">
                    <input type="file" id="profile_img" name="profile_img" style="display:none" accept=".jpg, .jpeg, .png"><br><br>
                    <label for="profile_img" class="btn btn-primary">Select Image</label><br><br>
                    <span class="text-muted">File size: maximum 5 MB<br>File extension: .JPEG, .PNG</span>
                </div>
            </div>
            <div style="width: 60%;">
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="firstname" class="m-0 text-muted" style="width: 250px;">First Name</label>
                        <div class="w-100">
                            <input type="text" name="firstname" id="firstname" placeholder="Enter your first name" value="<?= htmlspecialchars($user['first_name']); ?>" required />
                            <span class="errormessage" id="firstNameError"><?php echo $firstNameErr; ?></span>
                        </div>
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="middlename" class="m-0 text-muted" style="width: 250px;">Middle Name</label>
                        <input type="text" name="middlename" id="middlename" placeholder="Enter your middle name" value="<?= htmlspecialchars($user['middle_name']); ?>" />
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="lastname" class="m-0 text-muted" style="width: 250px;">Last Name</label>
                        <div class="w-100">
                            <input type="text" name="lastname" id="lastname" placeholder="Enter your last name" value="<?= htmlspecialchars($user['last_name']); ?>" required />
                            <span class="errormessage" id="lastNameError"><?php echo $lastNameErr; ?></span>
                        </div>
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="dob" class="m-0 text-muted" style="width: 250px;">Date of Birth</label>
                        <div class="w-100">
                            <input type="date" name="dob" id="dob" value="<?= htmlspecialchars($user['birth_date']); ?>" required max="<?= date('Y-m-d', strtotime('-18 years')) ?>" />
                            <span class="errormessage" id="dobError"><?php echo $dobErr; ?></span>
                        </div>
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="sex" class="m-0 text-muted" style="width: 250px;">Sex</label>
                        <div class="w-100">
                            <select name="sex" id="sex" required style="padding: 12px 0.75rem; flex-grow: 1;">
                                <option value="" disabled>Select your sex</option>
                                <option value="male" <?= ($user['sex'] == 'male') ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($user['sex'] == 'female') ? 'selected' : '' ?>>Female</option>
                            </select>
                            <span class="errormessage" id="sexError"><?php echo $sexErr; ?></span>
                        </div>
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="phone" class="m-0 text-muted" style="width: 250px;">Phone Number</label>
                        <div class="w-100">
                            <div class="input-group m-0" style="display: flex !important;">
                                <span class="input-group-text rounded-0">+63</span>
                                <input type="tel" name="phone" id="phone" class="form-control phone-input rounded-0" value="<?= htmlspecialchars($user['phone']); ?>" maxlength="10" placeholder="Enter your phone number" required>
                            </div>
                            <span class="errormessage" id="phoneError"><?php echo $phoneErr; ?></span>
                        </div>
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="email" class="m-0 text-muted" style="width: 250px;">Email</label>
                        <input type="email" name="email" id="email" placeholder="Enter your email" value="<?= htmlspecialchars($user['email']); ?>" required disabled/>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <button type="button" id="saveChanges" class="addpro px-5">Save</button>
        </div>
        <input type="hidden" name="current_password" id="current_password">
    </form>
    <br><br><br><br><br>
</main>
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="passwordForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Confirm Your Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body my-2">
                    <label for="modal_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="modal_password" placeholder="Enter your current password" required>
                    <span class="errormessage" id="modalPassErr"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include_once 'footer.php'; ?>
<script>
document.getElementById('profile_img').addEventListener('change', function(e) {
    const fileInput = this;
    const file = fileInput.files[0];
    if (file) {
        const allowedTypes = ['image/jpeg', 'image/png'];
        const maxSize = 5 * 1024 * 1024;
        if (!allowedTypes.includes(file.type)) {
            alert("Invalid file type. Only JPG and PNG are allowed.");
            fileInput.value = '';
            return;
        }
        if (file.size > maxSize) {
            alert("File size exceeds 5MB limit.");
            fileInput.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('profilePreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});
document.getElementById('saveChanges').addEventListener('click', function() {
    document.getElementById('firstNameError').textContent = '';
    document.getElementById('lastNameError').textContent = '';
    document.getElementById('phoneError').textContent = '';
    document.getElementById('dobError').textContent = '';
    document.getElementById('sexError').textContent = '';
    let firstName = document.getElementById('firstname').value.trim();
    let lastName = document.getElementById('lastname').value.trim();
    let phone = document.getElementById('phone').value.trim();
    let dob = document.getElementById('dob').value;
    let sex = document.getElementById('sex').value;
    let hasError = false;
    if (!firstName) {
        document.getElementById('firstNameError').textContent = 'First name is required.';
        hasError = true;
    }
    if (!lastName) {
        document.getElementById('lastNameError').textContent = 'Last name is required.';
        hasError = true;
    }
    if (!phone) {
        document.getElementById('phoneError').textContent = 'Phone is required.';
        hasError = true;
    } else if (!/^[0-9]{10}$/.test(phone)) {
        document.getElementById('phoneError').textContent = 'Invalid phone format.';
        hasError = true;
    }
    if (!dob) {
        document.getElementById('dobError').textContent = 'Date of birth is required.';
        hasError = true;
    } else {
        let selectedDate = new Date(dob);
        let today = new Date();
        let eighteenYearsAgo = new Date();
        eighteenYearsAgo.setFullYear(today.getFullYear() - 18);
        if (selectedDate > eighteenYearsAgo) {
            document.getElementById('dobError').textContent = 'You must be at least 18 years old.';
            hasError = true;
        }
    }
    if (!sex) {
        document.getElementById('sexError').textContent = 'Sex is required.';
        hasError = true;
    }
    if (!hasError) {
        new bootstrap.Modal(document.getElementById('passwordModal')).show();
    }
});
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var modalPassword = document.getElementById('modal_password').value;
    if(modalPassword.trim() === '') {
        document.getElementById('modalPassErr').textContent = "Password is required.";
    } else {
        document.getElementById('current_password').value = modalPassword;
        var passwordModalEl = document.getElementById('passwordModal');
        var modal = bootstrap.Modal.getInstance(passwordModalEl);
        modal.hide();
        document.getElementById('accountForm').submit();
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#deleteaccount').on('hidden.bs.modal', function () {
            $('#delete-account-form').trigger('reset');
        });
        
        $('#delete-account-form').on('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = $('#currentpassword').val();
            const confirmation = $('#confirmation').val();
            
            if (!currentPassword || !confirmation) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all fields'
                });
                return;
            }
            
            Swal.fire({
                title: 'Are you absolutely sure?',
                text: "This action cannot be undone. All your data will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete my account',
                cancelButtonText: 'No, keep my account'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#deleteaccount').modal('hide');
                    
                    $.ajax({
                        type: 'POST',
                        url: 'delete_account.php',
                        data: {
                            current_password: currentPassword,
                            confirmation: confirmation
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    allowOutsideClick: false
                                }).then((result) => {
                                    window.location.href = 'index.php'; 
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred: ' + error
                            });
                        }
                    });
                }
            });
        });
    });
</script>
