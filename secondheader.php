<?php 
    include_once 'links.php'; 
?>
<style>
    a{
        color: #CD5C08;
        text-decoration: none;
    }
</style>
<div class="bottom d-flex justify-content-between align-items-center py-3">
    <a href="index.php"><i class="fa-solid fa-arrow-left-from-bracket"></i> 
        <?php
            $currentDir = dirname($_SERVER['SCRIPT_NAME']);
            if (strpos($currentDir, 'email') !== false) {
                echo '<img src="../assets/images/logo.png" alt="GitGud" width="150">';
            } else {
                echo '<img src="assets/images/logo.png" alt="GitGud">';
            }
        ?>
    </a>
    <?php 
        if (isset($_SESSION['user']['id']))
            echo '<a href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>';
        else
            echo '<a href="../index.php"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>';
    ?>
</div> 