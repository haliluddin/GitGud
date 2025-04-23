// Deactivate User AJAX
$(document).ready(function () {
    $('#submitDeactivation').on('click', function (e) {
        e.preventDefault();
        var userId = $('#deactivateUserId').val();
        var duration = $('input[name="deactivation_duration"]:checked').val();
        var reason = $('#deactivation_reason').val();
        var $modal = $('#deactivateuser');
        var $deactivateBtn = $(this);
        $deactivateBtn.prop('disabled', true);

        if (!duration) {
            Swal.fire({
                title: 'Error',
                text: 'Please select a deactivation duration.',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            $deactivateBtn.prop('disabled', false);
            return;
        }
        if (!reason.trim()) {
            Swal.fire({
                title: 'Error',
                text: 'Please provide a reason for deactivation.',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            $deactivateBtn.prop('disabled', false);
            return;
        }

        $.ajax({
            url: 'classes/deactivate_user.php',
            type: 'POST',
            data: {
                deactivate_user_id: userId,
                deactivation_duration: duration,
                deactivation_reason: reason
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $modal.modal('hide');
                    Swal.fire({
                        title: 'Deactivated!',
                        text: 'User has been deactivated.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Failed to deactivate user.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while deactivating the user.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            },
            complete: function () {
                $deactivateBtn.prop('disabled', false);
            }
        });
    });
});
