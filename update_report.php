<?php
session_start();
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';

$adminObj = new Admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report_id = $_POST['report_id'];
    $status = htmlspecialchars(trim($_POST['status']));
    
    // Update report status in database
    $result = $adminObj->updateReportStatus($report_id, $status);
    
    if ($result) {
        // Success
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            Swal.fire({icon: 'success', title: 'Status Updated', text: 'The report status was updated.', confirmButtonColor: '#CD5C08'});
            window.location.href = '(admin)manageaccount.php#reports';
        </script>";
    } else {
        // Error
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            Swal.fire({icon: 'error', title: 'Update Failed', text: 'Couldn\'t update the report. Please try again.', confirmButtonColor: '#CD5C08'});
            window.location.href = '(admin)manageaccount.php#reports';
        </script>";
    }
} else {
    // Invalid request
    header('Location: (admin)manageaccount.php#reports');
    exit();
}
?>
