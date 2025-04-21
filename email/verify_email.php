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
    <div class="bg-white border p-5 w-50 rounded-2 text-center">
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
            <input type="submit" class="btn btn-primary w-50 p-3 rounded-5 resever" value="Resend Verification Email" /> 
        </form>
    </div>

</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../assets/js/resend_token.js"></script>