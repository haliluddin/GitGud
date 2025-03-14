<?php
include_once 'links.php'; 
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/park.class.php';

$user = null;
$nav_links = [];

if (isset($_SESSION['user'])) {
    $userObj = new User();
    $user = $userObj->getUser($_SESSION['user']['id']);

    if (isset($_SESSION['current_park_id'])) {
        $park_id = $_SESSION['current_park_id'];
        $park_name = $_SESSION['current_park_name'];

        $parkObj = new Park();
        $is_food_park_owner = $parkObj->isOwner($_SESSION['user']['id'], $park_id);
        $is_stall_owner = $parkObj->isStallOwner($_SESSION['user']['id'], $park_id);

        $nav_links = [
            'account.php' => 'ACCOUNT',
            'purchase.php'  => 'PURCHASE'
        ];

        if ($is_stall_owner) {
            $nav_links += [
                'orders.php'     => 'ORDERS',
                'managemenu.php' => 'MANAGE MENU',
                'sales.php'      => 'SALES'
            ];
        }

        if ($is_food_park_owner) {
            $nav_links += [
                'managestall.php' => 'MANAGE STALL',
                'dashboard.php'   => 'DASHBOARD'
            ];
        }
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .indicator {
        display: flex;
        justify-content: center;
        gap: 50px;
        padding: 0 120px;
        border-bottom: 1px solid #ccc;
    }
    .indicator a {
        color: #bbbbbb;
        padding: 18px 5px;
        font-size: 15px;
        text-decoration: none;
        transition: color 0.2s, border-bottom 0.2s;
    }
    .indicator a:hover {
        color: black;
    }
    .indicator a.active {
        color: black;
        border-bottom: 2px solid black;
    }
</style>

<nav class="indicator">
    <?php foreach ($nav_links as $link => $label): ?>
        <a href="<?= $link; ?>" class="<?= ($current_page == $link ? 'active' : ''); ?>">
            <?= $label; ?>
        </a>
    <?php endforeach; ?>
</nav>
