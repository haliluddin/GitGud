$(document).ready(function () {
    var businessId, action;

    // Approve or Deny button click handler
    $('.approve-btn, .deny-btn').click(function () {
        businessId = $(this).data('id'); // Get the business ID
        action = $(this).hasClass('approve-btn') ? 'approve' : 'deny'; // Determine the action
        
        // Update modal text dynamically
        $('#actionText').text(action === 'approve' ? 'approve' : 'deny');
        
        // Show the modal
        $('#confirmModal').modal('show');
    });

    // Confirm button click handler in the modal
    $('#confirmAction').click(function () {
        // Send AJAX request
        $.ajax({
            url: 'adminresponse.php', // Update with the correct path
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ business_id: businessId, action: action }),
            success: function (response) {
                console.log(response); // Log response for debugging
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Reload the page or update the UI
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function () {
                alert('Error processing request.');
            }
        });

        // Hide the modal after confirming
        $('#confirmModal').modal('hide');
    });
});
