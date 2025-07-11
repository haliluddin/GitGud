<?php
    ob_start();

    include_once 'links.php'; 
    include_once 'header.php'; 
    include_once 'bootstrap.php';
    require_once 'classes/encdec.class.php'; 

    if (!isset($_GET['id'])) {
        die('Missing stall ID.');
    }
    $id = urldecode(decrypt($_GET['id']));
    
    $redirectPage = 'managestall.php';
    if (isset($_GET['source']) && $_GET['source'] == 'stall') {
        $redirectPage = 'stall.php?id=' . urlencode(encrypt($id));
    }

    $stalllogo = $businessname = $description = $businessemail = $businessphonenumber = $website = '';
    $categories = $paymentMethods = [];

    if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
        if (isset($_GET['id'])) {
            $id = urldecode(decrypt($_GET['id']));
            $record = $parkObj->fetchRecord($id);
            if (!empty($record)) {
                $stalllogo = $record['logo'];
                $businessname = $record['name'];
                $description = $record['description'];
                $businessemail = $record['email'];
                $businessphonenumber = $record['phone'];
                $website = $record['website'];
                $categories = !empty($record['category_ids'])
                    ? explode(",", $record['category_ids'])
                    : [];
                $paymentMethods = !empty($record['payment_methods']) ? explode(", ", $record['payment_methods']) : [];
                $operatingHours = !empty($record['operating_hours']) ? explode("; ", $record['operating_hours']) : [];
            } 
        }
        $categoriesList = $parkObj->getCategories();

    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = urldecode(decrypt($_GET['id']));

        $businessname = clean_input($_POST['businessname']);
        $description = clean_input($_POST['description']);
        $businessemail = clean_input($_POST['businessemail']);
        $businessphonenumber = clean_input($_POST['businessphonenumber']);
        $website = clean_input($_POST['website']);
    
        $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
        $paymentMethods = isset($_POST['payment_methods']) ? $_POST['payment_methods'] : [];

        $categoriesList = $parkObj->getCategories();
    
        if (isset($_FILES['stalllogo']) && $_FILES['stalllogo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/business/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        
            $fileExtension = pathinfo($_FILES['stalllogo']['name'], PATHINFO_EXTENSION);
            $uniqueFileName = uniqid('stall_', true) . '.' . $fileExtension;
            $uploadPath = $uploadDir . $uniqueFileName;
        
            if (move_uploaded_file($_FILES['stalllogo']['tmp_name'], $uploadPath)) {
                $stalllogo = $uploadPath;
            }
        } else {
            $record = $parkObj->fetchRecord($id);
            if (!empty($record)) {
                $stalllogo = $record['logo'];
            }
        }
    
        $operatingHoursJson = $_POST['operating_hours'];
        $operatingHours = json_decode($operatingHoursJson, true);
    
        $stall = $parkObj->editStall($id, $businessname, $description, $businessemail, $businessphonenumber, $website, $stalllogo, $operatingHours, $categories, $paymentMethods);

        if (isset($_GET['id'])) {
            $id = urldecode(decrypt($_GET['id']));
            $record = $parkObj->fetchRecord($id);
            if (!empty($record)) {
                $stalllogo = $record['logo'];
                $businessname = $record['name'];
                $description = $record['description'];
                $businessemail = $record['email'];
                $businessphonenumber = $record['phone'];
                $website = $record['website'];
                $categories = !empty($record['category_ids'])
                ? explode(",", $record['category_ids'])
                : [];
                $paymentMethods = !empty($record['payment_methods']) ? explode(", ", $record['payment_methods']) : [];
                $operatingHours = !empty($record['operating_hours']) ? explode("; ", $record['operating_hours']) : [];
            } 
        } 

        header("Location: $redirectPage");
        exit;
    } 
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
<style>
     .nav-main {
        padding: 20px 120px;
    }
    .form-floating input, .form-floating textarea, .form-floating label::after, .logo, .add-schedule, .schedule-list{
        background-color: #F8F8F8 !important;
    }
</style>
<main class="nav-main">
    <div class="d-flex justify-content-end">
        <button class="addpro mb-3 prev" onclick="window.location.href='<?= $redirectPage ?>';">
            <i class="fa-solid fa-chevron-left me-2"></i> Previous
        </button>
    </div>
    <form action="" class="ep srform rounded-2 bg-white p-5" method="POST" enctype="multipart/form-data" id="editForm">
        <div class="pagehead mb-4 border-bottom">
            <div>
                <h4 class="fw-bold m-0">Edit Business Page</h4>
                <span></span>
            </div>
            <p class="par mt-2">Update your page to ensure it has the latest and most accurate information about your business. This will help people find and connect with your business more effectively.</p>
        </div>
        <div class="d-flex gap-3 align-items-center picdet-tm">
            <div class="logo px-4 py-5 text-center border flex-shrink-0" id="logoContainer" 
                onclick="document.getElementById('stalllogo').click();" 
                style="background-size: cover; background-position: center; <?= !empty($stalllogo) ? 'background-image: url(\'' . $stalllogo . '\');' : '' ?>">
                
                <?php if (empty($stalllogo)) : ?>
                    <i class="fa-solid fa-arrow-up-from-bracket fs-3 p-2 mb-1"></i><br>
                    <label for="stalllogo" class="fw-bold m-0 fs-6">Add Business Logo</label><br>
                    <p class="small mb-2">or drag and drop</p>
                    <span class="text-muted logorem">Image size must be less than 5MB. Only JPG, JPEG, and PNG formats are allowed.</span>
                <?php endif; ?>

                <input type="file" id="stalllogo" name="stalllogo" accept="image/jpeg, image/png, image/jpg" 
                    style="display:none;" onchange="displayImage(event)">
            </div>

            <script>
                function displayImage(event) {
                    const file = event.target.files[0];
                    if (file && file.size <= 5 * 1024 * 1024) { 
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const logoContainer = document.getElementById('logoContainer');
                            logoContainer.style.backgroundImage = `url('${e.target.result}')`;
                            logoContainer.innerHTML = '';
                            logoContainer.appendChild(event.target);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        Swal.fire({icon: 'error', title: 'Image Error', text: 'File is too large or not supported. Please select a JPG, JPEG, or PNG image under 5MB.', confirmButtonColor: '#CD5C08'});
                    }
                }
            </script>
            
            <div class="flex-grow-1">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" style="color: black;" id="businessname" name="businessname" placeholder="Business Name" value="<?= $businessname ?>">
                    <label for="businessname">Business Name <span style="color: #CD5C08;">*</span></label>
                </div>

                <div class="form-group m-0 select2Part select2multiple w-100 floating-group">
                    <label class="floating-label">Categories <span style="color: #CD5C08;">*</span></label>
                    <select name="categories[]" id="categories" class="form-control customSelectMultiple floating-control" multiple>
                        <?php foreach($categoriesList as $cat): ?>
                            <option
                            value="<?= htmlspecialchars($cat['id']) ?>"
                            <?= in_array($cat['id'], $categories) ? 'selected' : '' ?>
                            >
                            <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <script src="assets/js/selectcategory.js"></script>
                
                <div class="form-floating mt-3">
                    <textarea class="form-control" style="color: black;" placeholder="Description" id="description" name="description"><?= $description ?></textarea>
                    <label for="description">Description <span style="color: #CD5C08;">*</span></label>
                </div>
            </div>
        </div>

        <div class="contact mt-4">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="businessemail" name="businessemail" placeholder="Business Email" value="<?= $businessemail ?>">
                <label for="businessemail">Business Email</label>
            </div>
            <div class="input-group mb-3 mt-0">
                <span class="input-group-text">+63</span>
                <div class="form-floating flex-grow-1">
                    <input type="text" class="form-control" id="businessphonenumber" name="businessphonenumber" placeholder="Business Phone Number" value="<?= $businessphonenumber ?>">
                    <label for="businessphonenumber">Business Phone Number</label>
                </div>
            </div>
            <div class="form-floating mb-4">
                <input type="text" class="form-control" id="website" name="website" placeholder="Website" value="<?= $website ?>">
                <label for="website">Website</label>
            </div>
        </div>

        <div class="operatinghours">
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
                
                <?php if (isset($operatingHours) && !empty($operatingHours)) : ?>
                    <?php foreach ($operatingHours as $hourString) : ?>
                        <?php
                            $parts = explode(" - ", $hourString);
                            if (count($parts) >= 2) {
                                $openTime = substr($parts[0], -8);
                                $days = trim(substr($parts[0], 0, -8));
                                $closeTime = $parts[1];
                                $daysList = explode(", ", $days);
                                
                                echo "operatingHoursData.push({
                                    days: ['" . implode("', '", $daysList) . "'],
                                    openTime: '$openTime',
                                    closeTime: '$closeTime'
                                });\n";
                                
                                echo "
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const scheduleText = '" . implode(", ", $daysList) . "<br>$openTime - $closeTime';
                                        const scheduleContainer = document.getElementById('scheduleContainer');
                                        const scheduleItem = document.createElement('p');
                                        scheduleItem.innerHTML = scheduleText;
                                        const deleteButton = document.createElement('button');
                                        deleteButton.innerHTML = '<i class=\"fa-regular fa-circle-xmark\"></i>';
                                        deleteButton.classList.add('delete-btn');
                                        deleteButton.onclick = function() {
                                            scheduleContainer.removeChild(scheduleItem);
                                            operatingHoursData = operatingHoursData.filter(
                                                entry =>
                                                    !(entry.days.join(',') === '" . implode(",", $daysList) . "' &&
                                                    entry.openTime === '$openTime' &&
                                                    entry.closeTime === '$closeTime')
                                            );
                                            document.getElementById('operating_hours').value = JSON.stringify(operatingHoursData);
                                        };
                                        scheduleItem.insertBefore(deleteButton, scheduleItem.firstChild);
                                        scheduleContainer.appendChild(scheduleItem);
                                    });
                                ";
                            }
                        ?>
                    <?php endforeach; ?>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('operating_hours').value = JSON.stringify(operatingHoursData);
                    });
                <?php endif; ?>
                
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
                        Swal.fire({icon: 'warning', title: 'Missing Day', text: 'Please select at least one day.', confirmButtonColor: '#CD5C08'});
                        return;
                    }
                    for (let entry of operatingHoursData) {
                        for (let day of days) {
                            if (entry.days.includes(day)) {
                                Swal.fire({icon: 'warning', title: 'Duplicate Day', text: `The day "${day}" has already been added.`, confirmButtonColor: '#CD5C08'});
                                return;
                            }
                        }
                    }
                    operatingHoursData.push({
                        days: days,
                        openTime: `${openHour}:${openMinute} ${openAmpm}`,
                        closeTime: `${closeHour}:${closeMinute} ${closeAmpm}`
                    });
                    document.getElementById('operating_hours').value = JSON.stringify(operatingHoursData);
                    const scheduleText = `${days.join(', ')}<br>${openHour}:${openMinute} ${openAmpm} - ${closeHour}:${closeMinute} ${closeAmpm}`;
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

        <div class="paymentmethod mt-4">
            <div class="add-schedule">
                <label for="" class="mb-3">What payment methods does your business accept? <span style="color: #CD5C08;">*</span></label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="payment_methods[]" value="Cash" id="flexCheckCash" <?= in_array('Cash', $paymentMethods) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="flexCheckCash">Cash</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="payment_methods[]" value="GCash" id="flexCheckGcash" <?= in_array('GCash', $paymentMethods) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="flexCheckGcash">GCash</label>
                </div>
            </div>
        </div>

        <div class="text-center pt-4 mt-4 createpage">
            <button type="submit" class="btn btn-primary send px-5">SAVE EDIT</button>
        </div>
    </form>
    <br><br><br><br>
</main>

<?php
    include_once 'footer.php'; 
?>

<!-- Modal for error messages -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Form Errors</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul id="errorList" class="text-danger"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('#editForm').addEventListener('submit', function(event) {
    event.preventDefault(); 
    let errors = [];

    let logoInput = document.getElementById('stalllogo');
    if (!logoInput.files.length && "<?= $stalllogo ?>" === "") {
        errors.push("Business logo is required.");
    }
    
    let businessName = document.getElementById('businessname').value.trim();
    if (businessName === "") errors.push("Business name is required.");

    let categoryOptions = document.getElementById('categories').selectedOptions;
    if (categoryOptions.length === 0) errors.push("At least one category is required.");

    let description = document.getElementById('description').value.trim();
    if (description === "") errors.push("Business description is required.");

    let email = document.getElementById('businessemail').value.trim();
    let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (email === "") {
        errors.push("Business email is required.");
    } else if (!emailPattern.test(email)) {
        errors.push("Enter a valid email address.");
    }

    let phone = document.getElementById('businessphonenumber').value.trim();
    let phonePattern = /^[0-9]{10}$/;
    if (phone === "") {
        errors.push("Business phone number is required.");
    } else if (!phonePattern.test(phone)) {
        errors.push("Enter a valid 10-digit phone number.");
    }

    let operatingHoursInput = document.getElementById('operating_hours').value;
    try {
        let operatingHours = JSON.parse(operatingHoursInput);
        if (!Array.isArray(operatingHours) || operatingHours.length === 0) {
            errors.push("Operating hours are required.");
        }
    } catch (e) {
        errors.push("Operating hours are required.");
    }

    let paymentMethods = document.querySelectorAll('input[name="payment_methods[]"]:checked');
    if (paymentMethods.length === 0) errors.push("At least one payment method is required.");

    if (errors.length > 0) {
        const errorList = document.getElementById('errorList');
        errorList.innerHTML = "";
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    } else {
        event.target.submit();
    }
});
</script>
