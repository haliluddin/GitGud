<?php 
    include_once 'links.php'; 
    include_once 'header.php';
    require_once __DIR__ . '/classes/encdec.class.php';
    require_once __DIR__ . '/classes/park.class.php';

    $popularStalls = $parkObj->getPopularStalls($park_id);
    $promoStalls   = $parkObj->getPromoStalls($park_id);
    $newProdStalls = $parkObj->getNewProductStalls($park_id);
    $allStalls     = $parkObj->getStalls($park_id);

    $popularIds = array_column($popularStalls, 'id');
    $promoIds   = array_column($promoStalls, 'id');
    $newProdIds = array_column($newProdStalls, 'id');

    date_default_timezone_set('Asia/Manila'); 
    $currentDay = date('l'); 
    $currentTime = date('H:i');

    $park = $parkObj->getPark($park_id); 
    $parkOperatingHours = [];
    if (!empty($park['operating_hours'])) {
        $parkOperatingHours = explode('; ', $park['operating_hours']);
    }

    $parkIsOpen = false;
    foreach ($parkOperatingHours as $hours) {
        $days = [];
        $timeRange = $hours;

        // Split days and time if present
        if (strpos($hours, '<br>') !== false) {
            list($daysPart, $timeRange) = explode('<br>', $hours, 2);
            $days = array_map('trim', explode(',', $daysPart));
        }

        // Check if the current day is in the allowed days (or if no days specified)
        if (!empty($days) && !in_array($currentDay, $days)) {
            continue; // Skip this entry if today isn't listed
        }

        // Proceed with time check
        list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
        $openTime24  = date('H:i', strtotime($openTime));
        $closeTime24 = date('H:i', strtotime($closeTime));

        if ($closeTime24 <= $openTime24) {
            // Overnight window
            if ($currentTime >= $openTime24 || $currentTime <= $closeTime24) {
                $parkIsOpen = true;
                break;
            }
        } else {
            // Same-day window
            if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                $parkIsOpen = true;
                break;
            }
        }
    }

    function getNextMutualOpening($stallHours, $parkHours, $currentDay, $currentTime) {
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ];
        $currentDayIndex = array_search($currentDay, $daysOfWeek);
        $now = strtotime($currentTime);
        for ($i = 0; $i < 7; $i++) {
            $checkDayIndex = ($currentDayIndex + $i) % 7;
            $checkDay = $daysOfWeek[$checkDayIndex];

            // Find next park opening window for this day
            foreach ($parkHours as $parkEntry) {
                if (strpos($parkEntry, '<br>') !== false) {
                    list($parkDays, $parkTimeRange) = explode('<br>', $parkEntry);
                    $parkDaysArray = array_map('trim', explode(',', $parkDays));
                    if (!in_array($checkDay, $parkDaysArray, true)) continue;
                } else {
                    $parkTimeRange = $parkEntry;
                }
                list($parkOpen, $parkClose) = array_map('trim', explode(' - ', $parkTimeRange));
                $parkOpen24 = date('H:i', strtotime($parkOpen));
                $parkClose24 = date('H:i', strtotime($parkClose));

                // Find next stall opening window for this day
                foreach ($stallHours as $stallEntry) {
                    if (strpos($stallEntry, '<br>') !== false) {
                        list($stallDays, $stallTimeRange) = explode('<br>', $stallEntry);
                        $stallDaysArray = array_map('trim', explode(',', $stallDays));
                        if (!in_array($checkDay, $stallDaysArray, true)) continue;
                    } else {
                        $stallTimeRange = $stallEntry;
                    }
                    list($stallOpen, $stallClose) = array_map('trim', explode(' - ', $stallTimeRange));
                    $stallOpen24 = date('H:i', strtotime($stallOpen));
                    $stallClose24 = date('H:i', strtotime($stallClose));

                    // Find the overlap between park and stall
                    $open = max($parkOpen24, $stallOpen24);
                    $close = min($parkClose24, $stallClose24);
                    // Convert to timestamps for robust comparison
                    $dateStr = date('Y-m-d', strtotime("+{$i} days"));
                    $openTs = strtotime("$dateStr $open");
                    $closeTs = strtotime("$dateStr $close");

                    // Handle overnight close (close time less than open time)
                    if ($close <= $open) {
                        $closeTs = strtotime("+1 day", $closeTs);
                    }

                    // If today, opening must be after current time
                    if ($i === 0 && $openTs <= strtotime(date('Y-m-d') . ' ' . $currentTime)) {
                        continue;
                    }
                    // If overlap is valid
                    if ($closeTs > $openTs) {
                        return $checkDay . ' ' . date('g:i A', $openTs);
                    }
                }
            }
        }
        return 'N/A';
    }

    $parkNextOpening = getNextMutualOpening([], $parkOperatingHours, $currentDay, $currentTime);
    $categories = $parkObj->getCategories();
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .hp-tm{
        padding: 20px 120px;
    }
    .btn.active {
        background-color:  #6c757d;
        color: white;
    }
     .closed {
        z-index: 2;
    }
    .search-input{
        width: 100%;
        outline: none;
    }
</style>
<main class="hp-tm">
    <form action="" method="post" class="py-2 px-4 border rounded-2 search d-flex align-items-center bg-white mb-3">
        <i class="fas fa-search fa-lg text-muted me-1"></i>
        <input type="text" id="searchInput" placeholder="Search Food Stall" autocomplete="off" class="search-input border-0">
        <span class="text-muted small search-tm">GITGUD</span>
    </form>

    <section class="bg-white border rounded-2 px-5 py-4 mb-3">
        <h3 class="mb-3">Categories</h3>
        <div class="tpdiv position-relative">
            <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
            <div class="d-flex rightfilter gap-3">
            <?php foreach ($categories as $cat): ?>
                <a href="#"
                class="text-decoration-none text-center category-link"
                data-category-id="<?= $cat['id'] ?>">
                <img src="<?= htmlspecialchars($cat['image_url']) ?>"
                    width="110" height="110"
                    class="rounded-2">
                <span class="text-dark d-block mt-1">
                    <?= htmlspecialchars($cat['name']) ?>
                </span>
                </a>
            <?php endforeach; ?>
            </div>
            <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
        </div>
    </section>


    <div class="disabled" <?php if(isset($park['status']) && $park['status'] === 'Unavailable') { echo 'style="pointer-events: none;"'; } ?>>
        <section id="searchResultsSection" class="bg-white border rounded-2 px-5 py-4 mb-3" style="display: none; ">
            <h3 id="searchHeader" class="mb-3"></h3>
            <div id="searchResultsContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3"></div>
        </section>

        <section id="filterResultsSection" class="bg-white border rounded-2 px-5 py-4 mb-3" style="display: none;"></section>

        <!-- Popular Section -->
        <?php if (!empty($popularStalls)) { ?>
            <section class="bg-white border rounded-2 px-5 py-4 mb-3">
                <h3 class="mb-3">Popular</h3>
                <div class="tpdiv position-relative">
                    <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
                    <div class="d-flex rightfilter gap-3">
                        <?php foreach ($popularStalls as $stall) { ?>
                            <a href="stall.php?id=<?= urlencode(encrypt($stall['id'])) ?>" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative">
                                <img src="<?= $stall['logo'] ?>" class="h-100 rounded-start-2" width="150px">
                                <div class="p-3 badge-tm" style="width:400px;">
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php 
                                            $stall_categories = explode(',', $stall['stall_categories']); 
                                            foreach ($stall_categories as $index => $category) { 
                                        ?>
                                            <p class="card-text text-muted m-0"><?= trim($category) ?></p>
                                            <?php if ($index !== array_key_last($stall_categories)) { ?>
                                                <span class="dot text-muted"></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <h5 class="card-title my-2" style="color: black;"><?= $stall['name'] ?></h5>
                                    <p class="card-text text-muted m-0"><?= $stall['description'] ?></p>
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
                            </a>
                        <?php } ?>
                    </div>
                    <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
                </div>
            </section>
        <?php } ?>

        <!-- With Promo Section -->
        <?php if (!empty($promoStalls)) { ?>
            <section class="bg-white border rounded-2 px-5 py-4 mb-3">
                <h3 class="mb-3">With Promo</h3>
                <div class="tpdiv position-relative">
                    <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
                    <div class="d-flex rightfilter gap-3">
                        <?php foreach ($promoStalls as $stall) { ?>
                            <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative">
                                <img src="<?= $stall['logo'] ?>" class="h-100 rounded-start-2" width="150px">
                                <div class="p-3 badge-tm" style="width:400px;">
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php 
                                            $stall_categories = explode(',', $stall['stall_categories']); 
                                            foreach ($stall_categories as $index => $category) { 
                                        ?>
                                            <p class="card-text text-muted m-0"><?= trim($category) ?></p>
                                            <?php if ($index !== array_key_last($stall_categories)) { ?>
                                                <span class="dot text-muted"></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <h5 class="card-title my-2" style="color: black;"><?= $stall['name'] ?></h5>
                                    <p class="card-text text-muted m-0"><?= $stall['description'] ?></p>
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
                            </a>
                        <?php } ?>
                    </div>
                    <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
                </div>
            </section>
        <?php } ?>

        <!-- New Arrival Section -->
        <?php if (!empty($newProdStalls)) { ?>
            <section class="bg-white border rounded-2 px-5 py-4 mb-3">
                <h3 class="mb-3">New Arrival</h3>
                <div class="tpdiv position-relative">
                    <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
                    <div class="d-flex rightfilter gap-3">
                        <?php foreach ($newProdStalls as $stall) { ?>
                            <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative">
                                <img src="<?= $stall['logo'] ?>" class="h-100 rounded-start-2" width="150px">
                                <div class="p-3 badge-tm" style="width:400px;">
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php 
                                            $stall_categories = explode(',', $stall['stall_categories']); 
                                            foreach ($stall_categories as $index => $category) { 
                                        ?>
                                            <p class="card-text text-muted m-0"><?= trim($category) ?></p>
                                            <?php if ($index !== array_key_last($stall_categories)) { ?>
                                                <span class="dot text-muted"></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <h5 class="card-title my-2" style="color: black;"><?= $stall['name'] ?></h5>
                                    <p class="card-text text-muted m-0"><?= $stall['description'] ?></p>
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
                            </a>
                        <?php } ?>
                    </div>
                    <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
                </div>
            </section>
        <?php } ?>

        <section class="bg-white border rounded-2 px-5 py-4 m-0">
            <div class="mb-3 d-flex justify-content-between align-items-center allfs-tm">
                <?php
                    if (!empty($allStalls)) { ?>
                        <h3 class="m-0 p-0">All Food Stalls</h3>
                        <div class="oc"> 
                            <button id="openBtn" class="btn btn-outline-secondary">Open</button>
                            <button id="closedBtn" class="btn btn-outline-secondary">Closed</button>
                            <button id="unavailableBtn" class="btn btn-outline-secondary">Unavailable</button>
                        </div>
                <?php } 
                ?>
            </div>
            
            <div id="stallsContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <?php if (empty($allStalls)): ?>
                <div class="w-100 text-center py-4 bg-light rounded-3 border border-2" style="border-color: #CD5C08 !important;">
                    <div class="d-flex justify-content-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#CD5C08" class="bi bi-clipboard2-check" viewBox="0 0 16 16">
                            <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5z"/>
                            <path d="M3 2.5a.5.5 0 0 1 .5-.5H4a.5.5 0 0 0 0-1h-.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1H12a.5.5 0 0 0 0 1h.5a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5z"/>
                            <path d="M10.854 7.854a.5.5 0 0 0-.708-.708L7.5 9.793 6.354 8.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z"/>
                        </svg>
                    </div>
                    <h5 class="mb-3 fw-bold" style="color: #CD5C08 !important;">Before Opening Checklist</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex justify-content-center align-items-start">
                            <span class="badge me-2 mt-1" style="background-color: #CD5C08 !important;">✓</span>
                            <span>All stalls are properly set up</span>
                        </li>
                        <li class="mb-2 d-flex justify-content-center align-items-start">
                            <span class="badge me-2 mt-1" style="background-color: #CD5C08 !important;">✓</span>
                            <span>Stall products/menus are complete</span>
                        </li>
                        <li class="mb-2 d-flex justify-content-center align-items-start">
                            <span class="badge me-2 mt-1" style="background-color: #CD5C08 !important;">✓</span>
                            <span>Operating hours are configured</span>
                        </li>
                        <li class="d-flex justify-content-center align-items-start">
                            <span class="badge me-2 mt-1" style="background-color: #CD5C08 !important;">✓</span>
                            <span>Park information is accurate</span>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <?php foreach ($allStalls as $stall) { 
                    if (!$parkIsOpen) {
                        $isOpen = false;
                    } else {
                        $isOpen = true;
                        if (!empty($stall['stall_operating_hours'])) {
                            $operatingHours = explode('; ', $stall['stall_operating_hours']);
                            $isOpen = false;
                        
                            foreach ($operatingHours as $hours) {
                                // 1) split days vs times if present
                                if (strpos($hours, '<br>') !== false) {
                                    list($dayString, $timeRange) = explode('<br>', $hours, 2);
                                    // Build an array of day-names: ["Monday","Tuesday",…]
                                    $allowedDays = array_map('trim', explode(',', $dayString));
                                    // if today’s not in that list, skip
                                    if (!in_array($currentDay, $allowedDays, true)) {
                                        continue;
                                    }
                                } else {
                                    // no day restriction → applies every day
                                    $timeRange = $hours;
                                }
                        
                                // 2) split open/close times
                                list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
                                $open24  = date('H:i', strtotime($openTime));
                                $close24 = date('H:i', strtotime($closeTime));
                        
                                // 3) same logic for overnight vs same‑day
                                if ($close24 <= $open24) {
                                    // overnight window (e.g. 18:00–02:00)
                                    if ($currentTime >= $open24 || $currentTime <= $close24) {
                                        $isOpen = true;
                                        break;
                                    }
                                } else {
                                    // same‑day window (e.g. 09:00–17:00)
                                    if ($currentTime >= $open24 && $currentTime <= $close24) {
                                        $isOpen = true;
                                        break;
                                    }
                                }
                            }
                        } else {
                            // no hours = closed
                            $isOpen = false;
                        }
                    }
                    
                    if (isset($stall['status']) && $stall['status'] === 'Unavailable') {
                        $status = 'unavailable';
                    } else {
                        $status = $isOpen ? 'open' : 'closed';
                    }
                ?>
                    <div class="col stall-card" data-status="<?= $status; ?>">
                        
                        <?php
                            // Check if the stall has products
                            $hasProducts = false;
                            if (!empty($stall['products'])) {
                                $products = json_decode($stall['products'], true);
                                $hasProducts = !empty($products);
                            }
                            if ($hasProducts) { ?>
                                <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="card-link text-decoration-none bg-white">
                        <?php }?>
                        
                            <div class="card" style="position: relative;">
                                <?php if ($status === 'unavailable' || !$hasProducts) { ?>
                                    <div class="closed text-center">
                                        <span>Unavailable</span>
                                    </div>
                                <?php } elseif ($status === 'closed' && $hasProducts) { 
                                    // Display closed message (for the park or the stall)
                                    $closedMessage = getNextMutualOpening(
                                        explode('; ', $stall['stall_operating_hours']),
                                        $parkOperatingHours,
                                        $currentDay,
                                        $currentTime
                                    );
                                ?>
                                    <div class="closed text-center">
                                        <div>
                                            <span>Closed until <?= $closedMessage ?></span>
                                            <button class="rounded bg-white small border-0 px-3 py-1 mt-2" style="color:#CD5C08;">Order for later</button>
                                        </div>
                                    </div>
                                <?php } ?>
                                <img src="<?= $stall['logo'] ?>" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php 
                                            $stall_categories = explode(',', $stall['stall_categories']); 
                                            foreach ($stall_categories as $index => $category) { 
                                        ?>
                                            <p class="card-text text-muted m-0"><?= trim($category) ?></p>
                                            <?php if ($index !== array_key_last($stall_categories)) { ?>
                                                <span class="dot text-muted"></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <h5 class="card-title my-2"><?= $stall['name'] ?></h5>
                                    <p class="card-text text-muted m-0"><?= $stall['description'] ?></p>
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
                        </a>
                    </div>
                <?php } ?>
            <?php endif; ?>
            </div>
        </section>

    </div>

    <br><br><br><br><br><br>

</main>
   
<?php include_once 'footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        const openBtn = document.getElementById('openBtn');
        const closedBtn = document.getElementById('closedBtn');
        const unavailableBtn = document.getElementById('unavailableBtn');
        const stallCards = document.querySelectorAll('.stall-card');

        function filterStalls(status) {
            stallCards.forEach(card => {
                const stallStatus = card.getAttribute('data-status');
                if(status === 'all'){
                    card.style.display = '';
                } else {
                    card.style.display = (stallStatus === status) ? '' : 'none';
                }
            });
        }

        openBtn.addEventListener('click', function(){
            if(openBtn.classList.contains('active')){
                openBtn.classList.remove('active');
                filterStalls('all');
            } else {
                openBtn.classList.add('active');
                closedBtn.classList.remove('active');
                unavailableBtn.classList.remove('active');
                filterStalls('open');
            }
        });

        closedBtn.addEventListener('click', function(){
            if(closedBtn.classList.contains('active')){
                closedBtn.classList.remove('active');
                filterStalls('all');
            } else {
                closedBtn.classList.add('active');
                openBtn.classList.remove('active');
                unavailableBtn.classList.remove('active');
                filterStalls('closed');
            }
        });

        unavailableBtn.addEventListener('click', function(){
            if(unavailableBtn.classList.contains('active')){
                unavailableBtn.classList.remove('active');
                filterStalls('all');
            } else {
                unavailableBtn.classList.add('active');
                openBtn.classList.remove('active');
                closedBtn.classList.remove('active');
                filterStalls('unavailable');
            }
        });
    });
</script>

<script>
    const searchInput = document.getElementById('searchInput');
    const searchResultsSection = document.getElementById('searchResultsSection');
    const searchHeader = document.getElementById('searchHeader');
    const searchResultsContainer = document.getElementById('searchResultsContainer');

    searchInput.addEventListener('keyup', function(){
        const term = this.value.trim();
        if(term.length === 0){
            searchResultsSection.style.display = 'none';
            return;
        }
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "search_stalls.php?park_id=<?= $park_id; ?>&search=" + encodeURIComponent(term), true);
        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4 && xhr.status === 200){
                searchResultsContainer.innerHTML = xhr.responseText;
                searchResultsSection.style.display = 'block';
                const numResults = searchResultsContainer.querySelectorAll('.col').length;
                searchHeader.innerHTML = `We found ${numResults} result${numResults !== 1 ? 's' : ''} for "<strong>${term}</strong>"`;
            }
        };
        xhr.send();
    });

    document.querySelectorAll('.category-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.dataset.categoryId;
            const xhr = new XMLHttpRequest();
            xhr.open("GET",
            `filter_stalls.php?park_id=<?= $park_id ?>&category_id=${categoryId}`,
            true
            );
            xhr.onreadystatechange = function(){
            if (xhr.readyState===4 && xhr.status===200) {
                document.getElementById('filterResultsSection').innerHTML = xhr.responseText;
                document.getElementById('filterResultsSection').style.display = 'block';
            }
            };
            xhr.send();
        });
    });



</script>
