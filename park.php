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
?>

<style>
    main {
        background-color: white;
    }
    .btn.active {
        background-color:  #6c757d;
        color: white;
    }
     .closed {
        z-index: 2;
    }
</style>

<main>
    <section>
        <br>
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/images/foodpark.jpg" class="d-block w-100 h-50 rounded" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/foodpark.jpg" class="d-block w-100 h-50 rounded" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/foodpark.jpg" class="d-block w-100 h-50 rounded" alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <section class="third">
        <br><br>
        <h3 class="mb-3">Categories</h3>
        <div class="tpdiv position-relative">
            <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
            <div class="d-flex rightfilter gap-3">
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/BBQ.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">BBQ</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Seafood.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Seafood</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Desserts.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Desserts</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Snacks.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Snacks</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Beverages.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Beverages</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Vegan.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Vegan</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Asian.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Asian</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Burgers.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Burgers</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Tacos.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Tacos</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Fusion.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Fusion</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Pasta.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Pasta</span>
                </a>
                <a href="#" class="text-decoration-none text-center">
                    <img src="assets/images/Salads.jpg" width="110px" height="110px" class="rounded-2">
                    <span class="text-dark d-block mt-1">Salads</span>
                </a>
            </div>
            <a href="stall.php" class="card-link text-decoration-none">
            </a>
            <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
        </div>

    </section>
    <!-- Popular Section -->
    <?php if (!empty($popularStalls)) { ?>
    <section>
        <br><br>
        <h3 class="mb-3">Popular</h3>
        <div class="tpdiv position-relative">
            <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
            <div class="d-flex rightfilter gap-3">
                <?php foreach ($popularStalls as $stall) { ?>
                    <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative">
                        <img src="<?= $stall['logo'] ?>" class="h-100 rounded-start-2" width="150px">
                        <div class="p-3" style="width:500px;">
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
    <section>
        <br><br>
        <h3 class="mb-3">With Promo</h3>
        <div class="tpdiv position-relative">
            <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
            <div class="d-flex rightfilter gap-3">
                <?php foreach ($promoStalls as $stall) { ?>
                    <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative">
                        <img src="<?= $stall['logo'] ?>" class="h-100 rounded-start-2" width="150px">
                        <div class="p-3" style="width:500px;">
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
    <section>
        <br><br>
        <h3 class="mb-3">New Arrival</h3>
        <div class="tpdiv position-relative">
            <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
            <div class="d-flex rightfilter gap-3">
                <?php foreach ($newProdStalls as $stall) { ?>
                    <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative">
                        <img src="<?= $stall['logo'] ?>" class="h-100 rounded-start-2" width="150px">
                        <div class="p-3" style="width:500px;">
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

    <section>
        <br><br>
        <h3 class="mb-3">All Food Stalls</h3>
        <div class="oc"> 
            <button id="openBtn" class="btn btn-outline-secondary">Open</button>
            <button id="closedBtn" class="btn btn-outline-secondary">Closed</button>
        </div>

        <div id="stallsContainer" class="row row-cols-1 row-cols-md-4 g-3">
            <?php foreach ($allStalls as $stall) { 
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
            ?>
                <div class="col stall-card" data-is-open="<?= $isOpen ? '1' : '0'; ?>">
                    <a href="stall.php?id=<?= encrypt($stall['id']); ?>" class="card-link text-decoration-none bg-white">
                        <div class="card" style="position: relative;">
                            <?php if (!$isOpen && !empty($stall['stall_operating_hours'])) { 
                                $operatingHoursArray = explode('; ', $stall['stall_operating_hours']);
                            ?>
                                <div class="closed">Closed until <?= getNextOpening($operatingHoursArray) ?></div>
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
        <br><br><br><br><br><br>
    </section>
</main>

<?php include_once 'footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const openBtn = document.getElementById('openBtn');
    const closedBtn = document.getElementById('closedBtn');
    const stallCards = document.querySelectorAll('.stall-card');

    function filterStalls(status) {
        stallCards.forEach(card => {
            const isOpen = card.getAttribute('data-is-open') === '1';
            if(status === 'open') {
                card.style.display = isOpen ? '' : 'none';
            } else if(status === 'closed') {
                card.style.display = !isOpen ? '' : 'none';
            } else {
                card.style.display = '';
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
            filterStalls('closed');
        }
    });
});

</script>
