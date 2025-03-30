<?php  
ob_start();
include_once 'links.php'; 
include_once 'header.php';
include_once 'nav.php';
include_once 'bootstrap.php'; 
require_once 'classes/encdec.class.php';

if (!$user) {
    echo '<script>window.location.href="signin.php";</script>';
    exit();
}

if (!isset($park_id)) {
    echo '<script>window.location.href="index.php";</script>';
    exit();
}

$park = $parkObj->getPark($park_id);
$operatingHours = $parkObj->fetchBusinessOperatingHours($park_id);

if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $stallId = $_POST['stallId'];
    $newStatus = $_POST['status'];
    $parkObj->updateStallStatus($stallId, $newStatus);
    header("Location: " . $_SERVER['PHP_SELF'] . "?park_id=" . urlencode($park_id));
    exit();
}

if (isset($_POST['update_operating_hours'])) {
    $operatingHoursData = json_decode($_POST['operating_hours'], true);
    $parkObj->updateBusinessOperatingHours($park_id, $operatingHoursData);
    header("Location: " . $_SERVER['PHP_SELF'] . "?park_id=" . urlencode($park_id) . "#reports");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_business'])) {
    if ($parkObj->deleteBusiness($park_id)) {
        header("Location: index.php");
        exit();
    } else {
        $delete_err = "Failed to delete the business. Please try again.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['businesslogo']) && $_FILES['businesslogo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/business/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileExtension = pathinfo($_FILES['businesslogo']['name'], PATHINFO_EXTENSION);
    $uniqueFileName = $uploadDir . uniqid('logo_', true) . '.' . $fileExtension;
    if (move_uploaded_file($_FILES['businesslogo']['tmp_name'], $uniqueFileName)) {
        $business_logo = $uniqueFileName;
        if ($parkObj->updateBusinessLogo($park_id, $business_logo)) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?park_id=" . urlencode($park_id));
            exit();
        } 
    } 
}
?>
<style>
    .checksub{
        transition: color 0.3s, transform 0.3s;
    }
    .checksub:hover {
        transform: scale(1.20);
    }
</style>
<main>
    <div class="d-flex mb-3 align-items-center justify-content-end gap-3">
        <button class="addpro flex-shrink-0" type="button" data-bs-toggle="modal" data-bs-target="#invitestall">+ Add Stall</button>
        <i class="fa-solid fa-circle-user fs-1 hover" data-bs-toggle="offcanvas" data-bs-target="#foodparkbranch" aria-controls="foodparkbranch" style="cursor: pointer;"></i>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="foodparkbranch" aria-labelledby="foodparkbranchLabel" style="width: 40%;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="foodparkbranchLabel">Manage Food Park</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="text-center mb-4 border-bottom pb-3">
                <form id="logoForm" action="<?= $_SERVER['PHP_SELF'] . '?park_id=' . urlencode($park_id); ?>" method="POST" enctype="multipart/form-data">
                    <div class="profile-picture" onclick="document.getElementById('businesslogo').click();">
                        <img id="profileImg" src="<?= $park['business_logo'] ?>?t=<?= time() ?>" alt="Profile Picture" class="profile-img rounded-5">
                        <div class="camera-overlay">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                    </div>
                    <input type="file" id="businesslogo" name="businesslogo" accept="image/jpeg,image/png,image/jpg" style="display:none" onchange="previewLogo(event)">
                    <button type="submit" id="submitLogoButton" class="mt-2 fs-4 text-success checksub" style="border: none; background: none; display: none;">
                        <i class="fa-solid fa-check"></i>
                    </button>
                </form>
                <script>
                function previewLogo(event) {
                    const file = event.target.files[0];
                    if (file) {
                        if (file.size > 5 * 1024 * 1024) {
                            alert('File is too large. Please select an image under 5MB.');
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('profileImg').src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                        document.getElementById('submitLogoButton').style.display = 'inline';
                    }
                }
                </script>

                <h4 class="fw-bold m-0 mb-1 mt-3"><?= $park['business_name'] ?></h4>
                <span class="text-muted mb-1"><?= $park['street_building_house'] ?>, <?= $park['barangay'] ?>, Zamboanga City, Philippines</span>
                <div class="d-flex gap-2 text-muted align-items-center justify-content-center mb-1">
                    <span><i class="fa-solid fa-envelope"></i> <?= $park['business_email'] ?></span>
                    <span class="dot"></span>
                    <span><i class="fa-solid fa-phone small"></i> +63<?= $park['business_phone'] ?></span>
                </div>
                <button class="variation-btn addrem m-2" data-bs-toggle="modal" data-bs-target="#operatinghours">Operating Hours</button>
                <button class="variation-btn addrem" data-bs-toggle="modal" data-bs-target="#deletepark">Delete Park</button>
            </div>
        </div>
    </div>

    <!-- Operating Hours Modal -->
    <div class="modal fade" id="operatinghours" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="operatinghoursLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-custom-width">
            <form method="POST" id="operatingHoursForm">
                <input type="hidden" name="update_operating_hours" value="1">
                <input type="hidden" name="operating_hours" id="operating_hours">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="operatinghoursLabel">Edit Operating Hours</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="operatinghours">
                            <div class="add-schedule mb-4 small">
                                <label class="mb-3">What are your business operating hours? <span style="color: #CD5C08;">*</span></label>
                                <div id="timeForm">
                                    <div class="oh">
                                        <div class="och mb-3">
                                            <label>Open at</label>
                                            <div>
                                                <select name="open_hour" id="open_hour">
                                                    <script>
                                                        for (let i = 1; i <= 12; i++) {
                                                            document.write('<option value="'+i+'">'+String(i).padStart(2, "0")+'</option>');
                                                        }
                                                    </script>
                                                </select>
                                                :
                                                <select name="open_minute" id="open_minute">
                                                    <script>
                                                        for (let i = 0; i < 60; i++) {
                                                            document.write('<option value="'+i+'">'+String(i).padStart(2, "0")+'</option>');
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
                                                            document.write('<option value="'+i+'">'+String(i).padStart(2, "0")+'</option>');
                                                        }
                                                    </script>
                                                </select>
                                                :
                                                <select name="close_minute" id="close_minute">
                                                    <script>
                                                        for (let i = 0; i < 60; i++) {
                                                            document.write('<option value="'+i+'">'+String(i).padStart(2, "0")+'</option>');
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
                                
                                <?php if (!empty($operatingHours)) : ?>
                                    <?php foreach ($operatingHours as $row) : 
                                        $daysList = explode(', ', $row['days']);
                                    ?>
                                        operatingHoursData.push({
                                            days: <?= json_encode($daysList) ?>,
                                            openTime: '<?= $row['open_time'] ?>',
                                            closeTime: '<?= $row['close_time'] ?>'
                                        });
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const scheduleText = '<?= implode(", ", $daysList) ?>' + '<br>' + '<?= $row['open_time'] ?> - <?= $row['close_time'] ?>';
                                            const scheduleContainer = document.getElementById('scheduleContainer');
                                            const scheduleItem = document.createElement('p');
                                            scheduleItem.innerHTML = scheduleText;
                                            const deleteButton = document.createElement('button');
                                            deleteButton.innerHTML = '<i class="fa-regular fa-circle-xmark"></i>';
                                            deleteButton.classList.add('delete-btn');
                                            deleteButton.onclick = function() {
                                                scheduleContainer.removeChild(scheduleItem);
                                                operatingHoursData = operatingHoursData.filter(
                                                    entry =>
                                                        !(entry.days.join(',') === '<?= implode(",", $daysList) ?>' &&
                                                          entry.openTime === '<?= $row['open_time'] ?>' &&
                                                          entry.closeTime === '<?= $row['close_time'] ?>')
                                                );
                                                document.getElementById('operating_hours').value = JSON.stringify(operatingHoursData);
                                            };
                                            scheduleItem.insertBefore(deleteButton, scheduleItem.firstChild);
                                            scheduleContainer.appendChild(scheduleItem);
                                        });
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
                                        alert("Please select at least one day.");
                                        return;
                                    }
                                    for (let entry of operatingHoursData) {
                                        for (let day of days) {
                                            if (entry.days.includes(day)) {
                                                alert('The day "' + day + '" has already been added.');
                                                return;
                                            }
                                        }
                                    }
                                    const scheduleText = days.join(', ') + '<br>' + openHour + ':' + openMinute + ' ' + openAmpm + ' - ' + closeHour + ':' + closeMinute + ' ' + closeAmpm;
                                    operatingHoursData.push({
                                        days: days,
                                        openTime: openHour + ':' + openMinute + ' ' + openAmpm,
                                        closeTime: closeHour + ':' + closeMinute + ' ' + closeAmpm
                                    });
                                    document.getElementById('operating_hours').value = JSON.stringify(operatingHoursData);
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
                                                    openTime: openHour + ':' + openMinute + ' ' + openAmpm,
                                                    closeTime: closeHour + ':' + closeMinute + ' ' + closeAmpm
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
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary" onclick="return validateOperatingHours();">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <br><br><br><br><br>

    <!-- Delete Park Modal -->
    <div class="modal fade" id="deletepark" tabindex="-1" aria-labelledby="deleteparkLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="deleteBusinessForm">
                <input type="hidden" name="delete_business" value="1">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="text-center">
                            <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-exclamation"></i> Delete Food Park</h4>
                            <span>You are about to delete this food park.<br>Are you sure?</span><br><br>
                            <strong>This action cannot be undone. All associated data, including stalls under this park will be permanently removed.</strong>
                            <div class="mt-5 mb-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Delete</button>
                            </div>
                        </div>
                        <?php if(isset($delete_err)) { echo '<p class="text-danger">'.$delete_err.'</p>'; } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function validateOperatingHours() {
    let operatingHoursInput = document.getElementById('operating_hours').value;
    try {
        let operatingHours = JSON.parse(operatingHoursInput);
        if (!Array.isArray(operatingHours) || operatingHours.length === 0) {
            alert("Please add at least one operating hour schedule.");
            return false;
        }
    } catch (e) {
        alert("Invalid operating hours data. Please try again.");
        return false;
    }
    return true;
}
</script>

<?php 
include_once 'footer.php'; 
?>
