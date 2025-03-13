<?php   
session_start();
include_once 'links.php'; 
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/park.class.php';

$user = null;
$is_food_park_owner = false;
$is_stall_owner = false;
$nav_links = [];

$userObj = new User();
$parkObj = new Park();

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $user = $userObj->getUser($user_id);

    if (isset($_SESSION['current_park_id'])) {
        $park_id = $_SESSION['current_park_id'];
        $park_name = $_SESSION['current_park_name'];
        $is_food_park_owner = $parkObj->isOwner($user_id, $park_id);
        $is_stall_owner = $parkObj->isStallOwner($user_id, $park_id);
    }

    $nav_links = [
        'account.php' => ['label' => 'Account', 'icon' => 'fa-solid fa-user'],
        'favorites.php' => ['label' => 'Favorites', 'icon' => 'fa-solid fa-heart'],
        'purchase.php' => ['label' => 'Purchase', 'icon' => 'fa-solid fa-shopping-bag'],
    ];

    if ($is_stall_owner) {
        $nav_links += [
            'orders.php' => ['label' => 'Orders', 'icon' => 'fa-solid fa-receipt'],
            'managemenu.php' => ['label' => 'Manage Menu', 'icon' => 'fa-solid fa-utensils'],
            'sales.php' => ['label' => 'Sales', 'icon' => 'fa-solid fa-chart-line'],
            //'stallpage.php' => ['label' => 'Stall Page', 'icon' => 'fa-solid fa-store'],
        ];
    }

    if ($is_food_park_owner) {
        $nav_links += [
            'managestall.php' => ['label' => 'Manage Stall', 'icon' => 'fa-solid fa-cogs'],
            'dashboard.php' => ['label' => 'Dashboard', 'icon' => 'fa-solid fa-chart-bar'],
            //'centralized.php' => ['label' => 'Centralized', 'icon' => 'fa-solid fa-layer-group'],
        ];
    }
} else {
    if (isset($_SESSION['current_park_id'])) {
        $park_id = $_SESSION['current_park_id'];
    }
}
?>

<style>
    .headnav a.active,
    .headnav a.hover {
        border-bottom: 2px solid #CD5C08;
    }
</style>

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
            <form action="#" method="get">
                <input type="text" name="search" placeholder="Search">
                <button type="submit"><i class="fas fa-search fa-lg"></i></button>
            </form>
            <ul class="headnav">
                <li><a href="park.php">Home</a></li>
                <li><a href="popular.php">Popular</a></li>
                <li><a href="new.php">New</a></li>
                <li><a href="promotion.php">Promotion</a></li>
            </ul>
        </div>
    </div>
</nav>

<script src="assets/js/navigation.js"></script>
<script src="assets/js/dropdown.js"></script>
