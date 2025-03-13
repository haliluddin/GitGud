<?php
    include_once 'links.php'; 
    include_once 'header.php'; 
    include_once 'bootstrap.php';  

    $stalllogo = $businessname = $description = $businessemail = $businessphonenumber = $website = '';

    if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $record = $parkObj->fetchRecord($id);
            if (!empty($record)) {
                $stalllogo = $record['logo'];
                $businessname = $record['name'];
                $description = $record['description'];
                $businessemail = $record['email'];
                $businessphonenumber = $record['phone'];
                $website = $record['website'];
                $categories = explode(", ", $record['categories']);
                $paymentMethods = explode(", ", $record['payment_methods']);
                $operatingHours = explode("; ", $record['operating_hours']);
            } 
        } 
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        $businessname = clean_input($_POST['businessname']);
        $description = clean_input($_POST['description']);
        $businessemail = clean_input($_POST['businessemail']);
        $businessphonenumber = clean_input($_POST['businessphonenumber']);
        $website = clean_input($_POST['website']);
    
        $categories = isset($_POST['categories']) ? $_POST['categories'] : []; // Get categories
        $payment_methods = isset($_POST['payment_methods']) ? $_POST['payment_methods'] : []; // Get payment methods
    
        if (isset($_FILES['stalllogo']) && $_FILES['stalllogo']['error'] == UPLOAD_ERR_OK) {
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
        }
    
        $operatingHoursJson = $_POST['operating_hours'];
        $operatingHours = json_decode($operatingHoursJson, true);
    
        // Pass categories and payment methods to addStall function
        $stall = $parkObj->editStall($id, $businessname, $description, $businessemail, $businessphonenumber, $website, $stalllogo, $operatingHours, $categories, $payment_methods);
    } 
?>

<link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
<style>
     main {
        padding: 20px 120px;
    }
    .form-floating input, .form-floating textarea, .form-floating label::after, .logo, .add-schedule, .schedule-list{
        background-color: #F8F8F8 !important;
    }
</style>
<main>
    <div class="d-flex justify-content-end">
        <button class="addpro mb-3 prev" onclick="window.location.href='#';"><i class="fa-solid fa-chevron-left me-2"></i> Previous</button>
    </div>
    <form action="" class="srform rounded-2 bg-white p-5" method="POST" enctype="multipart/form-data">
        <div class="pagehead mb-4 border-bottom">
            <div>
                <h4 class="fw-bold m-0">Edit Business Page</h4>
                <span></span>
            </div>
            <p class="par mt-2">Update your page to ensure it has the latest and most accurate information about your business. This will help people find and connect with your business more effectively.</p>
        </div>
        <div class="d-flex gap-3 align-items-center">
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
                    if (file && file.size <= 5 * 1024 * 1024) { // Check file size
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const logoContainer = document.getElementById('logoContainer');
                            logoContainer.style.backgroundImage = `url('${e.target.result}')`;
                            logoContainer.innerHTML = ''; // Remove icon and text
                            logoContainer.appendChild(event.target); // Re-append the input field
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert('File is too large or not supported. Please select a JPG, JPEG, or PNG image under 5MB.');
                    }
                }
            </script>
            
            <div class="flex-grow-1 ms-4">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" style="color: black;" id="businessname" name="businessname" placeholder="Business Name" value="<?= $businessname ?>">
                    <label for="businessname">Business Name <span style="color: #CD5C08;">*</span></label>
                </div>

                <div class="form-group m-0 select2Part select2multiple w-100 floating-group">
                    <label class="floating-label">Categories <span style="color: #CD5C08;">*</span></label>
                    <select name="categories[]" id="categories" class="form-control customSelectMultiple floating-control" multiple>
                        <option value="Drinks" <?= in_array('Drinks', $categories) ? 'selected' : '' ?>>Drinks</option>
                        <option value="Vegetables" <?= in_array('Vegetables', $categories) ? 'selected' : '' ?>>Vegetables</option>
                        <option value="Desserts" <?= in_array('Desserts', $categories) ? 'selected' : '' ?>>Desserts</option>
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
                        alert("Please select at least one day.");
                        return;
                    }

                    // Check for duplicate days
                    for (let entry of operatingHoursData) {
                        for (let day of days) {
                            if (entry.days.includes(day)) {
                                alert(`The day "${day}" has already been added.`);
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