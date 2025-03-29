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
    if (strpos($hours, '<br>') !== false) {
        list($days, $timeRange) = explode('<br>', $hours);
        $daysArray = array_map('trim', explode(',', $days));
        if (in_array($currentDay, $daysArray)) {
            list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
            $openTime24 = date('H:i', strtotime($openTime));
            $closeTime24 = date('H:i', strtotime($closeTime));
            if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                $parkIsOpen = true;
                break;
            }
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
?>

<style>
    main{
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
<main>

    <form action="" method="post" class="py-2 px-4 border rounded-2 search d-flex align-items-center bg-white mb-3">
        <i class="fas fa-search fa-lg text-muted me-1"></i>
        <input type="text" id="searchInput" placeholder="Search Food Stall" autocomplete="off" class="search-input border-0">
        <span class="text-muted small">GITGUD</span>
    </form>

    <section class="bg-white border rounded-2 px-5 py-4 mb-3">
        <h3 class="mb-3">Categories</h3>
        <div class="tpdiv position-relative">
            <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
            <div class="d-flex rightfilter gap-3">
                <a href="#" class="text-decoration-none text-center category-link" data-category="BBQ">
                    <img src="assets/images/BBQ.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">BBQ</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Seafood">
                    <img src="assets/images/Seafood.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Seafood</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Desserts">
                    <img src="assets/images/Desserts.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Desserts</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Snacks">
                    <img src="assets/images/Snacks.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Snacks</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Beverages">
                    <img src="assets/images/Beverages.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Beverages</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Vegan">
                    <img src="assets/images/Vegan.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Vegan</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Asian">
                    <img src="assets/images/Asian.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Asian</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Burgers">
                    <img src="assets/images/Burgers.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Burgers</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Tacos">
                    <img src="assets/images/Tacos.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Tacos</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Fusion">
                    <img src="assets/images/Fusion.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Fusion</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Pasta">
                    <img src="assets/images/Pasta.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Pasta</span>
                </a>
                <a href="#"class="text-decoration-none text-center category-link" data-category="Salads">
                    <img src="assets/images/Salads.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Salads</span>
                </a>
            </div>
            <a href="stall.php" class="card-link text-decoration-none">
            </a>
            <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
        </div>

    </section>

    <div class="disabled" <?php if(isset($park['status']) && $park['status'] === 'Unavailable') { echo 'style="pointer-events: none; opacity: 0.5;"'; } ?>>
        <section id="searchResultsSection" class="bg-white border rounded-2 px-5 py-4 mb-3" style="display: none; ">
            <h3 id="searchHeader" class="mb-3"></h3>
            <div id="searchResultsContainer" class="row row-cols-1 row-cols-md-3 g-3"></div>
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
                                <div class="p-3" style="width:400px;">
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
                                <div class="p-3" style="width:400px;">
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
                                <div class="p-3" style="width:400px;">
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
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 p-0">All Food Stalls</h3>
                <div class="oc"> 
                    <button id="openBtn" class="btn btn-outline-secondary">Open</button>
                    <button id="closedBtn" class="btn btn-outline-secondary">Closed</button>
                    <button id="unavailableBtn" class="btn btn-outline-secondary">Unavailable</button>
                </div>

            </div>
            
            <div id="stallsContainer" class="row row-cols-1 row-cols-md-3 g-3">
                <?php foreach ($allStalls as $stall) { 
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
                                    $openTime24 = date('H:i', strtotime($openTime));
                                    $closeTime24 = date('H:i', strtotime($closeTime));
                                    if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                                        $isOpen = true;
                                        break;
                                    }
                                }
                            }
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
            const category = this.dataset.category;
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "filter_stalls.php?park_id=<?= $park_id; ?>&category=" + encodeURIComponent(category), true);
            xhr.onreadystatechange = function(){
                if(xhr.readyState === 4 && xhr.status === 200){
                    // For example, insert into a dedicated section for filtered stalls:
                    document.getElementById('filterResultsSection').innerHTML = xhr.responseText;
                    document.getElementById('filterResultsSection').style.display = 'block';
                }
            };
            xhr.send();
        });
    });


</script>
