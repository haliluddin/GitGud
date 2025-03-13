<?php
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            if (isset($_SESSION['user']['id'])) {
                if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
                    header('Location: ../index.php');
                    exit();
                }
    
                $user = $userObj->getUser($_SESSION['user']['id']);
                $email = $user['email'];
            }
    
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING);
            $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
            if ($user_id && $first_name && $email) {
                $result = $verificationObj->sendVerificationEmail($user_id, $email, $first_name);
                
                if ($result === true) {
                    echo "<script>alert('Verification email has been resent successfully!');</script>";
                } elseif (isset($result['message']) && $result['message'] === "verified") {
                    echo "<script>alert('Email is already verified.');</script>";
                } elseif (isset($result['message']) && $result['message'] === "cooldown") {
                    $minutes = floor($result['cd'] / 60);
                    $seconds = $result['cd'] % 60;
                    echo "<script>alert('Please wait for {$minutes} minutes and {$seconds} seconds before resending the verification email.');</script>";
                } else {
                    echo "ERROR: " . $result;
                    echo "<script>alert('Failed to resend verification email. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('Invalid request. All fields are required.');</script>";
            }
            break;
    
        case 'GET':
            if (isset($_SESSION['user']['id'])) {
                if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
                    header('Location: ../index.php');
                    exit();
                }
    
                $user = $userObj->getUser($_SESSION['user']['id']);
                $email = $user['email'];
            } else {
                header('Location: ../signin.php');
                exit();
            }
            break;
    }