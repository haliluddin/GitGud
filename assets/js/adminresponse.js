$(document).ready(function () {
    var businessId, action, statusCell;
    
    $('.approve-btn').click(function () {
        businessId = $(this).data('id');
        action = 'approve';
        $('#approvalActionText').text('approve');
        statusCell = $(this).closest('tr').find('td.status-cell');
        $('#approvalConfirmModal').modal('show');
    });
    
    $('.deny-btn').click(function () {
        businessId = $(this).data('id');
        action = 'deny';
        statusCell = $(this).closest('tr').find('td.status-cell');
        $('#rejectReasonModal input[type="checkbox"]').prop('checked', false);
        $('#customRejectionReason').val(''); // Clear the textarea
        $('#rejectReasonModal').modal('show');
    });
    
    $('#confirmApproval').click(function () {
        $.ajax({
            url: 'adminresponse.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ business_id: businessId, action: action }),
            success: function (response) {
                if (response.success) {
                    statusCell.html('<span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">Accepted</span>');
                    var row = statusCell.closest('tr');
                    row.find('.approve-btn, .deny-btn').prop('disabled', true);
                    Swal.fire({icon: 'success', title: 'Action Complete', text: response.message, confirmButtonColor: '#CD5C08'});
                    location.reload();
                } else {
                    Swal.fire({icon: 'error', title: 'Error', text: 'Error: ' + response.message, confirmButtonColor: '#CD5C08'});
                }
            },
            error: function () {
                Swal.fire({icon: 'error', title: 'Request Error', text: 'Error processing request.', confirmButtonColor: '#CD5C08'});
            }
        });
        $('#approvalConfirmModal').modal('hide');
    });
    
    $('#saveRejection').click(function () {
        var reasons = [];
        
        // Get selected checkboxes
        $('#rejectReasonModal input[type="checkbox"]:checked').each(function() {
            var label = $(this).siblings('label').text().trim();
            reasons.push(label);
        });
        
        // Get custom rejection reason from textarea
        var customReason = $('#customRejectionReason').val().trim();
        
        // Build the rejection reason string
        var rejection_reason = reasons.join(', ');
        
        // Add custom reason if provided
        if (customReason) {
            if (rejection_reason) {
                rejection_reason += ', Additional details: ' + customReason;
            } else {
                rejection_reason = 'Additional details: ' + customReason;
            }
        }
        
        $.ajax({
            url: 'adminresponse.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ business_id: businessId, action: action, rejection_reason: rejection_reason }),
            success: function (response) {
                if (response.success) {
                    statusCell.html('<span class="small rounded-5 text-danger border border-danger p-1 border-2 fw-bold">Rejected</span>');
                    var row = statusCell.closest('tr');
                    row.find('.approve-btn, .deny-btn').prop('disabled', true);
                    Swal.fire({icon: 'success', title: 'Action Complete', text: response.message, confirmButtonColor: '#CD5C08'});
                } else {
                    Swal.fire({icon: 'error', title: 'Error', text: 'Error: ' + response.message, confirmButtonColor: '#CD5C08'});
                }
            },
            error: function () {
                Swal.fire({icon: 'error', title: 'Request Error', text: 'Error processing request.', confirmButtonColor: '#CD5C08'});
            }
        });
        $('#rejectReasonModal').modal('hide');
    });
});
