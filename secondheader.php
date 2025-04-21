<?php 
    include_once 'links.php'; 
?>
<style>
    a {
        color: #CD5C08;
        text-decoration: none;
    }
    .navbar-logo img {
        max-height: 50px;
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="navbar-logo">
            <a href="../index.php" class="d-flex align-items-center">
                <i class="fa-solid fa-arrow-left-from-bracket me-2"></i>
                <?php
                    $currentDir = dirname($_SERVER['SCRIPT_NAME']);
                    if (strpos($currentDir, 'email') !== false) {
                        echo '<img src="../assets/images/logo.png" alt="GitGud" width="150">';
                    } else {
                        echo '<img src="assets/images/logo.png" alt="GitGud" width="150">';
                    }
                ?>
            </a>
        </div>
        <div class="logout-link">
            <?php 
                if (isset($_SESSION['user']['id']))
                    echo '<a href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>';
                else
                    echo '<a href="../index.php"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>';
            ?>
        </div>
    </div>
</div>
