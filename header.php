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

    if ($user && $user['role'] === 'Admin') {
        $is_food_park_owner = true;
        $is_stall_owner = true;
    }

    if (isset($_SESSION['current_park_id'])) {
        $park_id = $_SESSION['current_park_id'];
        $park_name = $_SESSION['current_park_name'];

        if (!$user || $user['role'] !== 'Admin') {
            $is_food_park_owner = $parkObj->isOwner($user_id, $park_id);
            $is_stall_owner = $parkObj->isStallOwner($user_id, $park_id);
        }
    }

    $nav_links = [
        'account.php' => ['label' => 'Account', 'icon' => 'fa-solid fa-user'],
        'purchase.php' => ['label' => 'Purchase', 'icon' => 'fa-solid fa-shopping-bag'],
    ];

    if ($is_stall_owner) {
        $nav_links += [
            'orders.php' => ['label' => 'Orders', 'icon' => 'fa-solid fa-receipt'],
            'managemenu.php' => ['label' => 'Manage Menu', 'icon' => 'fa-solid fa-utensils'],
            'sales.php' => ['label' => 'Sales', 'icon' => 'fa-solid fa-chart-line'],
        ];
    }

    if ($is_food_park_owner) {
        $nav_links += [
            'managestall.php' => ['label' => 'Manage Stall', 'icon' => 'fa-solid fa-cogs'],
        ];
    }
} else {
    if (isset($_SESSION['current_park_id'])) {
        $park_id = $_SESSION['current_park_id'];
        $park_name = $_SESSION['current_park_name'];
    }
}

if (isset($park_id))
    $allStalls = $parkObj->getStalls($park_id);

?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    .parkhead:hover{
        cursor: pointer;
        background-color: #f4f4f4;
        padding: 10px;
        border-radius: 5px;
    }
</style>
<div class="border-bottom main-head" style="padding: 15px 120px;">
    <div class="d-flex justify-content-between align-items-center">
        <a href="index.php"><img src="assets/images/logo.png" width="150" class="mh-logo"></a>
        <div class="d-flex gap-2 align-items-center parkhead" onclick="window.location.href='park.php'">
            <i class="fa-solid fa-location-crosshairs"></i>
            <h6 class="m-0"><?= htmlspecialchars($park_name) ?></h6>
        </div>
        <div class="d-flex gap-3 align-items-center nav">
            <?php if ($user): ?>
                <a href="cart.php" class="rounded-5 cbu"><i class="fa-solid fa-cart-shopping"></i></a>
                <a href="notification.php" class="rounded-5 cbu"><i class="fa-solid fa-bell"></i></a>
                <div class="dropdown">
                    <a href="javascript:void(0)" onclick="toggleDropdown()" class="d-flex gap-2 align-items-center pro-tm">
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
                    <div>
                        <button onclick="window.location.href='signin.php';" class="rounded-3 bg-white py-1 px-3 inupbtn">Sign in</button>
                        <button onclick="window.location.href='signup.php';" class="rounded-3 py-1 px-3 text-white border-0 inupbtn" style="background: #CD5C08;">Sign Up</button>
                    </div>
                    <i class="fa-solid fa-arrow-right-to-bracket lhuser" onclick="window.location.href='signin.php';"></i>
                <?php endif; ?>
        </div>
    </div>
    <div class="parkhead-tm" onclick="window.location.href='park.php'">
        <div class="d-flex gap-1 align-items-center justify-content-center mt-3">
            <i class="fa-solid fa-location-crosshairs"></i>
            <h6 class="m-0"><?= htmlspecialchars($park_name) ?></h6>
        </div>
    </div>
    
</div>

<div class="modal fade bd-example-modal-lg" id="stallSelectModal" tabindex="-1" role="dialog" aria-labelledby="stallSelectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select a Stall</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="stallsModalContainer" class="modal-body">
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <?php if(isset($allStalls) && !empty($allStalls)): ?>
                        <?php foreach ($allStalls as $singleStall): ?>
                            <div class="col stall-card">
                                <a href="#" class="card-link text-decoration-none bg-white" data-stall-id="<?= $singleStall['id'] ?>">
                                    <div class="card">
                                        <img src="<?= $singleStall['logo'] ?>" class="card-img-top" alt="Stall Logo">
                                        <div class="card-body">
                                            <div class="d-flex gap-2 align-items-center">
                                                <?php 
                                                    $stall_categories = explode(',', $singleStall['stall_categories']); 
                                                    foreach ($stall_categories as $index => $category): 
                                                ?>
                                                    <p class="card-text text-muted m-0"><?= trim($category) ?></p>
                                                    <?php if ($index !== array_key_last($stall_categories)): ?>
                                                        <span class="dot text-muted"></span>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                            <h5 class="card-title my-2"><?= $singleStall['name'] ?></h5>
                                            <p class="card-text text-muted m-0"><?= $singleStall['description'] ?></p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col">
                            <p>No stalls available.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/navigation.js"></script>
<script src="assets/js/dropdown.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var isAdmin = <?php echo ($user && $user['role'] === 'Admin') ? 'true' : 'false'; ?>;
    
    if (isAdmin) {
        const stallModal = new bootstrap.Modal(document.getElementById('stallSelectModal'));
        let targetUrl = '';
    
        document.querySelectorAll('.dropdown-content a').forEach(function(link) {
            const href = link.getAttribute('href');
            if (['orders.php', 'managemenu.php', 'sales.php'].includes(href)) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    targetUrl = href;
                    stallModal.show();
                });
            }
        });
    
        document.querySelectorAll('.stall-card a').forEach(function(stallLink) {
            stallLink.addEventListener('click', function(e) {
                if (typeof targetUrl !== 'undefined' && targetUrl !== '') {
                    e.preventDefault();
                    const stallId = this.getAttribute('data-stall-id');
                    if (stallId) {
                        window.location.href = targetUrl + '?stall_id=' + stallId;
                    }
                }
            });
        });

    }
});
</script>
