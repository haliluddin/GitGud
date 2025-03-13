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
    <!-- Reset Password Form -->
    <form id="resetPasswordForm" class="form" method="POST">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-arrow-left rename text-dark fs-6" onclick="window.location.href='signin.php';"></i>
            <h4 class="fw-bold m-0">Reset Password</h4>
        </div>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" placeholder="Enter your email" required />
        </div>
        <div class="btns-group d-block text-center">
            <input type="button" value="Next" class="button" onclick="showVerificationForm()">
        </div><br>
    </form>

    <!-- Verification Message -->
    <div id="verificationMessage" class="form d-none">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-arrow-left rename text-dark fs-6" onclick="window.location.href='signin.php';"></i>
            <h4 class="fw-bold m-0">Reset Password</h4>
        </div>
        <div class="text-center my-5">
            <i class="fa-regular fa-envelope" style="font-size: 70px;"></i>
            <p class="m-0 mt-3 mb-5">A verification email has been sent to this email address <span style="color:#CD5C08;">hnailataji@gmail.com</span>. Please verify it.</p>
            <button class="button" onclick="window.location.href='signin.php';">OK</button>
        </div>
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
