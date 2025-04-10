<?php
session_start();
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';

$adminObj = new Admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report_id = $_POST['report_id'];
    
    // Delete report from database
    $result = $adminObj->deleteReport($report_id);
    
    if ($result) {
        // Success
        echo "<script>
            alert('Report deleted successfully!');
            window.location.href = '(admin)manageaccount.php#reports';
        </script>";
    } else {
        // Error
        echo "<script>
            alert('Failed to delete report. Please try again.');
            window.location.href = '(admin)manageaccount.php#reports';
        </script>";
    }
} else {
    // Invalid request
    header('Location: (admin)manageaccount.php#reports');
    exit();
}
?>
