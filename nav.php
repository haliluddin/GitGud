<?php
include_once 'links.php';
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/park.class.php';

$user = null;
$nav_links = [];
$is_food_park_owner = false;
$is_stall_owner = false;

if (isset($_SESSION['user'])) {
    $userObj = new User();
    $user = $userObj->getUser($_SESSION['user']['id']);

    if (isset($_SESSION['current_park_id'])) {
        $park_id = $_SESSION['current_park_id'];
        $park_name = $_SESSION['current_park_name'];

        $parkObj = new Park();
        $is_food_park_owner = $parkObj->isOwner($_SESSION['user']['id'], $park_id);
        $is_stall_owner = $parkObj->isStallOwner($_SESSION['user']['id'], $park_id);

        // Grant admin full access
        if ($user && $user['role'] === 'Admin') {
            $is_food_park_owner = true;
            $is_stall_owner = true;
        }

        $nav_links = [
            'account.php'  => 'ACCOUNT',
            'purchase.php' => 'PURCHASE'
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
                'managestall.php' => 'MANAGE STALL'
            ];
        }
    }
}

$current_page = basename($_SERVER['PHP_SELF']);

// Retrieve all stalls for the current park for use in the admin stall-selection modal
if (isset($parkObj) && isset($park_id)) {
    $allStalls = $parkObj->getStalls($park_id);
} else {
    $allStalls = [];
}
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
    /* Modal Styling (if needed) */
    .stall-card a {
        text-decoration: none;
        color: inherit;
    }
</style>

<nav class="indicator">
    <?php foreach ($nav_links as $link => $label): ?>
        <a href="<?= $link; ?>" class="<?= ($current_page == $link ? 'active' : ''); ?>">
            <?= $label; ?>
        </a>
    <?php endforeach; ?>
</nav>

<!-- Admin Stall Selection Modal -->
<?php if ($user && $user['role'] === 'Admin' && !empty($allStalls)): ?>
<div class="modal fade" id="stallSelectModal" tabindex="-1" role="dialog" aria-labelledby="stallSelectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select a Stall</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="stallsContainer" class="modal-body">
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <?php foreach ($allStalls as $stall): ?>
                        <div class="col stall-card">
                            <a href="#" data-stall-id="<?= $stall['id'] ?>">
                                <div class="card">
                                    <img src="<?= $stall['logo'] ?>" class="card-img-top" alt="Stall Logo">
                                    <div class="card-body">
                                        <div class="d-flex gap-2 align-items-center">
                                            <?php 
                                            $stall_categories = explode(',', $stall['stall_categories']);
                                            foreach ($stall_categories as $index => $category): ?>
                                                <p class="card-text text-muted m-0"><?= trim($category) ?></p>
                                                <?php if ($index !== array_key_last($stall_categories)): ?>
                                                    <span class="dot text-muted"></span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <h5 class="card-title my-2"><?= $stall['name'] ?></h5>
                                        <p class="card-text text-muted m-0"><?= $stall['description'] ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="assets/js/dropdown.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var isAdmin = <?php echo ($user && $user['role'] === 'Admin') ? 'true' : 'false'; ?>;
    if (isAdmin) {
        // Create a Bootstrap modal instance for stall selection
        const stallModal = new bootstrap.Modal(document.getElementById('stallSelectModal'));
        let targetUrl = '';
        
        // Intercept clicks on nav links that require a stall selection
        document.querySelectorAll('.indicator a').forEach(function(link) {
            const href = link.getAttribute('href');
            if (['orders.php', 'managemenu.php', 'sales.php'].includes(href)) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    targetUrl = href;
                    stallModal.show();
                });
            }
        });
        
        // When an admin clicks a stall card, redirect to targetUrl with the stall_id
        document.querySelectorAll('.stall-card a').forEach(function(stallLink) {
            stallLink.addEventListener('click', function(e) {
                e.preventDefault();
                const stallId = this.getAttribute('data-stall-id');
                if (stallId) {
                    window.location.href = targetUrl + '?stall_id=' + stallId;
                }
            });
        });
    }
});
</script>
