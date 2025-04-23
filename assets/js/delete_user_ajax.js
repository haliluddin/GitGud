// User Deletion AJAX
$(document).ready(function () {
    $('#deleteuser form').submit(function (e) {
        e.preventDefault();
        var userId = $('#modalUserId').val();
        var $modal = $('#deleteuser');
        var $deleteBtn = $(this).find('button[type="submit"]');
        $deleteBtn.prop('disabled', true);
        $.ajax({
            url: 'classes/delete_user.php',
            type: 'POST',
            data: { user_id: userId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $modal.modal('hide');
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'User has been deleted.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Failed to delete user.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while deleting the user.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            },
            complete: function () {
                $deleteBtn.prop('disabled', false);
            }
        });
    });
});
