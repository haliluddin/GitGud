<?php 
    include_once 'links.php'; 
?>
<style>
    .bottom{
        padding: 10px 120px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
    a{
        color: #CD5C08;
        text-decoration: none;
    }
</style>
<div class="bottom d-flex justify-content-between align-items-center">
    <a href="/GitGudPark/index.php"><i class="fa-solid fa-arrow-left-from-bracket"></i> 
        <?php
            $currentDir = dirname($_SERVER['SCRIPT_NAME']);
            if (strpos($currentDir, 'email') !== false) {
                echo '<img src="../assets/images/logo.png" alt="GitGud">';
            } else {
                echo '<img src="assets/images/logo.png" alt="GitGud">';
            }
        ?>
    </a>
    <?php 
        if (isset($_SESSION['user']['id']))
            echo '<a href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>';
        else
            echo '<a href="/GitGudPark/index.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Back</a>';
    ?>
</div> 