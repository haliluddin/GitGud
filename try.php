<?php
include_once 'links.php'; 
include_once 'header.php'; 
require_once __DIR__ . '/classes/product.class.php';
require_once __DIR__ . '/classes/stall.class.php';
require_once __DIR__ . '/classes/park.class.php'; // include park class to get park details
require_once __DIR__ . '/classes/encdec.class.php';

$stallObj   = new Stall();
$productObj = new Product();
$parkObj    = new Park(); 

if (isset($_GET['id'])) {
    $stall_id = decrypt(urldecode($_GET['id']));
    
    $stall = $parkObj->getStall($stall_id); 
    $products = $stallObj->getProducts($stall_id);
    $categories = $productObj->getCategories($stall_id);
    $totalProducts = $stallObj->getTotalProducts($stall_id);
    
    $likeCount = $productObj->getStallLikes($stall_id);
    
    if (isset($user_id))
        $likedByUser = $productObj->isStallLiked($stall_id, $user_id);
    else
        $likedByUser = false;
    
    $popularProducts = $productObj->getPopularProducts($stall_id);
    $promoProducts   = $productObj->getPromoProducts($stall_id);
    $newProducts     = $productObj->getNewProducts($stall_id);

    $popularProdIds = array_column($popularProducts, 'id');
    $promoProdIds   = array_column($promoProducts, 'id');
    $newProdIds     = array_column($newProducts, 'id');
}

?>
<main>
    <div class="pageinfo pb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex gap-4 align-items-center pagelogo">
                <img src="<?= htmlspecialchars($stall['logo']); ?>" alt="Stall Logo">
                <div>
                    <div class="d-flex gap-2 align-items-center">
                        <?php 
                        $stall_categories = explode(',', $stall['stall_categories']); 
                        foreach ($stall_categories as $index => $cat): ?>
                            <p class="card-text text-muted m-0"><?= trim($cat); ?></p>
                            <?php if ($index !== array_key_last($stall_categories)): ?>
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <h5 class="my-2 fw-bold fs-2"><?= htmlspecialchars($stall['name']); ?></h5>
                    <p class="text-muted m-0"><?= htmlspecialchars($stall['description']); ?></p>
                    <div class="d-flex gap-2 align-items-center my-2">
                        <?php if (!$parkIsOpen): ?>
                            <span class="pageon text-muted">Closed now</span>
                        <?php else: ?>
                            <?php if ($isOpen): ?>
                                <span class="pageon">Open now</span>
                            <?php else: ?>
                                <span class="pageon text-muted">Closed now</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <span class="dot text-muted"></span>
                        <button class="conopepay" data-bs-toggle="modal" data-bs-target="#morestallinfo"
                            data-email="<?= htmlspecialchars($stall['email']); ?>"
                            data-phone="<?= htmlspecialchars($stall['phone']); ?>"
                            data-hours="<?= htmlspecialchars($stall['stall_operating_hours']); ?>"
                            data-payment-methods="<?= htmlspecialchars($stall['stall_payment_methods']); ?>">
                            <i class="fa-solid fa-circle-info"></i> More info
                        </button>
                    </div>
                    <div class="d-flex gap-5 m-0">
                        <div class="d-flex gap-2">
                            <span>Likes</span>
                            <span class="likpro"><?= $likeCount; ?></span>
                        </div>
                        <div class="d-flex gap-2">
                            <span>Products</span>
                            <span class="likpro"><?= $totalProducts; ?></span>
                        </div> 
                    </div>
                </div>
            </div>
            <?php if (isset($user_id) && $user_id == $stall['user_id']): ?>
                <button class="pagelike" onclick="window.location.href='editpage.php?id=<?= $stall['id'] ?>';">Edit Page</button>
            <?php else: ?>
                <button id="likeBtn" class="pagelike <?= $likedByUser ? 'liked' : ''; ?>">
                    <?= $likedByUser ? 'Liked' : 'Like'; ?>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex pagefilter align-items-center gap-3">
        <div class="d-flex align-items-center gap-3 leftfilter">
            <?php if (!empty($products)): ?>
            <div id="searchForm" action="#" method="get" class="searchmenu">
                <button><i class="fas fa-search fa-lg"></i></button>
                <input type="text" name="search" id="searchInput" placeholder="Search in menu">
            </div>
            <?php elseif (!empty($popularProducts)): ?>
                <a href="#popular" class="nav-link"><i class="fa-solid fa-fire-flame-curved"></i> Popular</a>
            <?php endif; ?>
            <?php if (!empty($newProducts)): ?>
                <a href="#new" class="nav-link"><i class="fa-solid fa-ribbon"></i> New</a>
            <?php endif; ?>
            <?php if (!empty($promoProducts)): ?>
                <a href="#promo" class="nav-link"><i class="fa-solid fa-percent"></i> Promo</a>
            <?php endif; ?>
        </div>

        <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>

        <div class="d-flex rightfilter gap-3">
            <?php foreach ($categories as $cat): ?>
                <a href="#category<?= $cat['id']; ?>" class="nav-link"><?= htmlspecialchars($cat['name']); ?></a>
            <?php endforeach; ?>
        </div>

        <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
    </div>
    
    <div class="disabled">
        <section id="searchResultsSection" class="pt-3 mt-3" style="display: none; ">
            <h5 id="searchHeader"></h5>
            <div id="searchResultsContainer" class="row row-cols-1 row-cols-md-4 g-3"></div>
        </section>
        
        <?php if (empty($products)): ?>
            <br><br>
            <section class="mt-5 text-center">
                No products are available in this stall at the moment.
            </section>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                ...
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <br><br><br><br><br><br>

    <div class="modal fade" id="morestallinfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold m-0">More Info</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <h5 class="fw-bold mb-3">Business Contact</h5>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Business Email</span>
                            <span data-email></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Business Phone Number</span>
                            <span data-phone></span>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3">Operating Hours</h5>
                    <div class="mb-4" data-hours>
                        <!-- Dynamically added operating hours -->
                    </div>

                    <h5 class="fw-bold mb-3">Payment Accepted</h5>
                    <div class="mb-4" data-payment-methods>
                    </div>

                    <button class="border-0 py-2 px-3 rounded-5 me-2"><i class="fa-regular fa-copy me-2 fs-5"></i>Share Link</button>
                    <button class="border-0 py-2 px-3 rounded-5" data-bs-toggle="modal" data-bs-target="#report"><i class="fa-regular fa-flag me-2 fs-5"></i>Report</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('likeBtn')?.addEventListener('click', function(){
            const btn = this;
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "toggle_like.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function(){
                if(xhr.readyState === 4 && xhr.status === 200){
                    let res = JSON.parse(xhr.responseText);
                    if(res.liked){
                        btn.classList.add('liked');
                        btn.innerText = 'Liked';
                    } else {
                        btn.classList.remove('liked');
                        btn.innerText = 'Like';
                    }
                    document.querySelector('.likpro').innerText = res.likeCount;
                }
            };
            xhr.send("stall_id=<?= $stall_id; ?>&user_id=<?= $user_id; ?>");
        });

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
            xhr.open("GET", "search_products.php?stall_id=<?= $stall_id; ?>&search=" + encodeURIComponent(term), true);
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
    </script>

    <script>
        const modal = document.getElementById('morestallinfo');

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const email = button.getAttribute('data-email');
            const phone = button.getAttribute('data-phone');
            const hours = button.getAttribute('data-hours');
            const paymentMethods = button.getAttribute('data-payment-methods');

            modal.querySelector('.modal-body span[data-email]').textContent = email || 'N/A';
            modal.querySelector('.modal-body span[data-phone]').textContent = phone || 'N/A';

            const hoursContainer = modal.querySelector('.modal-body div[data-hours]');
            hoursContainer.innerHTML = hours
                ? hours.split('; ').map(hour => `<p>${hour}</p>`).join('')
                : '<p>No operating hours available</p>';

            const paymentContainer = modal.querySelector('.modal-body div[data-payment-methods]');
            paymentContainer.innerHTML = paymentMethods
                ? paymentMethods.split(', ').map(method => `<div><i class="fa-solid fa-check me-2"></i><span>${method}</span></div>`).join('')
                : '<p>No payment methods available</p>';
        });
    </script>

    <script src="assets/js/navigation.js?v=<?= time(); ?>"></script>

</main>

<?php
    include_once 'modals.php'; 
    include_once './footer.php'; 
?> 