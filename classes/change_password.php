<?php
session_start();
require_once __DIR__ . '/user.class.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['currentpassword'];
    $newPassword = $_POST['newpassword'];
    $retypePassword = $_POST['retypepassword'];
    $logoutOtherDevices = isset($_POST['logout_other_devices']) ? 1 : 0;

    if ($newPassword !== $retypePassword) {
        $_SESSION['error'] = "New passwords do not match.";
        header("Location: ../account.php{$source}");
        exit;
    }

    $user = new UserClass();
    $user->id = $_SESSION['user']['id'];

    $result = $user->changePassword($user->id, $currentPassword, $newPassword, $logoutOtherDevices);
    
    $source = isset($_POST['source']) && $_POST['source'] === 'landing'
    ? '?source=landing'
    : '';
    
    if ($result['success']) {
        $_SESSION['success'] = "Password changed successfully.";
        $_SESSION['user_session'] = $result['user_session'];
    } else {
        $_SESSION['error'] = $result['message'];
    }

    header("Location: ../account.php{$source}");
    exit;
}
?>
