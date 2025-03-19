$(document).ready(function () {
    var businessId, action, statusCell;

    $('.approve-btn, .deny-btn').click(function () {
        businessId = $(this).data('id');
        action = $(this).hasClass('approve-btn') ? 'approve' : 'deny';
        $('#actionText').text(action === 'approve' ? 'approve' : 'deny');
        statusCell = $(this).closest('tr').find('td.status-cell');
        $('#confirmModal').modal('show');
    });

    $('#confirmAction').click(function () {
        $.ajax({
            url: 'adminresponse.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ business_id: businessId, action: action }),
            success: function (response) {
                if (response.success) {
                    if (action === 'approve') {
                        statusCell.html('<span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">Accepted</span>');
                    } else if (action === 'deny') {
                        statusCell.html('<span class="small rounded-5 text-danger border border-danger p-1 border-2 fw-bold">Rejected</span>');
                    }
                    var row = statusCell.closest('tr');
                    row.find('.approve-btn, .deny-btn').prop('disabled', true);
                    alert(response.message);
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
