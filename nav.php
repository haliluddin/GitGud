<?php
    include_once 'links.php'; 
    require_once __DIR__ . '/classes/db.class.php';
    $userObj = new User();

    if (isset($_SESSION['user']['id'])) {
        if ($userObj->isVerified($_SESSION['user']['id']) == 0) {
            header('Location: ./email/verify_email.php');
            exit();
        }

        $user = $userObj->getUser($_SESSION['user']['id']);
    } else {
        header('Location: ./signin.php');
        exit();
    }
    
    $user = $userObj->getUser($_SESSION['user']['id']);
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    .indicator {
        display: flex;
        justify-content: center;
        gap: 50px;
        padding: 0 120px;
    }
    .indicator a {
        color: #bbbbbb;
        padding: 18px 5px;
        font-size: 15px;
        text-decoration: none;
    }
    .indicator a:hover {
        color: black;
    }
    .indicator a.active {
        color: black;
        border-bottom: 2px black solid;
    }
</style>
<nav class="indicator border-bottom">
    <?php
        switch ($user['role']) {
            case 'Customer':
                echo '<a href="purchase.php" class="' . ($current_page == "purchase.php" ? "active" : "") . '">PURCHASE</a>';
                echo '<a href="favorites.php" class="' . ($current_page == "favorites.php" ? "active" : "") . '">FAVORITES</a>';
                echo '<a href="account.php" class="' . ($current_page == "account.php" ? "active" : "") . '">ACCOUNT</a>';
                break;
            case 'Stall Owner':
                echo '<a href="purchase.php" class="' . ($current_page == "purchase.php" ? "active" : "") . '">PURCHASE</a>';
                echo '<a href="favorites.php" class="' . ($current_page == "favorites.php" ? "active" : "") . '">FAVORITES</a>';
                echo '<a href="account.php" class="' . ($current_page == "account.php" ? "active" : "") . '">ACCOUNT</a>';
                echo '<a href="orders.php" class="' . ($current_page == "orders.php" ? "active" : "") . '">ORDERS</a>';
                echo '<a href="managemenu.php" class="' . ($current_page == "managemenu.php" ? "active" : "") . '">MANAGE MENU</a>';
                echo '<a href="stallpage.php" class="' . ($current_page == "stallpage.php" ? "active" : "") . '">STALL PAGE</a>';
                echo '<a href="sales.php" class="' . ($current_page == "sales.php" ? "active" : "") . '">SALES</a>';
                break;
            case 'Park Owner':
                echo '<a href="purchase.php" class="' . ($current_page == "purchase.php" ? "active" : "") . '">PURCHASE</a>';
                echo '<a href="favorites.php" class="' . ($current_page == "favorites.php" ? "active" : "") . '">FAVORITES</a>';
                echo '<a href="account.php" class="' . ($current_page == "account.php" ? "active" : "") . '">ACCOUNT</a>';
                echo '<a href="managestall.php" class="' . ($current_page == "managestall.php" ? "active" : "") . '">MANAGE STALL</a>';
                echo '<a href="dashboard.php" class="' . ($current_page == "dashboard.php" ? "active" : "") . '">DASHBOARD</a>';
                echo '<a href="centralized.php" class="' . ($current_page == "centralized.php" ? "active" : "") . '">CENTRALIZED</a>';
                break;
        }
    ?>
</nav>

