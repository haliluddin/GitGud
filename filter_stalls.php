<?php
session_start();
include_once __DIR__ . '/classes/db.class.php';
include_once __DIR__ . '/classes/park.class.php';
include_once __DIR__ . '/classes/encdec.class.php';
include_once __DIR__ . '/classes/user.class.php';

if (isset($_GET['park_id'], $_GET['category_id'])) {
    $park_id    = intval($_GET['park_id']);
    $categoryId = intval($_GET['category_id']);
    
    // User permission checks
    $user = null;
    if (isset($_SESSION['user'])) {
        $userObj = new User();
        $user = $userObj->getUser($_SESSION['user']['id']);
    }
    
    $parkObj = new Park();

    $cat         = $parkObj->getCategoryById($categoryId);
    $categoryName = $cat['name'] ?? 'Unknown';

    $results = $parkObj->filterStallsByCategory($park_id, $categoryId);
    
    $popularStalls = $parkObj->getPopularStalls($park_id);
    $promoStalls   = $parkObj->getPromoStalls($park_id);
    $newProdStalls = $parkObj->getNewProductStalls($park_id);
    
    $popularIds = array_column($popularStalls, 'id');
    $promoIds   = array_column($promoStalls, 'id');
    $newProdIds = array_column($newProdStalls, 'id');
    
    date_default_timezone_set('Asia/Manila'); 
    $currentDay  = date('l'); 
    $currentTime = date('H:i');
    
    function getNextOpening($operatingHoursArray) {
        if (!empty($operatingHoursArray)) {
            $first = $operatingHoursArray[0];
            if (strpos($first, '<br>') !== false) {
                list($days, $timeRange) = explode('<br>', $first);
                $daysArray = array_map('trim', explode(',', $days));
                $day = !empty($daysArray) ? $daysArray[0] : 'Unknown';
                list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
                return $day . ' ' . date('g:i A', strtotime($openTime));
            }
        }
        return "N/A";
    }
    
    $park = $parkObj->getPark($park_id);
    $parkOperatingHours = [];
    if (!empty($park['operating_hours'])) {
        $parkOperatingHours = explode('; ', $park['operating_hours']);
    }
    
    $parkIsOpen = false;
    foreach ($parkOperatingHours as $hours) {
        if (strpos($hours, '<br>') !== false) {
            list($days, $timeRange) = explode('<br>', $hours);
            $daysArray = array_map('trim', explode(',', $days));
            if (in_array($currentDay, $daysArray)) {
                list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
                $openTime24  = date('H:i', strtotime($openTime));
                $closeTime24 = date('H:i', strtotime($closeTime));
                if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                    $parkIsOpen = true;
                    break;
                }
            }
        }
    }
    
    $parkNextOpening = getNextOpening($parkOperatingHours);
    
    $numResults = count($results);
    echo '<h3 id="filterHeader" class="mb-3">' . "We found {$numResults} result" . ($numResults !== 1 ? 's' : '') . ' for "<strong>' . htmlspecialchars($categoryName) . '</strong>"' . '</h3>';

    echo '<div id="filterResultsContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">';
    
    foreach ($results as $stall) {
        if (!$parkIsOpen) {
            $isOpen = false;
        } else {
            $isOpen = true;
            if (!empty($stall['stall_operating_hours'])) {
                $operatingHours = explode('; ', $stall['stall_operating_hours']);
                $isOpen = false;
                foreach ($operatingHours as $hours) {
                    list($days, $timeRange) = explode('<br>', $hours);
                    $daysArray = array_map('trim', explode(',', $days));
                    if (in_array($currentDay, $daysArray)) {
                        list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
                        $openTime24  = date('H:i', strtotime($openTime));
                        $closeTime24 = date('H:i', strtotime($closeTime));
                        if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                            $isOpen = true;
                            break;
                        }
                    }
                }
            }
        }
        
        // Check if the stall has products
        $stallProducts = $parkObj->getStallProducts($stall['id']);
        $hasProducts = !empty($stallProducts);
        
        // Set status based on park/stall open status and product availability
        $status = (!$parkIsOpen || !$isOpen) ? 'closed' : 'open';
        if (!$hasProducts) {
            $status = 'unavailable';
        }
        
        // Check if current user is admin, park owner, or stall owner of this stall
        $canAccessStall = false;
        if (isset($user)) {
            $user_id = $user['id'];
            $isParkOwnerOfPark = $parkObj->isParkOwnerOfPark($user_id, $park_id);
            
            // Admin/Food Park Owner can access all stalls
            if ($user['role'] === 'Admin' || $isParkOwnerOfPark) {
                $canAccessStall = true;
            }
            
            // Stall owner can access their own stall
            else if (isset($stall['user_id']) && $stall['user_id'] == $user_id) {
                $canAccessStall = true;
            }
        }
        ?>
        <div class="col stall-card" data-is-open="<?= $isOpen ? '1' : '0'; ?>" data-status="<?= $status ?>">
        <?php if ($hasProducts || $canAccessStall) { ?>
            <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="card-link text-decoration-none bg-white">
        <?php } ?>
                <div class="card" style="position: relative;">
                    <?php if ($status === 'unavailable' || !$hasProducts) { ?>
                        <div class="closed text-center">
                            <span>Unavailable</span>
                        </div>
                    <?php } elseif ($status === 'closed' && $hasProducts) { 
                        $operatingHoursArray = explode('; ', $stall['stall_operating_hours']);
                        $closedMessage = !$parkIsOpen ? $parkNextOpening : getNextOpening($operatingHoursArray);
                    ?>
                        <div class="closed text-center">
                            <div>
                                <span>Closed until <?= $closedMessage ?></span>
                                <button class="rounded bg-white small border-0 px-3 py-1 mt-2" style="color:#CD5C08;">Order for later</button>
                            </div> 
                        </div>
                    <?php } ?>
                    <img src="<?= $stall['logo'] ?>" class="card-img-top" alt="<?= htmlspecialchars($stall['name']); ?>">
                    
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center">
                            <?php 
                            $stall_categories = explode(',', $stall['stall_categories']); 
                            foreach ($stall_categories as $index => $catName) { 
                            ?>
                                <p class="card-text text-muted m-0"><?= trim($catName) ?></p>
                                <?php if ($index !== array_key_last($stall_categories)) { ?>
                                    <span class="dot text-muted"></span>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <h5 class="card-title my-2"><?= $stall['name']; ?></h5>
                        <p class="card-text text-muted m-0"><?= $stall['description']; ?></p>
                        <div class="mt-2">
                            <?php if (in_array($stall['id'], $popularIds)) { ?>
                                <span class="opennow">Popular</span>
                            <?php } ?>
                            <?php if (in_array($stall['id'], $promoIds)) { ?>
                                <span class="discount">With Promo</span>
                            <?php } ?>
                            <?php if (in_array($stall['id'], $newProdIds)) { ?>
                                <span class="newopen">New Arrival</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php if ($hasProducts || $canAccessStall) { ?>
                </a>
            <?php } ?>
        </div>
        <?php
    }
    echo '</div>'; 
    exit;
}
?>
