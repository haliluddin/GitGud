<?php
    include_once './links.php';     include_once './secondheader.php';
    require_once './email/password_reset.class.php';

    $error = '';
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address';
        } else {
            // Get user details from database
            $db = new Database();
            $query = $db->connect()->prepare("SELECT id, first_name FROM users WHERE email = :email");
            $query->execute([':email' => $email]);
            $user = $query->fetch();

            if ($user) {
                $passwordReset = new PasswordReset();
                $token = uniqid();
                $sent = $passwordReset->sendResetEmail($user['id'], $email, $user['first_name'], $token);

                if ($sent) {
                    $success = true;
                } else {
                    $error = 'Failed to send reset email. Please try again later.';
                }
            } else {
                $error = 'No account found with that email address';
            }
        }
    }
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<style>
    .whole {
        height: calc(100vh - 65.61px); 
    }
    .error-message {
        color: #dc3545;
        margin-bottom: 1rem;
    }
    .success-message {
        color: #28a745;
        margin-bottom: 1rem;
    }
</style>

<main class="whole d-flex justify-content-center align-items-center">
    <?php if (!$success): ?>
        <!-- Reset Password Form -->
        <form id="resetPasswordForm" class="form" method="POST">
            <div class="d-flex align-items-center gap-3">
                <i class="fa-solid fa-arrow-left rename text-dark fs-6" onclick="window.location.href='signin.php';"></i>
                <h4 class="fw-bold m-0">Reset Password</h4>
            </div>
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" placeholder="Enter your email" required />
            </div>
            <div class="btns-group d-block text-center">
                <input type="submit" value="Next" class="button">
            </div><br>
        </form>
    <?php else: ?>
        <!-- Verification Message -->
        <div id="verificationMessage" class="form">
            <div class="d-flex align-items-center gap-3">
                <i class="fa-solid fa-arrow-left rename text-dark fs-6" onclick="window.location.href='signin.php';"></i>
                <h4 class="fw-bold m-0">Reset Password</h4>
            </div>
            <div class="text-center my-5">
                <i class="fa-regular fa-envelope" style="font-size: 70px;"></i>
                <p class="m-0 mt-3 mb-5">A password reset email has been sent to <span style="color:#CD5C08;"><?php echo htmlspecialchars($email); ?></span>. Please check your inbox.</p>
                <button class="button" onclick="window.location.href='signin.php';">OK</button>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php
    include_once './footer.php'; 
?>
