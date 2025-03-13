<?php
    session_start();

    require_once 'verification_token.class.php';

    $verificationObj = new Verification();

    if (isset($_GET['token']) && isset($_GET['id'])) {
        $token = $_GET['token'];
        $user_id = $_GET['id'];

        $isVerified = $verificationObj->verifyEmail($token, $user_id);

        if ($isVerified === true) {
            echo "Email verified successfully!";
            header('Location: ../index.php');
            exit();
        } else if ($isVerified == "verified") {
            header('Location: ../index.php');
        } else {
            echo "Verification link has expired or is invalid.";
        }
    } else if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
        header('Location: ../index.php');
        exit();
    } 
    else {
        echo "No token provided.";
    }