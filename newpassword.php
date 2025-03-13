<?php
    include_once './links.php'; 
    include_once './secondheader.php'; 
?>

<title>Sign In</title>
<style>
    main {
        height: calc(100vh - 65.61px); 
    }
</style>
<main class="d-flex justify-content-center align-items-center">
    <!-- Set New Password Form -->
    <form id="resetPasswordForm" class="form" method="POST">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-arrow-left rename text-dark fs-6" onclick="window.location.href='signin.php';"></i>
            <h4 class="fw-bold m-0">Set your password</h4>
        </div>
        <p class="m-0 small mt-4 text-center">Create a new password for<br><span class="fw-bold">hnailataji@gmail.com</span></p>
        <div class="input-group my-4">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required/>
        </div>
        <div class="btns-group d-block text-center">
            <input type="button" value="Continue" class="button" onclick="showVerificationForm()">
        </div><br>
    </form>

    <!-- Verification Message -->
    <div id="verificationMessage" class="form d-none text-center">
        <h4 class="fw-bold mb-4 mt-2">Password reset successfully</h4>
        <i class="fa-solid fa-circle-check text-success" style="font-size: 60px;"></i>
        <p class="m-0 mt-3 mb-5">You have successfully reset the password for the account with the email hnailataji@gmail.com</p>
        <button class="button" onclick="window.location.href='signin.php';">OK</button>
    </div>
</main>

<script>
    function showVerificationForm() {
        document.getElementById('resetPasswordForm').classList.add('d-none');
        document.getElementById('verificationMessage').classList.remove('d-none');
    }
</script>
<?php
    include_once './footer.php'; 
?>
