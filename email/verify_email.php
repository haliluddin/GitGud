<?php
    session_start();
    include_once '../links.php'; 
    include_once '../secondheader.php';
    require_once __DIR__ . '/../classes/db.class.php';
    require_once 'verification_token.class.php';
    $verificationObj = new Verification();
    $userObj = new User();
    $email = '';

    require_once 'resend_token.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="../assets/css/media.css?v=<?php echo time(); ?>">
<style>
    .wholewhole{
        height: calc(100vh - 65.61px); 
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<main class="wholewhole">
<div class="container d-flex justify-content-center align-items-start pt-4" style="min-height: calc(100vh - 100px);">
    <div class="bg-white border p-3 p-md-4 w-100 w-sm-75 w-md-50 rounded-2 text-center">
        <img src="../assets/images/email.jpg" width="120" height="120" class="img-fluid mb-3">
        <h4 class="mb-3">Verify your email address</h4>
        <p class="mb-3">
            A verification email has been sent to your email 
            <span style="color: #CD5C08;"><?= $email ?></span>.<br>
            Please check your inbox and click the link to complete your account registration.
        </p>
        <div class="w-100 w-md-75 mx-auto mb-4">
            <span class="small">
                Didn’t get the email? Wait 5 minutes, then click below to resend.
            </span>
        </div>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?>" />
            <input type="hidden" name="first_name" value="<?= $user['first_name'] ?>" />
            <input type="hidden" name="email" value="<?= $email ?>" />
            <input type="submit" class="btn btn-primary w-100 w-md-50 p-2 rounded-5 resever" value="Resend Email" /> 
        </form>
    </div>
</div>


    <!-- Resend Modal 
    <div class="modal fade" id="resend" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="width: 800px;">
            <div class="modal-content p-5">
                <div class="modal-header p-0 border-0">
                    <h5 class="modal-title fw-bold fs-3">Resend Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 my-4 text-center">
                    <i class="fa-regular fa-envelope env mb-3"></i>
                    <p>The email address we have for you is <span class="fw-bold" id="email"><?= $email ?></span>. If you haven't received our message, please click the button below.</p>
                </div>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?>" />
                    <input type="hidden" name="first_name" value="<?= $user['first_name'] ?>" />
                    <input type="hidden" name="email" value="<?= $email ?>" />
                    <input type="submit" class="btn btn-primary" value="Resend" /> 
                </form>
            </div>
        </div>
    </div> -->

    <!-- Change Modal -->
     <!-- !!! REMOVED !!! -->
    <!-- <div class="modal fade" id="change" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="width: 800px;">
            <div class="modal-content p-5">
                <div class="modal-header p-0 border-0">
                    <h5 class="modal-title fw-bold fs-3">Change Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 my-4 text-center">
                    <i class="fa-regular fa-envelope env mb-3"></i>
                    <p>The email address we have for you is <span class="fw-bold">email</span>. If you want to change it, please provide us with your new email and we'll send a new verification link.</p>
                    <div class="input-group m-0">
                        <input type="email" name="email" id="new_email" placeholder="Type your new email address here" value="" required/>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="changeButton">Send</button> 
            </div>
        </div>
    </div> -->
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../assets/js/resend_token.js"></script>