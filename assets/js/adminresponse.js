$(document).ready(function () {
    var businessId, action;

    $('.approve-btn, .deny-btn').click(function () {
        businessId = $(this).data('id'); 
        action = $(this).hasClass('approve-btn') ? 'approve' : 'deny'; 
        
        $('#actionText').text(action === 'approve' ? 'approve' : 'deny');
        
        $('#confirmModal').modal('show');
    });

    $('#confirmAction').click(function () {
        $.ajax({
            url: 'adminresponse.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ business_id: businessId, action: action }),
            success: function (response) {
                console.log(response); 
                if (response.success) {
                    alert(response.message);
                    location.reload(); 
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function () {
                alert('Error processing request.');
            }
        });

        $('#confirmModal').modal('hide');
    });
});
