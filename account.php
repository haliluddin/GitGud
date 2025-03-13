<?php
    include_once 'header.php'; 
    include_once 'links.php'; 
    include_once 'nav.php';
    include_once 'modals.php';

    if (!isset($_SESSION['user'])) {
        header('Location: ./signin.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $userObj = new User();
        $userObj->id = $_SESSION['user']['id'];
        $user = $userObj->getUser($_SESSION['user']['id']);
    
        $hasChanges = ($_POST['firstname'] != $user['first_name']) ||
                      ($_POST['lastname'] != $user['last_name']) ||
                      ($_POST['phone'] != $user['phone']) ||
                      ($_POST['sex'] != $user['sex']);
    
        if (!$hasChanges && $_FILES['profile_img']['error'] == UPLOAD_ERR_NO_FILE) {
            echo '<script>alert("No changes were made.")</script>';
        } else {
            $userObj->first_name = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
            $userObj->last_name = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
            $userObj->phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $userObj->sex = filter_input(INPUT_POST, 'sex', FILTER_SANITIZE_STRING);
            $current_password = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_STRING);
    
            $uploadDir = 'uploads/profiles/';
            $allowedTypes = ['jpg', 'jpeg', 'png'];
            $maxFileSize = 5 * 1024 * 1024;
    
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profile_img']['tmp_name'];
                $fileSize = $_FILES['profile_img']['size'];
                $fileType = strtolower(pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION));
    
                if ($fileSize > $maxFileSize) {
                    echo '<script>alert("File size exceeds 5MB limit.")</script>';
                } elseif (!in_array($fileType, $allowedTypes)) {
                    echo '<script>alert("Invalid file type. Only JPG and PNG are allowed.")</script>';
                } else {
                    $destPath = $uploadDir . $_SESSION['user']['id'] . '.' . $fileType;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $userObj->profile_img = $destPath;
                    } else {
                        $userObj->profile_img = $user['profile_img'];
                        echo '<script>alert("Failed to move uploaded file.")</script>';
                    }
                }
            } else {
                $userObj->profile_img = $user['profile_img'];
            }
            
            $add = $userObj->editUser($_SESSION['user']['id'], $current_password, $user['phone']);
            if ($add) {
                echo '<script>alert("Account updated successfully.")</script>';
            } else {
                echo '<script>alert("Failed to update account.")</script>';
            }
    
            $user = $userObj->getUser($_SESSION['user']['id']);
        }
    }
?>
<style>
    main {
        padding: 20px 120px;
    }
    #profileImage {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #ddd;
    }
</style>
<main>
    <form class="bg-white rounded-2 p-5" method="POST" enctype="multipart/form-data">
        <div class="dropdown position-relative">
            <i class="fa-solid fa-gear rename text-dark fs-5" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>
            <ul class="dropdown-menu dropdown-menu-center p-0" style="box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changepassword">Change Password</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteaccount">Delete Account</a></li>
            </ul>
        </div>
        <div class="d-flex ">
            <div class="d-flex justify-content-center" style="width: 40%;">
                <div class="text-center flex-grow-1">
                    <img id="profileImage" src="<?= $user['profile_img'] ?>" alt="Profile Image" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;"><br><br>
                    <!-- <button id="selectImageBtn" type="button" class="disatc m-0">Select Image</button> -->
                    <input name="profile_img" id="fileInput" class="" type="file" accept="image/jpeg, image/png, image/jpg">
                    <br><br>
                    <span class="text-muted">File size: maximum 5 MB<br>File extension: .JPEG, .PNG</span>
                </div>
            </div>
            
            <div  style="width: 60%;">
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="firstname" class="m-0 text-muted" style="width: 250px;">First Name</label>
                        <input type="text" name="firstname" id="firstname" placeholder="Enter your first name" value="<?= $user['first_name'] ?>" required />
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="lastname" class="m-0 text-muted" style="width: 250px;">Last Name</label>
                        <input type="text" name="lastname" id="lastname" placeholder="Enter your last name" value="<?= $user['last_name'] ?>" required />
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="current_password" class="m-0 text-muted" style="width: 250px;">Current Password</label>
                        <input type="password" name="current_password" id="current_password" placeholder="Enter your current password" required />
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="phone" class="mb-2 text-muted" style="width: 250px;">Phone Number</label>
                        <div class="input-group m-0 mb-4">
                            <span class="input-group-text rounded-0">+63</span>
                            <input type="tel" name="phone" id="phone" class="form-control phone-input rounded-0" value="<?= $user['phone'] ?>" maxlength="10" placeholder="Enter your phone number" required>
                        </div>
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="email" class="m-0 text-muted" style="width: 250px;">Email</label>
                        <input type="email" name="email" id="email" placeholder="Enter your email" value="<?= $user['email'] ?>" required disabled/>
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="dob" class="m-0 text-muted" style="width: 250px;">Date of Birth</label>
                        <input type="date" name="dob" id="dob" value="<?= $user['birth_date'] ?>" required disabled/>
                    </div>
                </div>
                <div class="input-group m-0 mb-4">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="sex" class="m-0 text-muted" style="width: 250px;">Sex</label>
                        <select name="sex" id="sex" required style="padding: 12px 0.75rem; flex-grow: 1;">
                            <option value="" disabled>Select your sex</option>
                            <option value="male" <?php $user['sex'] == 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?php $user['sex'] == 'female' ? '' : 'selected' ?>>Female</option>
                        </select>
                        <span class="text-danger" id="sex_err"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <input type="submit" class="addpro px-5" value="Save">
        </div>
    </form>
    <br><br><br><br>
    <!-- <script>
         document.getElementById("selectImageBtn").addEventListener("click", function () {
            document.getElementById("fileInput").click();
        });

        document.getElementById("fileInput").addEventListener("change", function (event) {
            const file = event.target.files[0]; 

            if (file) {
                if (file.type.startsWith("image/")) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        document.getElementById("profileImage").src = e.target.result;
                    };

                    reader.readAsDataURL(file); 
                } else {
                    alert("Please select a valid image file (JPEG, PNG).");
                }
            }
        });
    </script> -->
</main>
<?php include_once 'footer.php'; ?>
