<?php
    session_start();

    include_once 'links.php'; 
    include_once 'bootstrap.php'; 
    include_once 'secondheader.php';

    require_once './classes/db.class.php';
    require_once './classes/park.class.php';

    $userObj = new User();
    $parkObj = new Park();

    $stalllogo = $businessname = $description = $businessemail = $businessphonenumber = $website = '';

    if (isset($_GET['owner_email']) && isset($_GET['owner_id']) && isset($_GET['park_id'])) {
        $owner_email = $_GET['owner_email'];
        $owner_id = $_GET['owner_id'];
        $park_id = $_GET['park_id'];

        $user = $userObj->getUser($owner_id);
    } 

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $stall = $parkObj->addStall($owner_id, $park_id, $businessname, $description, $businessemail, $businessphonenumber, $website, $stalllogo, $operatingHours, $categories, $payment_methods);

        echo "<script>
            alert('Stall has been successfully added to your food park. The stall owner can now be part of the service.');
            window.location.href = 'managestall.php';
            setTimeout(() => { window.close(); }, 1000);
        </script>";
        exit;

    } 
?>
<link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
<style>
    main {
        display: flex;
        height: calc(100vh - 65.61px); 
        overflow: hidden;
        background-color: white;
    }
    .fixed-image {
        width: 35%;
    }
    .srform {
        width: 65%;
        overflow-y: auto; 
        border: none;
        padding: 20px 80px;
    }
    .form-floating input, .form-floating textarea, .form-floating label::after, .logo, .add-schedule, .schedule-list{
        background-color: #F8F8F8 !important;
    }
</style>
<main>
    <img src="assets/images/rightbg.jpg" class="fixed-image">
    <form action="" class="srform" method="POST" enctype="multipart/form-data">
        <div class="mb-4 border-bottom">
            <div class="d-flex gap-3 align-items-center">
                <h4 class="fw-bold m-0">Create a business page</h4>
                <i class="fa-regular fa-circle-question" data-bs-toggle="tooltip" data-bs-placement="right" title="Your food stall will be registered under the food park that sent you this invitation link. Ensure that the invitation is from the correct food park, as this will determine where your stall is listed and managed." style="color: #CD5C08;"></i>
                <script>
                    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
                </script>
            </div>
            <p class="par mt-2">
                Your page is where people go to learn more about your business. Make sure yours has all the information they may need.
            </p>
        </div>
        <div class="d-flex justify-content-between align-items-center border rounded-2 py-2 px-3 mb-4" style="background-color: #F8F8F8;">
            <div class="d-flex gap-4 align-items-center">
                <img src="<?php echo $user['profile_img'] ?? 'assets/images/profile.jpg'; ?>" width="65" height="65" style="border-radius: 50%;">
                <div>
                    <h4 class="fw-bold mb-1"><?php echo $user['full_name']; ?></h4>
                    <div class="d-flex gap-2 text-muted align-items-center">
                        <span><i class="fa-solid fa-envelope"></i> <?php echo $user['email']; ?></span>
                        <span class="dot"></span>
                        <span><i class="fa-solid fa-phone small"></i> +63<?php echo $user['phone']; ?></span>
                    </div>
                </div>
            </div>
            <i class="text-muted">Stall Owner</i>
        </div>
        
        <div class="d-flex gap-3 align-items-center">
            <div class="logo px-4 py-5 text-center border flex-shrink-0" id="logoContainer" onclick="document.getElementById('stalllogo').click();" 
                style="background-size: cover; background-position: center;">
                <i class="fa-solid fa-arrow-up-from-bracket fs-3 p-2 mb-1"></i><br>
                <label for="stalllogo" class="fw-bold m-0 fs-6">Add Business Logo</label><br>
                <input type="file" id="stalllogo" name="stalllogo" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayImage(event)">
                <p class="small mb-2">or drag and drop</p>
                <span class="text-muted logorem">Image size must be less than 5MB. Only JPG, JPEG, and PNG formats are allowed.</span>
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
                    <input type="text" class="form-control" style="color: black;" id="businessname" name="businessname" placeholder="Business Name">
                    <label for="businessname">Business Name <span style="color: #CD5C08;">*</span></label>
                </div>

                <div class="form-group m-0 select2Part select2multiple w-100 floating-group">
                    <label class="floating-label">Categories <span style="color: #CD5C08;">*</span></label>
                    <select name="categories[]" id="categories" class="form-control customSelectMultiple floating-control" multiple>
                        <option value="BBQ">BBQ</option>
                        <option value="Seafood">Seafood</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Snacks">Snacks</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Vegan">Vegan</option>
                        <option value="Asian">Asian</option>
                        <option value="Burgers">Burgers</option>
                        <option value="Tacos">Tacos</option>
                        <option value="Fusion">Fusion</option>
                        <option value="Pasta">Pasta</option>
                        <option value="Salads">Salads</option>
                    </select>
                </div>
                <script src="assets/js/selectcategory.js"></script>
                
                <div class="form-floating mt-3">
                    <textarea class="form-control" style="color: black;" placeholder="Description" id="description" name="description"></textarea>
                    <label for="description">Description <span style="color: #CD5C08;">*</span></label>
                </div>
            </div>
        </div>

        <div class="contact mt-4">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="businessemail" name="businessemail" placeholder="Business Email">
                <label for="businessemail">Business Email</label>
            </div>
            <div class="input-group mb-3 mt-0">
                <span class="input-group-text">+63</span>
                <div class="form-floating flex-grow-1">
                    <input type="text" class="form-control" id="businessphonenumber" name="businessphonenumber" placeholder="Business Phone Number">
                    <label for="businessphonenumber">Business Phone Number</label>
                </div>
            </div>
            <div class="form-floating mb-4">
                <input type="text" class="form-control" id="website" name="website" placeholder="Website">
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
                    <input class="form-check-input" type="checkbox" name="payment_methods[]" value="Cash" id="flexCheckCash">
                    <label class="form-check-label" for="flexCheckCash">Cash</label>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="payment_methods[]" value="GCash" id="flexCheckGcash">
                    <label class="form-check-label" for="flexCheckGcash">GCash</label>
                </div>
            </div>
        </div>


        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                By clicking this box, I confirm that I am authorised by the Vendor to accept this Registration Form and the following <a href="">Terms and Conditions.</a>
            </label>
        </div>
        <div class="text-center pt-4 mt-4 createpage">
            <button type="submit" class="btn btn-primary send px-5">CREATE PAGE</button>
        </div>
        
    </form>
</main>


<!-- Bootstrap Modal for Error Messages -->
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
document.querySelector('.srform').addEventListener('submit', function(event) {
    event.preventDefault(); 
    let errors = [];

    let logoInput = document.getElementById('stalllogo');
    if (!logoInput.files.length) {
        errors.push("Business logo is required.");
    } else {
        let file = logoInput.files[0];
        let allowedExtensions = ['jpg', 'jpeg', 'png'];
        let fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            errors.push("Business logo must be a JPG, JPEG, or PNG.");
        }
        if (file.size > 5 * 1024 * 1024) {
            errors.push("Business logo must be less than 5MB.");
        }
    }

    let businessName = document.getElementById('businessname').value.trim();
    if (businessName === "") errors.push("Business name is required.");

    let categories = document.getElementById('categories').selectedOptions;
    if (categories.length === 0) errors.push("At least one category is required.");

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
    let phonePattern = /^[0-9]{10}$/; // Ensures exactly 10 digits
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

    let termsCheckbox = document.getElementById('flexCheckDefault');
    if (!termsCheckbox.checked) errors.push("You must accept the Terms and Conditions.");

    if (errors.length > 0) {
        let errorList = document.getElementById('errorList');
        errorList.innerHTML = ""; 
        errors.forEach(error => {
            let li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });

        let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    } else {
        event.target.submit();
    }
});
</script>
