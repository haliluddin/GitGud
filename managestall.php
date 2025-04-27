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

if (isset($_POST['report_update'])) {
    $report_id = $_POST['report_id'];
    $action = $_POST['action'];
    $newStatus = ($action === 'resolve') ? 'Resolved' : 'Rejected';
    $parkObj->updateStallReportStatus($report_id, $newStatus);
    header("Location: " . $_SERVER['PHP_SELF'] . "?park_id=" . urlencode($park_id) . "#reports");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grand_opening'], $_POST['park_id'])) {
    if ($_POST['grand_opening'] == 1) {
        $isParkEmpty = $parkObj->isParkEmpty($park_id);

        if ($isParkEmpty) {
            // Sweet alert
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

            echo '
            <script>
                Swal.fire({
                    icon: "warning",
                    title: "No Stalls Found",
                    text: "You cannot open the park because it has no stalls. Please add stalls before opening.",
                    confirmButtonColor: "#CD5C08"
                });
            </script>';
        } else {
            $success = $parkObj->deleteParkFirstOpening($_POST['park_id']);
            
            if ($success) {
                $redirectUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
                echo '
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Grand Opening Complete!",
                        html: `<div class="text-center">
                            <i class="fa-solid fa-calendar-check text-success fa-3x mb-3"></i>
                            <p class="fs-5">Your food park is now officially open to the public!</p>
                            <div class="alert alert-success text-start mt-3">
                                <i class="fa-solid fa-lightbulb me-2"></i>
                                Visitors can now visit your park and stalls in the app.
                            </div>
                        </div>`,
                        confirmButtonText: "Perfect!",
                        confirmButtonColor: "#28a745",
                        allowOutsideClick: false,
                        willClose: () => {
                            window.location.href = "' . $redirectUrl . '";
                        }
                    });
                });
                </script>';
            } else {
                $opening_err = "Failed to complete the opening process. Please try again.";
            }
        }
    }
}

?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .nav-main {
        padding: 20px 120px;
    }
    .btn {
        width: 150px;
    }
    .ip {
        color: #CD5C08;
        font-weight: bold;
    }
    .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        padding: 5px 10px !important;
        background-color: #f8f8f8 !important; 
        border: 1px solid #ccc !important; 
        padding: 0 10px !important;
        border-radius: 30px !important; 
        margin: 4px !important;
    }
    .select2-selection__choice__remove {
        font-size: 20px !important;
        margin-left: auto !important; 
        order: 2 !important; 
    }
    .select2-selection {
        padding: 10px !important;
    }
    .select2-selection__choice img {
        width: 25px !important;
        height: 25px !important;
        border-radius: 50% !important;
    }
    .select2-results__option {
        padding: 7px 15px !important;
        background-color: white !important;
        color: black !important;
    }
    .select2-results__option--highlighted {
        background-color: #e0e0e0 !important;
    }
    .disabled {
        color: #ccc !important;
        pointer-events: none;
    }
    .hover:hover {
        transform: scale(1.02);
        opacity: 0.8;
    }
    .checksub{
        transition: color 0.3s, transform 0.3s;
    }
    .checksub:hover {
        transform: scale(1.20);
    }
</style>

<main class="nav-main">
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
                            Swal.fire({icon: 'error', title: 'Image Too Large', text: 'Please select an image under 5MB.', confirmButtonColor: '#CD5C08'});
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
                
                <?php
                $isParkFirstTime = $parkObj->isParkFirstTime($park_id);
                if ($isParkFirstTime != 0) { ?>
                    <button class="variation-btn addrem m-2" style="background-color: #28a745; color: white;" data-bs-toggle="modal" data-bs-target="#grandOpeningModal">Open Park First Time</button>
                <?php } ?>
                
                <button class="variation-btn addrem" style="background-color: #dc3545; color: white;" data-bs-toggle="modal" data-bs-target="#deletepark">Delete Park</button></div>
            <h5 class="fw-bold m-0">Reports</h5>
            <span class="small text-muted">Resolve or reject customer's report on your stalls</span>
            <?php 
            $reports = $parkObj->getStallReports($park_id);
            if (empty($reports)) : ?>
                <div class="text-center py-5 mt-2 border rounded-2">
                    <p class="text-muted">No reports found.</p>
                </div>
            <?php else: ?>
                <?php 
                foreach ($reports as $report): 
                    if ($report['status'] == 'Pending') {
                        $statusIcon = '<i class="fa-solid fa-circle text-warning" style="font-size:9px;"></i>';
                    } elseif ($report['status'] == 'Resolved') {
                        $statusIcon = '<i class="fa-solid fa-circle text-success" style="font-size:9px;"></i>';
                    } elseif ($report['status'] == 'Rejected') {
                        $statusIcon = '<i class="fa-solid fa-circle text-danger" style="font-size:9px;"></i>';
                    }
                ?>
                <div class="d-flex align-items-center border gap-4 rounded-2 p-3 mt-2" id="report-<?= $report['id']; ?>">
                    <?= $statusIcon; ?>
                    <div class="d-flex gap-3 w-100">
                        <img src="<?= htmlspecialchars($report['profile_img']); ?>" width="40px" height="40px" style="border-radius:50%;">
                        <div>
                            <h6><?= htmlspecialchars($report['first_name'] . ' ' . $report['last_name']); ?> reported <?= htmlspecialchars($report['stall_name']); ?></h6>
                            <p class="text-muted m-0 my-1" style="font-size:12px;">"<?= htmlspecialchars($report['reason']); ?>"</p>
                            <span style="font-size:12px;"><?= htmlspecialchars($report['created_at']); ?></span>
                        </div>
                    </div>
                    <div class="d-flex gap-2" id="actions-<?= $report['id']; ?>">
                        <?php if ($report['status'] == 'Pending'): ?>
                            <form method="POST" action="" style="display:inline-block;">
                                <input type="hidden" name="report_id" value="<?= $report['id']; ?>">
                                <input type="hidden" name="action" value="resolve">
                                <button type="submit" name="report_update" style="background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-check text-success rename"></i>
                                </button>
                            </form>
                            <form method="POST" action="" style="display:inline-block;">
                                <input type="hidden" name="report_id" value="<?= $report['id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" name="report_update" style="background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-xmark text-danger rename"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <i class="fa-solid fa-check text-success rename disabled"></i>
                            <i class="fa-solid fa-xmark text-danger rename disabled"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
                                        Swal.fire({icon: 'warning', title: 'Missing Day', text: 'Please select at least one day.', confirmButtonColor: '#CD5C08'});
                                        return;
                                    }
                                    for (let entry of operatingHoursData) {
                                        for (let day of days) {
                                            if (entry.days.includes(day)) {
                                                Swal.fire({icon: 'warning', title: 'Duplicate Day', text: 'The day "' + day + '" has already been added.', confirmButtonColor: '#CD5C08'});
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

    <div class="modal fade" id="grandOpeningModal" tabindex="-1" aria-labelledby="grandOpeningLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="grandOpeningForm">
                <input type="hidden" name="grand_opening" value="1">
                <input type="hidden" name="park_id" value="<?= $park_id ?>">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="text-center">
                            <h4 class="fw-bold mb-4"><i class="fa-solid fa-calendar-check text-success"></i> Grand Opening</h4>
                            <div class="mb-4">
                                <span>You are about to officially open this food park for the first time.</span><br>
                                <strong>This will activate the park and make it available to the public.</strong>
                            </div>
                            
                            <div class="alert alert-success border-start border-5 border-success">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-circle-check me-3 fs-4"></i>
                                    <div>
                                        <h5 class="alert-heading mb-2">Ready for Grand Opening?</h5>
                                        <p class="mb-1">Before opening, please confirm:</p>
                                        <ul class="mb-0 ps-3">
                                            <li>All stalls are properly set up</li>
                                            <li>Stall products/menus are complete</li>
                                            <li>Operating hours are configured</li>
                                            <li>Park information is accurate</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-center gap-2 py-3">
                                <button type="button" class="btn btn-secondary flex-grow-1" data-bs-dismiss="modal">
                                    Not Yet
                                </button>
                                <button type="submit" class="btn btn-success flex-grow-1">
                                    <i class="fa-solid fa-flag me-2"></i>Open Park Now
                                </button>
                            </div>
                        </div>
                        <?php if(isset($opening_err)) { echo '<p class="text-danger mt-2">'.$opening_err.'</p>'; } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php
        $stalls = $parkObj->getStalls($park_id); 
        if (empty($stalls)) {
            echo '<div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
                     Add or invite your food stalls here. 
                  </div>';
        } else {
    ?>
    <div class="row row-cols-1 row-cols-md-2  row-cols-lg-3 g-3">
        <?php
            date_default_timezone_set('Asia/Manila'); 
            $currentDay = date('l'); 
            $currentTime = date('H:i');
            foreach ($stalls as $stall) { 
        ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="<?= $stall['logo'] ?>" class="card-img-top" alt="Stall Logo">
                            <div class="position-absolute d-flex gap-2 smaction">
                                <i class="fa-solid fa-pen-to-square" onclick="window.location.href='editpage.php?id=<?= urlencode(encrypt($stall['id'])) ?>&source=managestall'"></i>
                                <i class="fa-solid fa-trash-can" data-bs-toggle="modal" data-bs-target="#deletestall"></i>
                            </div>
                        </div>
                        <div class="card-body px-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php 
                                            $stall_categories = explode(',', $stall['stall_categories']); 
                                            foreach ($stall_categories as $index => $category) { 
                                        ?>
                                            <p class="card-text text-muted m-0"><?= trim($category) ?></p>
                                            <?php if ($index !== array_key_last($stall_categories)) { ?>
                                                <span class="dot text-muted"></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <h5 class="card-title my-2 fw-bold"><?= $stall['name'] ?></h5>
                                    <p class="card-text text-muted m-0"><?= $stall['description'] ?></p>
                                </div>
                                
                                <div class="dropdown">
                                    <button class="dropdown-toggle bg-white border-0 m-0 p-0 d-flex align-items-center" 
                                            id="dropdownMenuButton<?= $stall['id'] ?>" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-circle <?= ($stall['status'] === 'Available') ? 'text-success' : 'text-danger' ?> me-2" 
                                        style="font-size: 9px;"></i>
                                        <span class="pe-3"><?= $stall['status'] ?></span>
                                    </button>
                                    <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuButton<?= $stall['id'] ?>">
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="stallId" value="<?= $stall['id'] ?>">
                                            <input type="hidden" name="status" value="Available">
                                            <button type="submit" class="dropdown-item d-flex align-items-center">
                                                <i class="fa-solid fa-circle text-success me-2" style="font-size: 9px;"></i>
                                                <span>Available</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="stallId" value="<?= $stall['id'] ?>">
                                            <input type="hidden" name="status" value="Unavailable">
                                            <button type="submit" class="dropdown-item d-flex align-items-center">
                                                <i class="fa-solid fa-circle text-danger me-2" style="font-size: 9px;"></i>
                                                <span>Unavailable</span>
                                            </button>
                                        </form>

                                    </div>
                                </div>

                            </div>
                            <div class="accordion accordion-flush" id="accCol<?= $stall['id'] ?>">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#col1flu<?= $stall['id'] ?>1" aria-expanded="false" aria-controls="col1flu<?= $stall['id'] ?>1">
                                            Contact Information
                                        </button>
                                    </h2>
                                    <div id="col1flu<?= $stall['id'] ?>1" class="accordion-collapse collapse" data-bs-parent="#accCol<?= $stall['id'] ?>">
                                        <div class="accordion-body p-0 mb-3 small">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span>Email</span>
                                                <span><?= $stall['email'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span>Phone</span>
                                                <span class="text-muted"><?= $stall['phone'] ?? 'N/A' ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Website</span>
                                                <span class="text-muted"><?= $stall['website'] ?? 'N/A' ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#col1flu<?= $stall['id'] ?>2" aria-expanded="false" aria-controls="col1flu<?= $stall['id'] ?>2">
                                            Opening Hours
                                        </button>
                                    </h2>
                                    <div id="col1flu<?= $stall['id'] ?>2" class="accordion-collapse collapse" data-bs-parent="#accCol<?= $stall['id'] ?>">
                                        <div class="accordion-body p-0 mb-3 small">
                                            <?= !empty($stall['stall_operating_hours']) ? str_replace('; ', '<br>', $stall['stall_operating_hours']) : 'Not available' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#col1flu<?= $stall['id'] ?>3" aria-expanded="false" aria-controls="col1flu<?= $stall['id'] ?>3">
                                            Payment Methods
                                        </button>
                                    </h2>
                                    <div id="col1flu<?= $stall['id'] ?>3" class="accordion-collapse collapse" data-bs-parent="#accCol<?= $stall['id'] ?>">
                                        <div class="accordion-body p-0 mb-3 small">
                                            <ul>
                                                <?php 
                                                    if (!empty($stall['stall_payment_methods'])) {
                                                        $payment_methods = explode(', ', $stall['stall_payment_methods']);
                                                        foreach ($payment_methods as $method) {
                                                            echo "<li class='mb-2'>$method</li>";
                                                        }
                                                    } else {
                                                        echo "<li>No payment methods available</li>";
                                                    }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="owner mt-1 py-2 d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-3 align-items-center">
                                    <img src="<?= $stall['profile_img'] ?: 'assets/images/user.jpg' ?>" alt="Owner Profile">
                                    <div>
                                        <span class="fw-bold"><?= $stall['owner_name'] ?></span>
                                        <p class="m-0"><?= $stall['email'] ?></p>
                                    </div>
                                </div>
                                <i class="text-muted">Owner</i>
                            </div>
                        </div> 
                    </div>
                </div>
            <?php } ?>
    </div> 
    <?php } ?>
    <br><br><br><br><br>
</main>

<!-- Hidden input to store stall ID for delete operation -->
<input type="hidden" id="stall_id_to_delete" value="">

<div class="modal fade" id="invitestall" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0">
                <div class="d-flex gap-3 align-items-center">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Stall Owners</h1>
                    <i class="fa-regular fa-circle-question m-0" data-bs-toggle="tooltip" data-bs-placement="right" title="An email will be sent to them with an invitaion link to register their stall under your food park. Once they complete the registration, their stall will be added to your food park."></i>
                    <script>
                        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
                    </script>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <select id="emailSelect" name="emails[]" multiple="multiple" style="width: 100%;"></select>
                </div>
                <h6 class=" mb-3 mt-3 mt-1">People in your food park</h6>
                <?php
                    $owner = $parkObj->getParkOwner($park_id);
                    if($owner) {
                ?>
                <div class="owner mt-1 py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="<?= $owner['profile_img'] ?>" alt="">
                        <div>
                            <span class="fw-bold"><?= $owner['owner_name'] ?> (you)</span>
                            <p class="m-0"><?= $owner['email'] ?></p>
                        </div>
                    </div>
                    <i class="text-muted small mr-1">Park Owner</i>
                </div>
                <?php
                    }
                ?>
                <?php
                    $owners = $parkObj->getStallOwners($park_id);
                    if (!empty($owners)) {
                        foreach ($owners as $owner) {
                ?>
                <div class="owner mt-1 py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="<?= $owner['profile_img'] ?>" alt="">
                        <div>
                            <span class="fw-bold"><?= $owner['owner_name'] ?></span>
                            <p class="m-0"><?= $owner['email'] ?></p>
                        </div>
                    </div>
                    <i class="text-muted small mr-1">Stall Owner</i>
                </div>
                <?php
                        }
                    }
                ?>
            </div>
            <div class="modal-footer pt-0 border-0">
                <button type="button" class="btn btn-primary send p-2" id="createStallBtn">Create Stall Page</button>
                <button type="button" class="btn btn-primary send p-2" id="sendInviteBtn">Send Invitation Link</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deletestall" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Stall</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this stall?</p>
            </div>
            <div class="modal-footer pt-0 border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteStall">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function validateOperatingHours() {
        let operatingHoursInput = document.getElementById('operating_hours').value;
        try {
            let operatingHours = JSON.parse(operatingHoursInput);
            if (!Array.isArray(operatingHours) || operatingHours.length === 0) {
                Swal.fire({icon: 'warning', title: 'Missing Schedule', text: 'Please add at least one operating hour schedule.', confirmButtonColor: '#CD5C08'});
                return false;
            }
        } catch (e) {
            Swal.fire({icon: 'error', title: 'Invalid Data', text: 'Invalid operating hours data. Please try again.', confirmButtonColor: '#CD5C08'});
            return false;
        }
        return true;
    }
</script>

<script>
    $(document).ready(function () {
        $("#emailSelect").select2({
            placeholder: "Add emails to send invitation link",
            allowClear: true,
            templateResult: formatEmailWithImage, 
            templateSelection: formatSelectedEmail, 
            dropdownParent: $("#invitestall"), 
            ajax: {
                url: "fetch_emails.php",
                type: "GET",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return { 
                        search: params.term,
                        park_id: "<?= urlencode(encrypt($park_id)) ?>"
                    };
                },
                processResults: function (data) {
                    return { 
                        results: data.map(user => ({
                            id: user.id,  
                            text: user.email,
                            profile_img: user.profile_img
                        }))
                    };
                },
                cache: true
            }
        });

        function formatEmailWithImage(item) {
            if (!item.id) return item.text; 

            let imgSrc = item.profile_img ? item.profile_img : "default-avatar.png"; 
            return $(
                `<div style="display: flex; align-items: center;">
                    <img src="${imgSrc}" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">
                    <span>${item.text}</span>
                </div>`
            );
        }

        function formatSelectedEmail(item) {
            if (!item.id) return item.text;

            let imgSrc = item.profile_img ? item.profile_img : "default-avatar.png"; 
            return $(
                `<div style="display: flex; align-items: center; gap: 5px;">
                    <img src="${imgSrc}" style="width: 20px; height: 20px; border-radius: 50%;">
                    <span>${item.text}</span>
                </div>`
            );
        }

        $('#invitestall').on('shown.bs.modal', function () {
            $("#emailSelect").val(null).trigger("change"); 
        });

        $("#createStallBtn").on("click", function () {
            let selectedUsers = $("#emailSelect").select2("data"); 
            let parkId = "<?php echo $_SESSION['current_park_id']; ?>";

            if (!selectedUsers || selectedUsers.length === 0) {
                Swal.fire({
                    title: 'Hold up! ⚠️',
                    text: 'You haven’t selected any users yet. Let’s fix that and try again!',
                    icon: 'error',
                    confirmButtonText: 'Alright, selecting now!'
                });
                return;
            }

            Swal.fire({
                title: 'Sending invitations...',
                text: 'Processing your request',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let userData = selectedUsers.map(user => ({
                id: user.id,
                email: user.text
            }));

            $.ajax({
                url: 'email/process_stall_invitations.php',
                type: 'POST',
                data: {
                    users: userData,
                    park_id: parkId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.urls && response.urls.length > 0) {
                            let successCount = 0;
                            let successfulUrls = [];
                            
                            response.urls.forEach(function(item) {
                                if (item.success && item.url) {
                                    successfulUrls.push({
                                        email: item.email,
                                        url: item.url
                                    });
                                    successCount++;
                                }
                            });
                            
                            function openUrlsSequentially(urls, index) {
                                if (index < urls.length) {
                                    window.open(urls[index].url, "_blank");
                                    setTimeout(function() {
                                        openUrlsSequentially(urls, index + 1);
                                    }, 500);
                                }
                            }
                            
                            if (successfulUrls.length > 0) {
                                openUrlsSequentially(successfulUrls, 0);
                            }
                            
                            let failureCount = response.urls.length - successCount;
                            
                            if (failureCount > 0) {
                                let detailsHtml = '<ul class="text-start">';
                                response.urls.forEach(function(item) {
                                    if (!item.success) {
                                        detailsHtml += `<li>${item.email}: ${item.message}</li>`;
                                    }
                                });
                                detailsHtml += '</ul>';
                                
                                Swal.fire({
                                    title: 'Some URLs could not be generated',
                                    html: `${failureCount} URL(s) could not be generated due to errors.<br>${detailsHtml}`,
                                    icon: 'warning',
                                    confirmButtonText: 'Ok, I understand'
                                });
                            } else {
                                let urlListHtml = '';
                                if (successfulUrls.length > 1) {
                                    urlListHtml = '<p>If not all URLs opened automatically, you can click on them below:</p><ul class="text-start">';
                                    successfulUrls.forEach(function(item) {
                                        urlListHtml += `<li><a href="${item.url}" target="_blank">${item.email}</a></li>`;
                                    });
                                    urlListHtml += '</ul>';
                                }
                                
                                Swal.fire({
                                    title: 'Success! 🎉',
                                    html: `All URLs have been generated successfully!${urlListHtml}`,
                                    icon: 'success',
                                    confirmButtonText: 'Great!'
                                });
                            }
                            
                            $('#invitestall').modal('hide');
                        } else {
                            Swal.fire({
                                title: 'No URLs generated',
                                text: 'No URLs were generated from the server.',
                                icon: 'warning',
                                confirmButtonText: 'Ok'
                            });
                        }
                    } else {
                        Swal.fire({
                            title: 'Oops! 😕',
                            text: response.message || 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'Try again'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Connection Error',
                        text: 'Unable to connect to the server. Please check your connection and try again.',
                        icon: 'error',
                        confirmButtonText: 'Try again'
                    });
                }
            });
        });

        $('#sendInviteBtn').click(function () {
            var selectedEmails = $('#emailSelect').val();
            if (selectedEmails.length === 0) {
                Swal.fire({
                    title: 'Hey, wait a sec! ✋',
                    text: 'You need to pick at least one email before we can send the invites.',
                    icon: 'error',
                    confirmButtonText: 'Got it, picking now!'
                });

                return;
            }

            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
            $(this).prop('disabled', true);
            
            var $button = $(this);

            $.ajax({
                url: './email/send_invite.php',
                type: 'POST',
                data: { emails: selectedEmails },
                dataType: 'json',
                success: function (response) {
                    $button.html('Send Invitation Link');
                    $button.prop('disabled', false);
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            title: "Success! 🎉",
                            text: "All invitation links were sent without a hitch! Check your inboxes. 📩",
                            icon: "success"
                        });

                        
                        $("#emailSelect").val(null).trigger("change");
                        
                        $('#invitestall').modal('hide');
                    } else if (response.status === 'warning') {
                        var message = 'Some invitations could not be sent:\n';
                        response.results.forEach(function(result) {
                            message += '- ' + result.email + ': ' + result.message + '\n';
                        });
                        Swal.fire({
                            title: 'Yikes! 😬',
                            text: message || 'Something went wrong. Let’s try that one more time!',
                            icon: 'error',
                            confirmButtonText: 'Got it!'
                        });
                    } else {
                        Swal.fire({
                            title: 'Oops! 🚧',
                            text: 'Some invitations didn’t make it through. Maybe the internet gremlins are at it again? Give it another shot!',
                            icon: 'error',
                            confirmButtonText: 'Alright, I’ll try again!'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    $button.html('Send Invitation Link');
                    $button.prop('disabled', false);
                    
                    Swal.fire({
                        title: 'Uh-oh! 😟',
                        text: 'Something went wrong while sending the invitations. Maybe the internet took a coffee break? Try again! Error: ' + error,
                        icon: 'error',
                        confirmButtonText: 'Got it, I’ll try again!'
                    })
                }
            });
        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteIcons = document.querySelectorAll('.fa-trash-can');
        
        deleteIcons.forEach(icon => {
            icon.addEventListener('click', function() {
                const card = this.closest('.card');
                const editIconOnclick = card.querySelector('.fa-pen-to-square').getAttribute('onclick');
                const stallId = editIconOnclick.split('id=')[1].replace(/['")\s;]/g, '');
                
                console.log('Stall ID to delete:', stallId); 
                
                document.getElementById('stall_id_to_delete').value = stallId;
            });
        });
        
        document.getElementById('confirmDeleteStall').addEventListener('click', function() {
            const stallId = document.getElementById('stall_id_to_delete').value;
            
            console.log('Confirming delete of stall ID:', stallId); 
            
            fetch('delete_stall.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'stall_id=' + stallId
            })
            .then(response => response.json())
            .then(data => {
                console.log('Delete response:', data); 
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('deletestall'));
                modal.hide();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Stall has been deleted successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to delete stall. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    });
</script>

<?php 
    include_once 'footer.php'; 
?>