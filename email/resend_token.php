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
                $result = $verificationObj->sendVerificationEmail($user_id, $email, $first_name, true);
                
                if ($result === true) {
                    echo "<script>Swal.fire({icon: 'success', title: 'Email Sent', text: 'Verification email resent to your inbox!', confirmButtonColor: '#CD5C08'});</script>";
                } elseif (isset($result['message']) && $result['message'] === "verified") {
                    echo "<script>Swal.fire({icon: 'info', title: 'Already Verified', text: 'Your email is already verified.', confirmButtonColor: '#CD5C08'});</script>";
                } elseif (isset($result['message']) && $result['message'] === "cooldown") {
                    $minutes = floor($result['cd'] / 60);
                    $seconds = $result['cd'] % 60;
                    echo "<script>Swal.fire({icon: 'warning', title: 'Please Wait', text: 'Try again in {$minutes} minutes and {$seconds} seconds.', confirmButtonColor: '#CD5C08'});</script>";
                } else {
                    echo "ERROR: " . $result;
                    echo "<script>Swal.fire({icon: 'error', title: 'Resend Failed', text: 'Could not resend the verification email. Try again.', confirmButtonColor: '#CD5C08'});</script>";
                }
            } else {
                echo "<script>Swal.fire({icon: 'error', title: 'Invalid Request', text: 'Please fill in all required fields.', confirmButtonColor: '#CD5C08'});</script>";
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