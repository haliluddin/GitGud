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
    <div class="d-flex pagefilter align-items-center gap-3">
        <div class="d-flex align-items-center gap-3 leftfilter">
            <div id="searchForm" action="#" method="get" class="searchmenu">
                <button><i class="fas fa-search fa-lg"></i></button>
                <input type="text" name="search" id="searchInput" placeholder="Search in menu">
            </div>
            <?php if (!empty($popularProducts)): ?>
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

    <section id="searchResultsSection" class="pt-3 mt-3" style="display: none; ">
        <h5 id="searchHeader"></h5>
        <div id="searchResultsContainer" class="row row-cols-1 row-cols-md-4 g-3"></div>
    </section>

    <?php if (empty($products)): ?>
        <section class="pt-3 mt-3">
            <div class="alert alert-info">
                No products are available in this stall at the moment. Please check back later.
            </div>
        </section>
    <?php else: ?>
        <?php foreach ($categories as $cat): ?>
            <section id="category<?= $cat['id']; ?>" class="pt-3 mt-3">
                <h5><?= htmlspecialchars($cat['name']); ?></h5>
                <div class="row row-cols-1 row-cols-md-4 g-3">
                    <?php 
                        $hasProducts = false;
                        foreach ($products as $product):
                            if ($product['category_id'] == $cat['id']):
                                $hasProducts = true;
                                $variations = $stallObj->getProductVariations($product['id']);
                                $cardClickable = true; 
                                if (!empty($variations)) {
                                    foreach ($variations as $variation) {
                                        $options = $stallObj->getVariationOptions($variation['id']);
                                        $variationHasStock = false;
                                        foreach ($options as $option) {
                                            if ($stallObj->getStock($product['id'], $option['id']) > 0) {
                                                $variationHasStock = true;
                                                break;
                                            }
                                        }
                                        if (!$variationHasStock) {
                                            $cardClickable = false;
                                            break;
                                        }
                                    }
                                } else {
                                    $cardClickable = (($product['stock'] ?? 0) > 0);
                                }
                    ?>
                    <?php 
                            endif;
                        endforeach;
                    ?>
                    <?php if (!$hasProducts): ?>
                        <div class="col">
                            <p>No products found for this category.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script src="assets/js/navigation.js?v=<?= time(); ?>"></script>

</main>

<?php
    include_once 'modals.php'; 
    include_once './footer.php'; 
?> 