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
<style>
    .whole{
        height: calc(100vh - 65.61px); 
        display: flex;
        justify-content: center;
        align-items: center;
    }
 
</style>
<main>
    <div class="whole bg-white border p-5 w-50 rounded-2 text-center">
        <img src="../assets/images/email.jpg" width="150" height="150">
        <h2 class="my-4">Verify your email address</h2>
        <p>A verification email has been sent to your email <span style="color: #CD5C08;"><?= $email ?></span><br>Please check your email and click the link provided in the email to complete your account registration.</p>
        <div class="w-75 mx-auto my-4">
            <span class="small">If you do not receive the email within the next 5 minutes, use the button below to resend the verification email.</span>
        </div>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?>" />
            <input type="hidden" name="first_name" value="<?= $user['first_name'] ?>" />
            <input type="hidden" name="email" value="<?= $email ?>" />
            <input type="submit" class="btn btn-primary w-50 p-3 rounded-5" value="Resend Verification Email" /> 
        </form>
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