<?php
    include_once './links.php';
    include_once './secondheader.php';
    require_once './classes/db.php';
    require_once './classes/encdec.class.php';

    $error = '';
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $token = urldecode($_POST['token']);
        $user_id = urldecode($_POST['user_id']);

        // Decrypt and validate token
        $decrypted_token = decrypt($token);
        $decrypted_user_id = decrypt($user_id);

        $db = new Database();
        $query = $db->connect()->prepare("SELECT * FROM password_resets WHERE user_id = :user_id AND token = :token AND expires_at > NOW()");
        $query->execute([':user_id' => $decrypted_user_id, ':token' => $decrypted_token]);
        $reset = $query->fetch();

        if (!$reset) {
            $error = 'Invalid or expired reset token';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters';
        } else {
            // Update password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = $db->connect()->prepare("UPDATE users SET password = :password WHERE id = :user_id");
            $query->execute([':password' => $hashed_password, ':user_id' => $decrypted_user_id]);

            // Delete used reset token
            $query = $db->connect()->prepare("DELETE FROM password_resets WHERE user_id = :user_id");
            $query->execute([':user_id' => $decrypted_user_id]);

            $success = true;
        }
    } else {
        // Validate GET parameters
        if (!isset($_GET['token']) || !isset($_GET['id'])) {
            header('Location: signin.php');
            exit();
        }
    }
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password</title>
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
    <?php if ($success): ?>
        <div class="form text-center">
            <h4 class="fw-bold m-0">Password Changed</h4>
            <div class="text-center my-5">
                <i class="fa-solid fa-check-circle" style="font-size: 70px; color: #28a745;"></i>
                <p class="m-0 mt-3 mb-5">Your password has been successfully changed.</p>
                <button class="button" onclick="window.location.href='signin.php';">Sign In</button>
            </div>
        </div>
    <?php else: ?>
        <form class="form" method="POST">
            <div class="d-flex align-items-center gap-3">
                <i class="fa-solid fa-arrow-left rename text-dark fs-6" onclick="window.location.href='signin.php';"></i>
                <h4 class="fw-bold m-0">Change Password</h4>
            </div>
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="input-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" placeholder="Enter new password" required />
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required />
            </div>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
            <div class="btns-group d-block text-center">
                <input type="submit" value="Change Password" class="button">
            </div>
        </form>
    <?php endif; ?>
</main>

<?php
    include_once './footer.php';
?>
