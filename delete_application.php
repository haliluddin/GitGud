<?php
session_start();
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';

$adminObj = new Admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];
    
    // Delete application from database
    $result = $adminObj->deleteApplication($application_id);
    
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    if ($result) {
        // Success
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Application deleted successfully.',
                    confirmButtonColor: '#CD5C08'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '(admin)manageaccount.php#applications';
                    }
                });
            });
        </script>";
    } else {
        // Error
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Delete Failed',
                    text: 'Couldn\\'t delete the application. Please try again.',
                    confirmButtonColor: '#CD5C08'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '(admin)manageaccount.php#applications';
                    }
                });
            });
        </script>";
    }
} else {
    // Invalid request
    header('Location: (admin)manageaccount.php#applications');
    exit();
}
?>
