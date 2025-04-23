<?php
    session_start();
    include_once 'links.php'; 
    include_once 'secondheader.php';
    require_once './classes/db.class.php';
    require_once __DIR__ . '/classes/encdec.class.php';
    $userObj = new User();

    if (isset($_GET['user_id'])) {
        $owner_id = decrypt(urldecode($_GET['user_id']));
    } elseif (isset($_SESSION['user']['id'])) {
        $owner_id = $_SESSION['user']['id'];
    } else {
        header('Location: signin.php');
        exit();
    }

    if ($userObj->isVerified($owner_id) != 1) {
        header('Location: email/verify_email.php');
        exit();
    }

    $user = $userObj->getUser($owner_id);
    if (!$user) {
        header('Location: email/verify_email.php');
        exit();
    }

    if ($user['role'] == 'Park Owner') {
        $status = $userObj->getBusinessStatus($owner_id);
        if (!isset($_GET['reapply'])) {
            if ($status == 'Pending Approval') {
                header('Location: pendingapproval.php');
                exit();
            } else if ($status == 'Approved' && basename($_SERVER['PHP_SELF']) != 'parkregistration.php') {
                header('Location: parkregistration.php');
                exit();
            } else if ($status == 'Rejected') {
                header('Location: rejected.php');
                exit();
            }
        }
    }

    $first_name = $user['first_name'];
    $middle_name = $user['middle_name'] ?? '';
    $last_name = $user['last_name'];
    $email = $user['email'];
    $phone = $user['phone'];

    $business_name = $business_type = $branches = $business_email = $business_phone = $region_province_city = $barangay = $street_building_house = $business_permit = $business_logo = '';
    $email_cb = false;
    $phone_cb = false;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $first_name = htmlspecialchars(trim($_POST['firstname']), ENT_QUOTES, 'UTF-8');
        $middle_name = htmlspecialchars(trim($_POST['middlename']), ENT_QUOTES, 'UTF-8');
        $last_name = htmlspecialchars(trim($_POST['lastname']), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $phone = filter_var(trim($_POST['phonenumber']), FILTER_SANITIZE_STRING);
        $business_name = filter_var(trim($_POST['businessname']), FILTER_SANITIZE_STRING);
        $business_type = filter_var(trim($_POST['businesstype']), FILTER_SANITIZE_STRING);
        
        $email_cb = isset($_POST['flexCheckEmail']);
        $phone_cb = isset($_POST['flexCheckPhone']);
        
        if ($email_cb) {
            $business_email = $email;
        } else {
            $business_email = filter_var(trim($_POST['businessemail']), FILTER_SANITIZE_EMAIL);
        }
        
        if ($phone_cb) {
            $business_phone = $phone;
        } else {
            $business_phone = htmlspecialchars(trim($_POST['businessphonenumber']), ENT_QUOTES, 'UTF-8');
        }

        $region_province_city = htmlspecialchars(trim($_POST['rpc']), ENT_QUOTES, 'UTF-8');
        $barangay = htmlspecialchars(trim($_POST['barangay']), ENT_QUOTES, 'UTF-8');
        $street_building_house = htmlspecialchars(trim($_POST['sbh']), ENT_QUOTES, 'UTF-8');
        $business_permit = isset($_FILES['businesspermit']) ? $_FILES['businesspermit'] : '';
        $business_logo = isset($_FILES['businesslogo']) ? $_FILES['businesslogo'] : '';

        if (isset($_FILES['businesspermit']) && $_FILES['businesspermit']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/business/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExtension = pathinfo($_FILES['businesspermit']['name'], PATHINFO_EXTENSION);
            $uniqueFileName = $uploadDir . uniqid('permit_', true) . '.' . $fileExtension;
            if (move_uploaded_file($_FILES['businesspermit']['tmp_name'], $uniqueFileName)) {
                $business_permit = $uniqueFileName;
            } else {
                $business_permit_err = 'Failed to upload the business permit.';
            }
        }

        if (isset($_FILES['businesslogo']) && $_FILES['businesslogo']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/business/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExtension = pathinfo($_FILES['businesslogo']['name'], PATHINFO_EXTENSION);
            $uniqueFileName = $uploadDir . uniqid('logo_', true) . '.' . $fileExtension;
            if (move_uploaded_file($_FILES['businesslogo']['tmp_name'], $uniqueFileName)) {
                $business_logo = $uniqueFileName;
            } else {
                $business_logo_err = 'Failed to upload the business logo.';
            }
        }    

        $operatingHoursJson = $_POST['operating_hours'];
        $operatingHours = json_decode($operatingHoursJson, true);
        
        $business_id = $userObj->registerBusiness($owner_id, $business_name, $business_type, $region_province_city, $barangay, $street_building_house, $business_phone, $business_email, $business_permit, $business_logo, $operatingHours);
        if ($business_id) {
            header('Location: pendingapproval.php');
            exit();
        } else if ($business_id == "Park Owner") {
            $status = $userObj->getBusinessStatus($owner_id);
            if ($status == 'Pending Approval') {
                header('Location: pendingapproval.php');
                exit();
            } else if ($status == 'Approved' && basename($_SERVER['PHP_SELF']) != 'parkregistration.php') {
                header('Location: parkregistration.php');
                exit();
            } else if ($status == 'Rejected') {
                header('Location: rejected.php');
            }
        }
    }
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .progressbar{
        max-width: 80%;
        margin: 2rem auto 4rem;
    }
    .main-pr {
        display: flex;
        height: calc(100vh - 77.61px); 
        overflow: hidden;
        background-color: white;
    }
    .fixed-image {
        width: 50%;
        object-fit: cover; 
    }
    .form {
        width: 50%;
        overflow-y: auto; 
        padding: 0 120px; 
        margin-top: 30px;
        border: none;
    }
    .btns-group, .btn-group{
        position: sticky;
        bottom: 0;
        padding: 30px 0;
        background-color: white;
        border-top: 3px solid #ccc;
        border-radius: 0;
        z-index: 100;
    }
    .mb{
        margin-bottom: 50%;
    }
    .form-floating input, .form-floating .form-select, .form-floating label::after {
        background-color: #F8F8F8 !important;
    }
</style>
<main class="main-pr">

    <img src="assets/images/background.jpg" class="fixed-image">
    
    <form action="" class="form" method="POST" enctype="multipart/form-data">
        <div class="progressbar">
            <div class="progress" id="progress"></div>
            <div class="progress-step progress-step-active" data-title="Owner"></div>
            <div class="progress-step" data-title="Details"></div>
            <div class="progress-step" data-title="Address"></div>
            <div class="progress-step" data-title="Document"></div>
            <div class="progress-step" data-title="Submit"></div>
        </div>
        <div class="form-step form-step-active">
            <div class="mb">
                <h4 class="fw-bold">Confirm business owner details</h4>
                <p class="par mb-4">
                    The account you registered is linked to the business owner's details including name, email, and contact number. 
                    By proceeding, you will be associating this food park with your account. 
                    If this is correct, continue with your registration below. 
                    If you need to register with different owner details, please create a new account.
                </p>
                <div class="form-floating mb-4">
                    <input type="text" class="form-control c" id="firstname" name="firstname" placeholder="First Name" value="<?= $first_name ?>" required readonly>
                    <label for="firstname">First Name <span style="color: #CD5C08;">*</span></label>
                </div>
                <div class="form-floating mb-4">
                    <input type="text" class="form-control c" id="middlename" name="middlename" placeholder="Middle Name" value="<?= $middle_name ?>" readonly>
                    <label for="middlename">Middle Name</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="text" class="form-control c" id="lastname" name="lastname" placeholder="Last Name" value="<?= $last_name ?>" required readonly>
                    <label for="lastname">Last Name <span style="color: #CD5C08;">*</span></label>
                </div>
                <div class="form-floating mb-4">
                    <input type="email" class="form-control c" id="email" name="email" placeholder="Email" value="<?= $email ?>" required readonly>
                    <label for="email">Email <span style="color: #CD5C08;">*</span></label>
                </div>
                <div class="input-group mb-3 mt-0">
                    <span class="input-group-text c">+63</span>
                    <div class="form-floating flex-grow-1">
                        <input type="text" class="form-control c" id="phonenumber" name="phonenumber" placeholder="Phone Number" value="<?= $phone ?>" maxlength="10" min="10" max="10" required readonly> 
                        <label for="phonenumber">Phone Number <span style="color: #CD5C08;">*</span></label>
                    </div>
                </div>
            </div>
            <div class="btn-group w-100">
                <a href="#" class="button btn-next">Next</a>
            </div>
        </div>
        <div class="form-step">
            <div class="mb">
                <h4 class="fw-bold">Tell us about your business</h4>
                <p class="par mb-4">This information will be shown on the web so that customers can search and contact you in case they have any questions.</p>

                <div class="form-floating mb-4">
                    <input type="text" class="form-control" id="businessname" name="businessname" placeholder="Food Park Name" data-name="Business name">
                    <label for="businessname">Business Name <span style="color: #CD5C08;">*</span></label>
                </div>

                <div class="form-floating mb-4">
                    <select class="form-select" id="businesstype" name="businesstype" aria-label="Floating label select example">
                        <option value="Food Park" selected>Food Park</option>
                        <option value="Food Stall" disabled>Food Stall</option>
                    </select>
                    <label for="businesstype">Business Type <span style="color: #CD5C08;">*</span></label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control c" id="email" name="email" placeholder="Email" value="<?= $email ?>" readonly>
                    <label for="email">Email <span style="color: #CD5C08;">*</span></label>
                </div>

                <div class="form-floating mb-3" id="businessemailGroup" style="display: none;">
                    <input type="text" class="form-control" id="businessemail" name="businessemail" placeholder="Business Email" data-name="Business email">
                    <label for="businessemail">Business Email <span style="color: #CD5C08;">*</span></label>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckEmail" name="flexCheckEmail" checked onchange="toggleBusinessEmailInput()">
                    <label class="form-check-label" for="flexCheckEmail">My Business and Personal Emails are the same</label>
                </div>

                <div class="input-group mb-3 mt-0">
                    <span class="input-group-text c">+63</span>
                    <div class="form-floating flex-grow-1">
                        <input type="text" class="form-control c" id="phonenumber" name="phonenumber" placeholder="Phone Number" value="<?= $phone ?>" readonly>
                        <label for="phonenumber">Phone Number <span style="color: #CD5C08;">*</span></label>
                    </div>
                </div>

                <div class="input-group mb-3 mt-0" id="businessphonenumberGroup" style="display: none;">
                    <span class="input-group-text">+63</span>
                    <div class="form-floating flex-grow-1">
                        <input type="text" class="form-control" id="businessphonenumber" name="businessphonenumber" placeholder="Business Phone Number" data-name="Business phone number">
                        <label for="businessphonenumber">Business Phone Number <span style="color: #CD5C08;">*</span></label>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked"  name="flexCheckPhone" checked onchange="toggleBusinessPhoneNumberInput()">
                    <label class="form-check-label" for="flexCheckChecked">My Business and Personal Phone numbers are the same</label>
                </div>

                <script>
                    function toggleBusinessPhoneNumberInput() {
                        const phoneInput = document.getElementById('phonenumber');
                        const businessPhoneNumberGroup = document.getElementById('businessphonenumberGroup');
                        const checkbox = document.getElementById('flexCheckChecked');

                        if (checkbox.checked) {
                            businessPhoneNumberGroup.style.display = 'none';
                            phoneInput.readOnly = true;
                        } else {
                            businessPhoneNumberGroup.style.display = 'flex';
                            phoneInput.readOnly = false;
                        }
                    }

                    function toggleBusinessEmailInput() {
                        const emailInput = document.getElementById('email');
                        const businessEmailGroup = document.getElementById('businessemailGroup');
                        const emailCheckbox = document.getElementById('flexCheckEmail');

                        if (emailCheckbox.checked) {
                            businessEmailGroup.style.display = 'none';
                            emailInput.readOnly = true;
                        } else {
                            businessEmailGroup.style.display = 'block';
                            emailInput.readOnly = false;
                        }
                    }
                </script>

                <div class="mb-4">
                    <label for="businesslogo">Upload your business logo<span style="color: #CD5C08;">*</span></label>
                    <div class="logocon px-3 py-4 mt-3 text-center border" onclick="document.getElementById('businesslogo').click();">
                        <img src="assets/images/upload-icon.png" class="w-50 h-50 mb-2" alt=""><br>
                        <span>Maximum of 5MB and can accept only JPG, JPEG, PNG format</span>
                        <input type="file" id="businesslogo" accept="image/jpeg, image/png, image/jpg" name="businesslogo" style="display:none;" />
                    </div>
                    <div id="uploaded-parkfiles" class="mt-4">
                        <!-- Uploaded Park Image files list -->
                    </div>
                </div>

                <input type="hidden" name="operating_hours" id="operating_hours">
                <div class="add-schedule mb-4 small">
                    <label class="mb-3">What is your business operating hours? <span style="color: #CD5C08;">*</span></label>
                    <div id="timeForm">
                        <div class="oh">
                            <div class="och mb-3">
                                <label>Open at</label>
                                <div>
                                    <select name="open_hour" id="open_hour">
                                        <script>
                                            for (let i = 1; i <= 12; i++) {
                                                document.write(`<option value="${i}">${String(i).padStart(2, '0')}</option>`);
                                            }
                                        </script>
                                    </select>
                                    :
                                    <select name="open_minute" id="open_minute">
                                        <script>
                                            for (let i = 0; i < 60; i++) {
                                                document.write(`<option value="${i}">${String(i).padStart(2, '0')}</option>`);
                                            }
                                        </script>
                                    </select>
                                    <select name="open_ampm" id="open_ampm">
                                        <option value="AM">AM</option>
                                        <option value="PM">PM</option>
                                    </select>
                                </div>
                            </div>
                        
                            <div class="och mb-3">
                                <label>Close at</label>
                                <div>
                                    <select name="close_hour" id="close_hour">
                                        <script>
                                            for (let i = 1; i <= 12; i++) {
                                                document.write(`<option value="${i}">${String(i).padStart(2, '0')}</option>`);
                                            }
                                        </script>
                                    </select>
                                    :
                                    <select name="close_minute" id="close_minute">
                                        <script>
                                            for (let i = 0; i < 60; i++) {
                                                document.write(`<option value="${i}">${String(i).padStart(2, '0')}</option>`);
                                            }
                                        </script>
                                    </select>
                                    <select name="close_ampm" id="close_ampm">
                                        <option value="AM">AM</option>
                                        <option value="PM">PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>  
                        <div class="day-checkboxes mb-2">
                            <label><input type="checkbox" name="days" value="Monday"> Monday</label>
                            <label><input type="checkbox" name="days" value="Tuesday"> Tuesday</label>
                            <label><input type="checkbox" name="days" value="Wednesday"> Wednesday</label>
                            <label><input type="checkbox" name="days" value="Thursday"> Thursday</label>
                            <label><input type="checkbox" name="days" value="Friday"> Friday</label>
                            <label><input type="checkbox" name="days" value="Saturday"> Saturday</label>
                            <label><input type="checkbox" name="days" value="Sunday"> Sunday</label>
                        </div>

                        <button type="button" class="add-hours-btn mt-2" onclick="addOperatingHours()">+ Add</button>
                    </div>
                </div>
                <div class="schedule-list small">
                    <h6>Operating Hours</h6>
                    <div id="scheduleContainer"></div>
                </div>
                <script>
                    let operatingHoursData = [];
                    function addOperatingHours() {
                        const openHour = String(document.getElementById('open_hour').value).padStart(2, '0');
                        const openMinute = String(document.getElementById('open_minute').value).padStart(2, '0');
                        const openAmpm = document.getElementById('open_ampm').value;
                        const closeHour = String(document.getElementById('close_hour').value).padStart(2, '0');
                        const closeMinute = String(document.getElementById('close_minute').value).padStart(2, '0');
                        const closeAmpm = document.getElementById('close_ampm').value;

                        const days = Array.from(document.querySelectorAll('input[name="days"]:checked'))
                            .map(checkbox => checkbox.value);

                        if (days.length === 0) {
                            Swal.fire({icon: 'info', title: 'Select a Day', text: 'Pick at least one day to continue.', confirmButtonColor: '#CD5C08'});
                            return;
                        }

                        // Check for duplicate days
                        for (let entry of operatingHoursData) {
                            for (let day of days) {
                                if (entry.days.includes(day)) {
                                    Swal.fire({icon: 'info', title: 'Duplicate Day', text: `The day "${day}" is already added.`, confirmButtonColor: '#CD5C08'});
                                    return;
                                }
                            }
                        }

                        // Add to the operatingHoursData array
                        operatingHoursData.push({
                            days: days,
                            openTime: `${openHour}:${openMinute} ${openAmpm}`,
                            closeTime: `${closeHour}:${closeMinute} ${closeAmpm}`
                        });

                        // Update the hidden input field with the JSON string
                        document.getElementById('operating_hours').value = JSON.stringify(operatingHoursData);

                        // Display in the UI
                        const scheduleText = `${days.join(', ')} <br> ${openHour}:${openMinute} ${openAmpm} - ${closeHour}:${closeMinute} ${closeAmpm}`;
                        const scheduleContainer = document.getElementById("scheduleContainer");

                        const scheduleItem = document.createElement("p");
                        scheduleItem.innerHTML = scheduleText;

                        const deleteButton = document.createElement("button");
                        deleteButton.innerHTML = '<i class="fa-regular fa-circle-xmark"></i>';
                        deleteButton.classList.add("delete-btn");
                        deleteButton.onclick = function() {
                            scheduleContainer.removeChild(scheduleItem);
                            operatingHoursData = operatingHoursData.filter(
                                entry =>
                                    JSON.stringify(entry) !==
                                    JSON.stringify({
                                        days: days,
                                        openTime: `${openHour}:${openMinute} ${openAmpm}`,
                                        closeTime: `${closeHour}:${closeMinute} ${closeAmpm}`
                                    })
                            );
                            document.getElementById('operating_hours').value = JSON.stringify(operatingHoursData);
                        };

                        scheduleItem.insertBefore(deleteButton, scheduleItem.firstChild);
                        scheduleContainer.appendChild(scheduleItem);

                        // Reset inputs
                        document.getElementById('open_hour').selectedIndex = 0;
                        document.getElementById('open_minute').selectedIndex = 0;
                        document.getElementById('open_ampm').selectedIndex = 0;
                        document.getElementById('close_hour').selectedIndex = 0;
                        document.getElementById('close_minute').selectedIndex = 0;
                        document.getElementById('close_ampm').selectedIndex = 0;
                        document.querySelectorAll('input[name="days"]').forEach(checkbox => checkbox.checked = false);
                    }
                </script>
            </div>
            <div class="btns-group">
                <a href="#" class="button btn-prev">Previous</a>
                <a href="#" class="button btn-next">Next</a>
            </div>
        </div>
        <div class="form-step">
            <div class="mb">
                <h4 class="fw-bold">Where is your business located?</h4>
                <p class="par mb-4">
                    Customers will use this to find your business for directions and pickup options.
                </p>
                <div class="form-floating mb-4">
                    <input type="text" class="form-control c" id="rpc" name="rpc" placeholder="Region, Province, City" value="Mindanao, Zamboanga Del Sur, Zamboanga City" readonly>
                    <label for="rpc">Region, Province, City <span style="color: #CD5C08;">*</span></label>
                </div>
                <div class="form-floating mb-4">
                    <select class="form-select" id="barangay" name="barangay" required aria-label="Barangay select">
                        <option value="" disabled selected>Select</option>
                        <option value="Arena Blanco">Arena Blanco</option>
                        <option value="Ayala">Ayala</option>
                        <option value="Baliwasan">Baliwasan</option>
                        <option value="Baluno">Baluno</option>
                        <option value="Barangay Zone I (Pob.)">Barangay Zone I (Pob.)</option>
                        <option value="Barangay Zone II (Pob.)">Barangay Zone II (Pob.)</option>
                        <option value="Barangay Zone III (Pob.)">Barangay Zone III (Pob.)</option>
                        <option value="Barangay Zone IV (Pob.)">Barangay Zone IV (Pob.)</option>
                        <option value="Boalan">Boalan</option>
                        <option value="Bolong">Bolong</option>
                        <option value="Buenavista">Buenavista</option>
                        <option value="Bunguiao">Bunguiao</option>
                        <option value="Busay (Sacol Island)">Busay (Sacol Island)</option>
                        <option value="Cabaluay">Cabaluay</option>
                        <option value="Cabatangan">Cabatangan</option>
                        <option value="Cacao">Cacao</option>
                        <option value="Calabasa">Calabasa</option>
                        <option value="Calarian">Calarian</option>
                        <option value="Camino Nuevo">Camino Nuevo</option>
                        <option value="Campo Islam">Campo Islam</option>
                        <option value="Canelar">Canelar</option>
                        <option value="Capisan">Capisan</option>
                        <option value="Cawit">Cawit</option>
                        <option value="Culianan">Culianan</option>
                        <option value="Curuan">Curuan</option>
                        <option value="Dita">Dita</option>
                        <option value="Divisoria">Divisoria</option>
                        <option value="Dulian (Upper Bunguiao)">Dulian (Upper Bunguiao)</option>
                        <option value="Dulian (Upper Pasonanca)">Dulian (Upper Pasonanca)</option>
                        <option value="Guisao">Guisao</option>
                        <option value="Guiwan">Guiwan</option>
                        <option value="Kasanyangan">Kasanyangan</option>
                        <option value="La Paz">La Paz</option>
                        <option value="Labuan">Labuan</option>
                        <option value="Lamisahan">Lamisahan</option>
                        <option value="Landang Gua">Landang Gua</option>
                        <option value="Landang Laum">Landang Laum</option>
                        <option value="Lanzones">Lanzones</option>
                        <option value="Lapakan">Lapakan</option>
                        <option value="Latuan (Curuan)">Latuan (Curuan)</option>
                        <option value="Licomo">Licomo</option>
                        <option value="Limaong">Limaong</option>
                        <option value="Limpapa">Limpapa</option>
                        <option value="Lubigan">Lubigan</option>
                        <option value="Lumayang">Lumayang</option>
                        <option value="Lumbangan">Lumbangan</option>
                        <option value="Lunzuran">Lunzuran</option>
                        <option value="Maasin">Maasin</option>
                        <option value="Malagutay">Malagutay</option>
                        <option value="Mampang">Mampang</option>
                        <option value="Manalipa">Manalipa</option>
                        <option value="Mangusu">Mangusu</option>
                        <option value="Manicahan">Manicahan</option>
                        <option value="Mariki">Mariki</option>
                        <option value="Mercedes">Mercedes</option>
                        <option value="Muti">Muti</option>
                        <option value="Pamucutan">Pamucutan</option>
                        <option value="Pangapuyan">Pangapuyan</option>
                        <option value="Panubigan">Panubigan</option>
                        <option value="Pasilmanta (Sacol Island)">Pasilmanta (Sacol Island)</option>
                        <option value="Pasobolong">Pasobolong</option>
                        <option value="Pasonanca">Pasonanca</option>
                        <option value="Patalon">Patalon</option>
                        <option value="Putik">Putik</option>
                        <option value="Quiniput">Quiniput</option>
                        <option value="Recodo">Recodo</option>
                        <option value="Rio Hondo">Rio Hondo</option>
                        <option value="Salaan">Salaan</option>
                        <option value="San Jose Cawa-Cawa">San Jose Cawa-Cawa</option>
                        <option value="San Jose Gusu">San Jose Gusu</option>
                        <option value="San Roque">San Roque</option>
                        <option value="Sangali">Sangali</option>
                        <option value="Santa Barbara">Santa Barbara</option>
                        <option value="Santa Catalina">Santa Catalina</option>
                        <option value="Santa Maria">Santa Maria</option>
                        <option value="Santo Nino">Santo Nino</option>
                        <option value="Sibulao (Caruan)">Sibulao (Caruan)</option>
                        <option value="Sinubung">Sinubung</option>
                        <option value="Sinunoc">Sinunoc</option>
                        <option value="Tagasilay">Tagasilay</option>
                        <option value="Taguiti">Taguiti</option>
                        <option value="Talabaan">Talabaan</option>
                        <option value="Talisayan">Talisayan</option>
                        <option value="Talon-Talon">Talon-Talon</option>
                        <option value="Taluksangay">Taluksangay</option>
                        <option value="Tetuan">Tetuan</option>
                        <option value="Tictapul">Tictapul</option>
                        <option value="Tigbalabag">Tigbalabag</option>
                        <option value="Tigtabon">Tigtabon</option>
                        <option value="Tolosa">Tolosa</option>
                        <option value="Tugbungan">Tugbungan</option>
                        <option value="Tulungatung">Tulungatung</option>
                        <option value="Tumaga">Tumaga</option>
                        <option value="Tumalutab">Tumalutab</option>
                        <option value="Tumitus">Tumitus</option>
                        <option value="Victoria">Victoria</option>
                        <option value="Vitali">Vitali</option>
                        <option value="Zambowood">Zambowood</option>
                    </select>

                    <label for="barangay">Barangay <span style="color: #CD5C08;">*</span></label>
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control" id="sbh" name="sbh" placeholder="Street Name, Building, House No.">
                    <label for="sbh">Street Name, Building, House No. <span style="color: #CD5C08;">*</span></label>
                </div>
            </div>
            <div class="btns-group">
                <a href="#" class="button btn-prev">Previous</a>
                <a href="#" class="button btn-next">Next</a>
            </div>
        </div>
        <div class="form-step">
            <div class="mb">
                <h4 class="fw-bold">Add your business permit</h4>
                <p class="par mb-4">
                    We need your legal document to verify and approve your business registration.
                </p>
                <div>
                    <label for="fplogo">Upload FULL pages of your Business Permit <span style="color: #CD5C08;">*</span></label>
                    <div class="logocon px-3 py-4 mt-3 text-center border" onclick="document.getElementById('fplogo').click();">
                        <img src="assets/images/upload-icon.png" class="w-50 h-50 mb-2" alt=""><br>
                        <span>Maximum of 5MB and can accept only JPG, JPEG, PNG or PDF format</span>
                        <input type="file" id="fplogo" accept="image/jpeg, image/png, image/jpg, application/pdf" name="businesspermit" style="display:none;" />
                    </div>
                    <div id="uploaded-files" class="mt-4">
                        <!-- Uploaded Business Permit files list -->
                    </div>
                </div>
            </div>
            <div class="btns-group">
                <a href="" class="button btn-prev">Previous</a>
                <a href="" class="button btn-next">Next</a>
            </div>
        </div>
        <div class="form-step">
            <div class="mb">
                <h4 class="fw-bold">Review your details</h4>
                <p class="par mb-4">
                    Check carefully before you send us all the important information.
                </p>
                <div class="box py-3 fs">
                    <!-- Business Owner Section -->
                    <div class="mb-2">
                        <h6 class="fw-bold">Business Owner</h6>
                        <a href="#" class="edit-btn" data-step="0">Edit</a>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">First Name</span>
                        <span class="first-name">N/A</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Middle Name</span>
                        <span class="middle-name">N/A</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Last Name</span>
                        <span class="last-name">N/A</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Email</span>
                        <span class="email">N/A</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Mobile Phone Number</span>
                        <span class="phone">N/A</span>
                    </div>
                </div>
                
                <!-- Business Details Section -->
                <div class="box py-3 fs">
                    <div class="mb-2">
                        <h6 class="fw-bold">Business Details</h6>
                        <a href="#" class="edit-btn" data-step="1">Edit</a>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Business Name</span>
                        <span class="business-name">N/A</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Business Type</span>
                        <span class="business-type">Food Park</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Business Email</span>
                        <span class="business-email">N/A</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Business Phone Number</span>
                        <span class="business-phone">N/A</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Business Logo</span>
                        <span class="business-logo">N/A</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Operating Hours</span>
                        <span id="review-operating-hours">N/A</span>
                    </div>
                </div>

                <!-- Business Address Section -->
                <div class="box py-3 fs">
                    <div class="mb-3">
                        <h6 class="fw-bold">Business Address</h6>
                        <a href="#" class="edit-btn" data-step="2">Edit</a>
                    </div>
                    <span class="mb-1"><span class="sbh">N/A</span>, <span class="barangay">N/A</span>, Zamboanga City, Zamboanga Del Sur, Mindanao, 7000</span>
                </div>
                
                <!-- Business Document Section -->
                <div class="box py-3 mb-4 fs">
                    <div class="mb-2">
                        <h6 class="fw-bold">Business Document</h6>
                        <a href="#" class="edit-btn" data-step="3">Edit</a>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">File</span>
                        <span class="fplogo">N/A</span>
                    </div>
                </div>

                <!-- Terms and Conditions Checkbox -->
                <div class="form-check mb-4 last">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                        By clicking this box, I confirm that I am authorised by the Vendor to accept this Registration Form and the following <a href="#">Terms and Conditions.</a>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="btns-group">
                <a href="#" class="button btn-prev">Previous</a>
                <input type="submit" value="Submit" class="button" id="submitButton" />
            </div>
        </div>
        <script>
            document.getElementById("submitButton").addEventListener("click", function (e) {
                // Check if the checkbox is selected
                const checkbox = document.getElementById("flexCheckDefault");
                if (!checkbox.checked) {
                    e.preventDefault(); // Prevent form submission
                    Swal.fire({icon: 'warning', title: 'Terms Not Confirmed', text: 'Please confirm the Terms and Conditions by checking the box.', confirmButtonColor: '#CD5C08'});
                }
            });
        </script>`
    </form>

    <script src="assets/js/uploadedfiles.js?v=<?php echo time(); ?>"></script>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Validation Errors</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="errorList"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const prevBtns = document.querySelectorAll(".btn-prev");
        const nextBtns = document.querySelectorAll(".btn-next");
        const progress = document.getElementById("progress");
        const formSteps = document.querySelectorAll(".form-step");
        const progressSteps = document.querySelectorAll(".progress-step");
        const errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
        const errorList = document.getElementById("errorList");

        let formStepsNum = 0;

        const formData = {
            firstName: "",
            middleName: "",
            lastName: "",
            email: "",
            phone: "",
            businessName: "",
            businessType: "",
            businessEmail: "",
            businessPhone: "",
            barangay: "",
            sbh: "",
            fplogo: "",
            businessLogo: "",
        };

        nextBtns.forEach((btn) => {
            btn.addEventListener("click", (event) => {
                event.preventDefault();  // Prevent default form behavior

                const currentStepInputs = formSteps[formStepsNum].querySelectorAll("input, select");
                const errors = validateInputs(currentStepInputs);

                if (errors.length > 0) {
                    showErrors(errors);
                } else {

                    saveFormData(currentStepInputs);

                    formStepsNum++;
                    updateFormSteps();
                    updateProgressbar();

                    if (formStepsNum === formSteps.length - 1) {
                        updateReviewStep();
                    }
                }
            });
        });

        prevBtns.forEach((btn) => {
            btn.addEventListener("click", (event) => {
                event.preventDefault();  // Prevent default form behavior
                formStepsNum--;
                updateFormSteps();
                updateProgressbar();
            });
        });

        const editBtns = document.querySelectorAll(".edit-btn");

        editBtns.forEach((btn) => {
            btn.addEventListener("click", (event) => {
                event.preventDefault();  // Prevent default anchor behavior

                const step = parseInt(btn.getAttribute("data-step"));
                formStepsNum = step;  // Update form step to the corresponding step

                updateFormSteps();
                updateProgressbar();
            });
        });

        function saveFormData(inputs) {
            const emailCheckbox = document.getElementById("flexCheckEmail");
            const phoneCheckbox = document.getElementById("flexCheckChecked");

            inputs.forEach((input) => {
                const value = input.type === "file" ? input.files[0]?.name : input.value.trim();
                const id = input.id;

                if (id) {
                    if (id === "firstname") formData.firstName = value;
                    else if (id === "middlename") formData.middleName = value;
                    else if (id === "lastname") formData.lastName = value;
                    else if (id === "email") formData.email = value;
                    else if (id === "phonenumber") formData.phone = value;
                    else if (id === "businessname") formData.businessName = value;
                    else if (id === "businessemail") {
                        formData.businessEmail = emailCheckbox.checked ? formData.email : value;
                    } else if (id === "businessphonenumber") {
                        formData.businessPhone = phoneCheckbox.checked ? formData.phone : value;
                    }
                    else if (id === "barangay") formData.barangay = value;
                    else if (id === "sbh") formData.sbh = value;
                    else if (id === "fplogo") formData.fplogo = value;
                    else if (id === "businesslogo") formData.businessLogo = value;

                    else formData[id] = value;
                }
            });
        }


        function updateFormSteps() {
            formSteps.forEach((formStep) => {
                formStep.classList.remove("form-step-active");
            });
            formSteps[formStepsNum].classList.add("form-step-active");
        }

        function updateProgressbar() {
            progressSteps.forEach((progressStep, idx) => {
                if (idx < formStepsNum + 1) {
                    progressStep.classList.add("progress-step-active");
                } else {
                    progressStep.classList.remove("progress-step-active");
                }
            });

            const progressActive = document.querySelectorAll(".progress-step-active");
            progress.style.width = ((progressActive.length - 1) / (progressSteps.length - 1)) * 100 + "%";
        }

        function validateInputs(inputs) {
            const errors = [];
            const emailCheckbox = document.getElementById('flexCheckEmail');
            const phoneCheckbox = document.getElementById('flexCheckChecked');

            // Check if the current step contains the operating hours field
            const currentStep = formSteps[formStepsNum];
            if (currentStep.querySelector('#operating_hours')) {
                const operatingHoursInput = document.getElementById('operating_hours').value;
                if (!operatingHoursInput || JSON.parse(operatingHoursInput).length === 0) {
                    errors.push("Please add at least one operating hour.");
                }
            }

            inputs.forEach((input) => {
                const value = input.value.trim();
                const id = input.id;

                if (id === "businessname" && value === "") {
                    errors.push("The business name is required.");
                }

                if (id === "barangay" && (value === "" || value === "Select")) {
                    errors.push("The barangay is required.");
                }

                if (id === "sbh" && value === "") {
                    errors.push("The street, building, house is required.");
                }

                // Check for image upload
                if (id === "fplogo") {
                    if (input.files.length === 0) {
                        errors.push("The business permit is required.");
                    } else {
                        const file = input.files[0];
                        const allowedTypes = ["image/png", "image/jpeg", "image/jpg", "application/pdf"];
                        const maxSizeInBytes = 5 * 1024 * 1024; // 5 MB

                        if (!allowedTypes.includes(file.type)) {
                            errors.push("The business permit must be a PNG, JPEG, JPG, or PDF file.");
                        }
                        if (file.size > maxSizeInBytes) {
                            errors.push("The business permit must not exceed 5 MB.");
                        }
                    }
                }

                if (id === "businesslogo") {
                    if (input.files.length === 0) {
                        errors.push("The business logo is required.");
                    } else {
                        const file = input.files[0];
                        const allowedTypes = ["image/png", "image/jpeg", "image/jpg"];
                        const maxSizeInBytes = 5 * 1024 * 1024; // 5 MB

                        if (!allowedTypes.includes(file.type)) {
                            errors.push("The business logo must be a PNG, JPEG, or JPG file.");
                        }
                        if (file.size > maxSizeInBytes) {
                            errors.push("The business logo must not exceed 5 MB.");
                        }
                    }
                }

                if (id === "businessemail" && !emailCheckbox.checked) {
                    if (value === "") {
                        errors.push("The business email is required.");
                    } else if (!validateEmail(value)) {
                        errors.push("The business email must be in a valid format.");
                    }
                }

                if (id === "businessphonenumber" && !phoneCheckbox.checked) {
                    if (value === "") {
                        errors.push("The business phone number is required.");
                    } else if (!validatePhone(value)) {
                        errors.push("The business phone number must be valid.");
                    }
                }
            });

            return errors;
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function validatePhone(phone) {
            const phoneRegex = /^9\d{9}$/;
            return phoneRegex.test(phone);
        }

        function showErrors(errors) {
            errorList.innerHTML = errors.map((error) => `<li>${error}</li>`).join("");
            errorModal.show();
        }

        function updateReviewStep() {
            // Other fields
            document.querySelector(".first-name").textContent = formData.firstName;
            document.querySelector(".middle-name").textContent = formData.middleName;
            document.querySelector(".last-name").textContent = formData.lastName;
            document.querySelector(".email").textContent = formData.email;
            document.querySelector(".phone").textContent = formData.phone;
            document.querySelector(".business-name").textContent = formData.businessName;
            document.querySelector(".business-email").textContent = formData.businessEmail;
            document.querySelector(".business-phone").textContent = formData.businessPhone;
            document.querySelector(".barangay").textContent = formData.barangay;
            document.querySelector(".sbh").textContent = formData.sbh;
            document.querySelector(".fplogo").textContent = formData.fplogo;
            document.querySelector(".business-logo").textContent = formData.businessLogo;

            // Update Operating Hours
            const reviewOperatingHours = document.getElementById("review-operating-hours");
            if (operatingHoursData.length === 0) {
                reviewOperatingHours.textContent = "N/A";
            } else {
                const operatingHoursText = operatingHoursData
                    .map(
                        (entry) =>
                            `${entry.days.join(", ")} (${entry.openTime} - ${entry.closeTime})`
                    )
                    .join("<br>");
                reviewOperatingHours.innerHTML = operatingHoursText;
            }
        }


    </script>


</main>

