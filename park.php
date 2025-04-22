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
        // if days are embedded, split them off
        if (strpos($hours, '<br>') !== false) {
            list(, $timeRange) = explode('<br>', $hours, 2);
        } else {
            $timeRange = $hours;
        }
    
        // now split open/close
        list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
    
        $openTime24  = date('H:i', strtotime($openTime));
        $closeTime24 = date('H:i', strtotime($closeTime));
    
        // overnight?
        if ($closeTime24 <= $openTime24) {
            if ($currentTime >= $openTime24 || $currentTime <= $closeTime24) {
                $parkIsOpen = true;
                break;
            }
        } else {
            if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                $parkIsOpen = true;
                break;
            }
        }
    }

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

    $parkNextOpening = getNextOpening($parkOperatingHours);
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


    <div class="disabled" <?php if(isset($park['status']) && $park['status'] === 'Unavailable') { echo 'style="pointer-events: none; opacity: 0.5;"'; } ?>>
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
                <h3 class="m-0 p-0">All Food Stalls</h3>
                <div class="oc"> 
                    <button id="openBtn" class="btn btn-outline-secondary">Open</button>
                    <button id="closedBtn" class="btn btn-outline-secondary">Closed</button>
                    <button id="unavailableBtn" class="btn btn-outline-secondary">Unavailable</button>
                </div>

            </div>
            
            <div id="stallsContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <?php if (empty($allStalls)): ?>
                <div class="w-100 text-center py-3">
                    <p class="text-muted">Sorry, there are no food stalls available right now.</p>
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
                        <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="card-link text-decoration-none bg-white">
                            <div class="card" style="position: relative;">
                                <?php if ($status === 'unavailable') { ?>
                                    <div class="closed text-center">
                                        <span>Unavailable</span>
                                    </div>
                                <?php } elseif ($status === 'closed') { 
                                    // Display closed message (for the park or the stall)
                                    $closedMessage = !$parkIsOpen ? $parkNextOpening : getNextOpening(explode('; ', $stall['stall_operating_hours']));
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
