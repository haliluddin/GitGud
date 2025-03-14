<?php
    include_once 'links.php';
?>
<div class="d-flex justify-content-around align-items-center">
    <a href="#"><img src="assets/images/logo.png" alt=""></a>
    <span class=""><i class="fa-solid fa-location-crosshairs me-3"></i>Location of the food park here ...</span>
    <div>
        <a href=""><i class="fa-solid fa-bell"></i></a>
        <a href=""><i class="fa-solid fa-cart-shopping"></i></a>
        <a href="">
            <img src="food1.jpg">
            <span>username</span>
        </a>
        <div></div>
    </div>
</div>


<nav>
    <div class="top d-flex justify-content-between">
        <span class="text-white"><i class="fa-solid fa-location-crosshairs me-3"></i><?= htmlspecialchars($park_name) ?></span>
        <ul>
            <?php if ($user): ?>
                <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a></li>
                <li><a href="notification.php"><i class="fa-solid fa-bell"></i> Notifications</a></li>
                <li>
                    <div class="dropdown">
                        <a href="javascript:void(0)" onclick="toggleDropdown()">
                            <img src="<?php echo $user['profile_img'] ?? 'assets/images/profile.jpg'; ?>" alt="Profile Image"> 
                            <span><?php echo $user['full_name']; ?></span>
                        </a>
                        <div class="dropdown-content" id="dropdownMenu">
                            <?php foreach ($nav_links as $link => $data): ?>
                                <a href="<?php echo $link; ?>">
                                    <i class="<?php echo $data['icon']; ?>"></i> <?php echo $data['label']; ?>
                                </a>
                            <?php endforeach; ?>
                            <a href="./logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
                        </div>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="signin.php">Sign In</a></li>
                <span style="color:white;">|</span>
                <li><a href="signup.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="bottom">
        <a href="index.php">
            <img src="assets/images/logo.png" alt="">
        </a>
        <div>
            <ul class="headnav">
                <li><a href="park.php">Home</a></li>
            </ul>
        </div>
    </div>
</nav>