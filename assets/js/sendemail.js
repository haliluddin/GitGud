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
            alert("Please select at least one user!");
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




});