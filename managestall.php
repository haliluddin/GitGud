<?php  
    include_once 'header.php';
    include_once 'links.php'; 
    include_once 'nav.php';
    include_once 'bootstrap.php'; 
    include_once 'modals.php';
    require_once 'classes/encdec.class.php';
    $park = $parkObj->getPark($park_id);

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
    <div class="d-flex mb-3 align-items-center gap-3">
        <div class="py-2 px-3 rounded-2 border w-100 bg-white d-flex align-items-center justify-content-between" data-bs-toggle="offcanvas" data-bs-target="#foodparkbranch" aria-controls="foodparkbranch" style="cursor: pointer;">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= $park['business_logo'] ?>" width="50px" height="50px" class="rounded-5">
                <div>
                    <p class="m-0 fw-bold"><?= $park['business_name'] ?></p>
                    <span class="text-muted small"><?= $park['street_building_house'] ?>, <?= $park['barangay'] ?>, Zamboanga City</span>
                </div>
            </div>
            <i class="fa-solid fa-angle-down"></i>
        </div>
        <button class="addpro flex-shrink-0" type="button" data-bs-toggle="modal" data-bs-target="#invitestall">+ Add Stall</button>
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
    <div class="row row-cols-1 row-cols-md-3 g-3">
        <?php
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
                                <?php if ($isOpen) { ?>
                                    <div class="smopen">
                                        <i class="fa-solid fa-clock"></i>
                                        <span>OPEN</span>
                                    </div>
                                <?php } else { ?>
                                    <div class="smclose">
                                        <i class="fa-solid fa-door-closed"></i>
                                        <span>CLOSED</span>
                                    </div>
                                <?php } ?>
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

<script>
// Attach click event to all delete icons
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