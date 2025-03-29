<?php  
include_once 'links.php'; 
include_once 'header.php';
include_once 'nav.php';
include_once 'bootstrap.php'; 
include_once 'modals.php';
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

if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $stallId = $_POST['stallId'];
    $newStatus = $_POST['status'];
    $parkObj->updateStallStatus($stallId, $newStatus);
    header("Location: " . $_SERVER['PHP_SELF'] . "?park_id=" . urlencode($park_id));
    exit();
}

if (isset($_POST['report_update'])) {
    $report_id = $_POST['report_id'];
    $action = $_POST['action'];
    $newStatus = ($action === 'resolve') ? 'Resolved' : 'Rejected';
    $parkObj->updateStallReportStatus($report_id, $newStatus);
    header("Location: " . $_SERVER['PHP_SELF'] . "?park_id=" . urlencode($park_id) . "#reports");
    exit();
}
?>

<style>
    main {
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
                <div class="profile-picture" data-bs-toggle="modal" data-bs-target="#editfoodpark">
                    <img src="<?= $park['business_logo'] ?>" alt="Profile Picture" class="profile-img rounded-5">
                    <div class="camera-overlay">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                </div>
                <h4 class="fw-bold m-0 mb-1 mt-3"><?= $park['business_name'] ?></h4>
                <span class="text-muted mb-1"><?= $park['street_building_house'] ?>, <?= $park['barangay'] ?>, Zamboanga City, Philippines</span>
                <div class="d-flex gap-2 text-muted align-items-center justify-content-center mb-1">
                    <span><i class="fa-solid fa-envelope"></i> <?= $park['business_email'] ?></span>
                    <span class="dot"></span>
                    <span><i class="fa-solid fa-phone small"></i> +63<?= $park['business_phone'] ?></span>
                </div>
                <button class="variation-btn addrem m-2" data-bs-toggle="modal" data-bs-target="#editfoodpark">Edit Park</button>
                <button class="variation-btn addrem" data-bs-toggle="modal" data-bs-target="#deletepark">Delete Park</button>
            </div>
            <h5 class="fw-bold m-0">Reports</h5>
            <span class="small text-muted">Resolve or reject customer's report on your stalls</span>
            <?php 
            $reports = $parkObj->getStallReports($park_id);
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
                        <i class="fa-solid fa-check text-success rename update-status" data-report_id="<?= $report['id']; ?>" data-new_status="Resolved" style="cursor:pointer;"></i>
                        <i class="fa-solid fa-xmark text-danger rename update-status" data-report_id="<?= $report['id']; ?>" data-new_status="Rejected" style="cursor:pointer;"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-check text-success rename disabled"></i>
                        <i class="fa-solid fa-xmark text-danger rename disabled"></i>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    $(document).ready(function(){
        $('.update-status').click(function(){
            var report_id = $(this).data('report_id');
            var new_status = $(this).data('new_status');
            $.ajax({
                url: '',
                method: 'POST',
                dataType: 'json',
                data: { action: 'update_report_status', report_id: report_id, new_status: new_status },
                success: function(response) {
                    if(response.success) {
                        var container = $('#report-' + report_id);
                        if(new_status === 'Resolved') {
                            container.find('i.fa-solid.fa-circle').replaceWith('<i class="fa-solid fa-circle text-success" style="font-size:9px;"></i>');
                        } else if(new_status === 'Rejected') {
                            container.find('i.fa-solid.fa-circle').replaceWith('<i class="fa-solid fa-circle text-danger" style="font-size:9px;"></i>');
                        }
                        $('#actions-' + report_id).html('<i class="fa-solid fa-check text-success rename disabled" style="cursor:not-allowed;"></i><i class="fa-solid fa-xmark text-danger rename disabled" style="cursor:not-allowed;"></i>');
                    }
                }
            });
        });
    });
    </script>
    <?php
        $stalls = $parkObj->getStalls($park_id); 
        if (empty($stalls)) {
            echo '<div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
                     Add or invite your food stalls here. 
                  </div>';
        } else {
    ?>
    <div class="row row-cols-1 row-cols-md-3 g-3">
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
                                <i class="fa-solid fa-pen-to-square" onclick="window.location.href='editpage.php?id=<?= urlencode(encrypt($stall['id'])) ?>'"></i>
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
                                        style="font-size: 9px;" id="statusIcon<?= $stall['id'] ?>"></i>
                                        <span class="pe-3" id="statusText<?= $stall['id'] ?>"><?= $stall['status'] ?></span>
                                    </button>
                                    <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuButton<?= $stall['id'] ?>">
                                        <a class="dropdown-item update-status d-flex align-items-center" href="#" data-stallid="<?= $stall['id'] ?>" data-status="Available">
                                            <i class="fa-solid fa-circle text-success me-2" style="font-size: 9px;"></i>
                                            <span>Available</span>
                                        </a>
                                        <a class="dropdown-item update-status d-flex align-items-center" href="#" data-stallid="<?= $stall['id'] ?>" data-status="Unavailable">
                                            <i class="fa-solid fa-circle text-danger me-2" style="font-size: 9px;"></i>
                                            <span>Unavailable</span>
                                        </a>
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
    $(document).ready(function(){
        $('.update-status').on('click', function(e) {
            e.preventDefault();
            var newStatus = $(this).data('status');
            var stallId = $(this).data('stallid');
            
            $.ajax({
                url: '', 
                type: 'POST',
                dataType: 'json',
                data: { 
                    action: 'update_status', 
                    stallId: stallId, 
                    status: newStatus 
                },
                success: function(response) {
                    if(response.success){
                        location.reload();
                    } else {
                        console.error('Error updating status.');
                    }
                },
                error: function(){
                    console.error('There was an error processing your request.');
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#emailSelect").select2({
            placeholder: "Add emails to send invitation link",
            allowClear: true,
            templateResult: formatEmailWithImage, // For dropdown items
            templateSelection: formatSelectedEmail, // For selected items
            dropdownParent: $("#invitestall"), // Ensure it renders within the modal
            ajax: {
                url: "fetch_emails.php",
                type: "GET",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return { search: params.term };
                },
                processResults: function (data) {
                    return { 
                        results: data.map(user => ({
                            id: user.id,  // Use user ID instead of email as ID
                            text: user.email,
                            profile_img: user.profile_img
                        }))
                    };
                },
                cache: true
            }
        });

        // Format items in dropdown with an image
        function formatEmailWithImage(item) {
            if (!item.id) return item.text; // If no ID, show plain text

            let imgSrc = item.profile_img ? item.profile_img : "default-avatar.png"; // Fallback image
            return $(
                `<div style="display: flex; align-items: center;">
                    <img src="${imgSrc}" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">
                    <span>${item.text}</span>
                </div>`
            );
        }

        // Format the selected items inside the box
        function formatSelectedEmail(item) {
            if (!item.id) return item.text;

            let imgSrc = item.profile_img ? item.profile_img : "default-avatar.png"; // Fallback image
            return $(
                `<div style="display: flex; align-items: center; gap: 5px;">
                    <img src="${imgSrc}" style="width: 20px; height: 20px; border-radius: 50%;">
                    <span>${item.text}</span>
                </div>`
            );
        }

        $('#invitestall').on('shown.bs.modal', function () {
            $("#emailSelect").val(null).trigger("change"); // Reset selection
        });

        $("#createStallBtn").on("click", function () {
            let selectedUsers = $("#emailSelect").select2("data"); // Get selected user objects
            let parkId = "<?php echo $_SESSION['current_park_id']; ?>"; // Get park ID

            if (!selectedUsers || selectedUsers.length === 0) {
                Swal.fire({
                    title: 'Hold up! ⚠️',
                    text: 'You haven’t selected any users yet. Let’s fix that and try again!',
                    icon: 'error',
                    confirmButtonText: 'Alright, selecting now!'
                });
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Sending invitations...',
                text: 'Processing your request',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Prepare data for AJAX
            let userData = selectedUsers.map(user => ({
                id: user.id,
                email: user.text
            }));

            // Send AJAX request
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
                        // We have URLs for each user
                        if (response.urls && response.urls.length > 0) {
                            let successCount = 0;
                            let successfulUrls = [];
                            
                            // Collect successful URLs
                            response.urls.forEach(function(item) {
                                if (item.success && item.url) {
                                    successfulUrls.push({
                                        email: item.email,
                                        url: item.url
                                    });
                                    successCount++;
                                }
                            });
                            
                            // Function to open URLs with a delay to avoid popup blockers
                            function openUrlsSequentially(urls, index) {
                                if (index < urls.length) {
                                    window.open(urls[index].url, "_blank");
                                    setTimeout(function() {
                                        openUrlsSequentially(urls, index + 1);
                                    }, 500);
                                }
                            }
                            
                            // Try to open URLs sequentially
                            if (successfulUrls.length > 0) {
                                openUrlsSequentially(successfulUrls, 0);
                            }
                            
                            // Check if we have any failures to report
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
                                // Create HTML for manual URL opening in case automatic opening fails
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
                            
                            // Close the modal after processing
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

            // Show loading indicator
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
            $(this).prop('disabled', true);
            
            // Store button reference
            var $button = $(this);

            $.ajax({
                url: './email/send_invite.php',
                type: 'POST',
                data: { emails: selectedEmails },
                dataType: 'json',
                success: function (response) {
                    // Reset button state
                    $button.html('Send Invitation Link');
                    $button.prop('disabled', false);
                    
                    if (response.status === 'success') {
                        // Show success message
                        // alert('Invitation links sent successfully!');
                        Swal.fire({
                            title: "Success! 🎉",
                            text: "All invitation links were sent without a hitch! Check your inboxes. 📩",
                            icon: "success"
                        });

                        
                        // Reset the select2 dropdown
                        $("#emailSelect").val(null).trigger("change");
                        
                        // Close the modal
                        $('#invitestall').modal('hide');
                    } else if (response.status === 'warning') {
                        // Show warning message with details
                        var message = 'Some invitations could not be sent:\n';
                        response.results.forEach(function(result) {
                            message += '- ' + result.email + ': ' + result.message + '\n';
                        });
                        // alert(message);
                        Swal.fire({
                            title: 'Yikes! 😬',
                            text: message || 'Something went wrong. Let’s try that one more time!',
                            icon: 'error',
                            confirmButtonText: 'Got it!'
                        });
                    } else {
                        // Show error message
                        // alert('Failed to send some invitation links. Please try again.');
                        Swal.fire({
                            title: 'Oops! 🚧',
                            text: 'Some invitations didn’t make it through. Maybe the internet gremlins are at it again? Give it another shot!',
                            icon: 'error',
                            confirmButtonText: 'Alright, I’ll try again!'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    // Reset button state
                    $button.html('Send Invitation Link');
                    $button.prop('disabled', false);
                    
                    // Show error message
                    // alert('An error occurred while sending invitations: ' + error);
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
                // Get the stall ID from the edit icon in the same card
                // First, get the card parent element
                const card = this.closest('.card');
                // Get the edit icon's onclick attribute which contains the ID
                const editIconOnclick = card.querySelector('.fa-pen-to-square').getAttribute('onclick');
                // Extract just the ID number
                const stallId = editIconOnclick.split('id=')[1].replace(/['")\s;]/g, '');
                
                console.log('Stall ID to delete:', stallId); // Debug
                
                // Set the stall ID in the hidden input
                document.getElementById('stall_id_to_delete').value = stallId;
            });
        });
        
        // Handle delete confirmation
        document.getElementById('confirmDeleteStall').addEventListener('click', function() {
            const stallId = document.getElementById('stall_id_to_delete').value;
            
            console.log('Confirming delete of stall ID:', stallId); // Debug
            
            // Make an AJAX request to delete the stall
            fetch('delete_stall.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'stall_id=' + stallId
            })
            .then(response => response.json())
            .then(data => {
                console.log('Delete response:', data); // Debug
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deletestall'));
                modal.hide();
                
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Stall has been deleted successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reload the page
                        window.location.reload();
                    });
                } else {
                    // Show error message
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