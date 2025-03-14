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
    .nav a{
        text-decoration: none;
    }
    .cbu{
        background-color: #CD5C08;
        color: white;
        height: 40px;
        width: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform 0.2s ease-in-out;
    }
    .cbu:hover{
        transform: scale(1.20);
    }
</style>
<div class="d-flex justify-content-between align-items-center border-bottom" style="padding: 20px 120px;">
    <a href="park.php"><img src="assets/images/logo.png" width="150"></a>
    <div class="d-flex gap-2 align-items-center">
        <i class="fa-solid fa-location-crosshairs"></i>
        <h5 class="m-0"><?= htmlspecialchars($park_name) ?></h5>
    </div>
    <div class="d-flex gap-3 align-items-center nav">
        <?php if ($user): ?>
            <a href="cart.php" class="rounded-5 cbu"><i class="fa-solid fa-cart-shopping"></i></a>
            <a href="notification.php" class="rounded-5 cbu"><i class="fa-solid fa-bell"></i></a>
            <div class="dropdown">
                <a href="javascript:void(0)" onclick="toggleDropdown()" class="d-flex gap-2 align-items-center">
                    <img height="40" width="40" class="rounded-5" src="<?php echo $user['profile_img'] ?? 'assets/images/profile.jpg'; ?>" alt="Profile Image"> 
                    <i class="fa-solid fa-chevron-down text-muted"></i>
                </a>
                <div class="dropdown-content" id="dropdownMenu">
                    <?php foreach ($nav_links as $link => $data): ?>
                        <a href="<?php echo $link; ?>">
                            <i class="<?php echo $data['icon']; ?> me-1"></i> <?php echo $data['label']; ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="./logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="signin.php">Sign In</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/navigation.js"></script>
<script src="assets/js/dropdown.js"></script>
