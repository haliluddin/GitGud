<?php  
    include_once 'header.php';

    // if (isset($user) && $user['role'] !== 'Park Owner')
    //     exit(header("Location: index.php"));

    include_once 'links.php'; 
    include_once 'nav.php';
    include_once 'bootstrap.php'; 
    include_once 'modals.php'; 
?>

<style>
     main{
        padding: 20px 120px;
    }
    .btn{
        width: 150px;
    }
    .ip{
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

    .select2-results__option{
        padding: 7px 15px !important;
        background-color: white !important;
        color: black !important;
    }
    .select2-results__option--highlighted{
        background-color: #e0e0e0 !important;
    }
</style>

<main>
    <div class="d-flex justify-content-end mb-3">
        <button class="addpro" type="button" data-bs-toggle="modal" data-bs-target="#invitestall">+ Add Stall</button>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-3">
        <?php
            $stalls = $parkObj->getStalls($park_id); 

            date_default_timezone_set('Asia/Manila'); 
            $currentDay = date('l'); 
            $currentTime = date('H:i');

            foreach ($stalls as $stall) { 
                $isOpen = false;
                $operatingHours = explode('; ', $stall['stall_operating_hours']); 

                foreach ($operatingHours as $hours) {
                    list($days, $timeRange) = explode('<br>', $hours); 
                    $daysArray = array_map('trim', explode(',', $days)); 

                    if (in_array($currentDay, $daysArray)) { 
                        list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
                        
                        $openTime24 = date('H:i', strtotime($openTime));
                        $closeTime24 = date('H:i', strtotime($closeTime));

                        if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                            $isOpen = true;
                            break;
                        }
                    }
                }
                ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="<?= $stall['logo'] ?>" class="card-img-top" alt="Stall Logo">
                            <div class="position-absolute d-flex gap-2 smaction">
                                <!--<i class="fa-solid fa-sack-dollar" onclick="window.location.href='rent.php';"></i>-->
                                <i class="fa-solid fa-pen-to-square" onclick="window.location.href='editpage.php?id=<?= $stall['id'] ?>';"></i>
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

                                <!-- Display OPEN or CLOSE based on operating hours -->
                                <?php if ($isOpen) { ?>
                                    <div class="smopen">
                                        <i class="fa-solid fa-clock"></i>
                                        <span>OPEN</span>
                                    </div>
                                <?php } else { ?>
                                    <div class="smclose">
                                        <i class="fa-solid fa-door-closed"></i>
                                        <span>CLOSE</span>
                                    </div>
                                <?php } ?>
                            </div>
                                
                                <!-- Accordion for Details -->
                                <div class="accordion accordion-flush" id="accCol<?= $stall['id'] ?>">
                                    <!-- Contact Information -->
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

                                    <!-- Opening Hours -->
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

                                    <!-- Payment Method -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#col1flu<?= $stall['id'] ?>3" aria-expanded="false" aria-controls="col1flu<?= $stall['id'] ?>3">
                                                Payment Methods
                                            </button>
                                        </h2>
                                        <div id="col1flu<?= $stall['id'] ?>3" class="accordion-collapse collapse" data-bs-parent="#accCol<?= $stall['id'] ?>">
                                            <div class="accordion-body p-0 mb-3 small">
                                                <ul>
                                                    <?php if (!empty($stall['stall_payment_methods'])) {
                                                        $payment_methods = explode(', ', $stall['stall_payment_methods']);
                                                        foreach ($payment_methods as $method) {
                                                            echo "<li class='mb-2'>$method</li>";
                                                        }
                                                    } else {
                                                        echo "<li>No payment methods available</li>";
                                                    } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Owner Information -->
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
    <br><br><br><br><br>
</main>

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
                <!--<div class="owner mt-1 py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="assets/images/profile.jpg" alt="">
                        <div>
                            <span class="fw-bold">Naila Haliluddin</span>
                            <p class="m-0">example@gmail.com</p>
                        </div>
                    </div>
                    <i class="text-muted small mr-1">Stall Owner</i>
                </div>-->
            </div>
            <div class="modal-footer pt-0 border-0">
                <button type="button" class="btn btn-primary send p-2" id="createStallBtn">Create Stall Page</button>
                <button type="button" class="btn btn-primary send p-2" id="sendInviteBtn">Send Invitation Link</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        // Open a new window/tab for each selected user
        selectedUsers.forEach(function (user) {
            let userId = user.id; // Fetch user ID from selection
            let userEmail = encodeURIComponent(user.text); // Fetch user email

            window.open(
                `stallregistration.php?owner_email=${userEmail}&owner_id=${userId}&park_id=${parkId}`, 
                "_blank"
            );
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

<?php 
    include_once 'footer.php'; 
?>