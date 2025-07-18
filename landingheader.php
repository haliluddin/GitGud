<?php
    session_start();
    require_once __DIR__ . '/classes/db.class.php';

    $userObj = new User();
    $user = null;

    if (isset($_SESSION['user'])) {
        $user = $userObj->getUser($_SESSION['user']['id']);
    }
?>
<style>
   .dropdown img{
        height: 30px !important;
        width: 30px !important;
   }
</style>

<div class="bottom d-flex justify-content-between align-items-center py-3">
    <a href="index.php"><img src="assets/images/logo.png" alt="GitGud"></a>

    <?php if ($user): ?>
        <div class="dropdown position-relative">
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="text-decoration-none text-dark d-flex align-items-center gap-2 py-1 px-4 rounded-3 inupbtn">
                <img src="<?= htmlspecialchars($user['profile_img'] ?? '') ?>" alt="Profile Image" width="30" height="30" class="rounded-circle"> 
                <span><?= htmlspecialchars($user['full_name'] ?? '') ?></span>
            </a>
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="lhuser text-decoration-none text-dark">
                <i class="fa-regular fa-user"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-center p-0 mt-2" style="box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                <?php if ($user && $user['role'] === 'Admin'): ?>
                    <li>
                        <a class="dropdown-item" href="(admin)manageaccount.php">
                            <i class="fa-solid fa-gear me-2"></i> Manage Accounts
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a class="dropdown-item" href="account.php?source=landing">
                        <i class="fa-solid fa-user me-2"></i> Account
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="./logout.php">
                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout
                    </a>
                </li>
            </ul>

        </div>
    <?php else: ?>
        <div>
            <button onclick="window.location.href='signin.php';" class="rounded-3 bg-white py-1 px-3 inupbtn">Sign in</button>
            <button onclick="window.location.href='signup.php';" class="rounded-3 py-1 px-3 text-white border-0 inupbtn" style="background: #CD5C08;">Sign Up</button>
        </div>
        <i class="fa-solid fa-arrow-right-to-bracket lhuser" onclick="window.location.href='signin.php';"></i>
    <?php endif; ?>
</div>
