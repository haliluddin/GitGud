<?php
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/product.class.php';
require_once __DIR__ . '/classes/stall.class.php';

if (isset($_GET['stall_id']) && isset($_GET['search'])) {
    $stall_id   = intval($_GET['stall_id']);
    $searchTerm = trim($_GET['search']);
    
    $productObj = new Product();
    $stallObj   = new Stall();
    
    $results = $productObj->searchProducts($stall_id, $searchTerm);
    
    $popularProducts = $productObj->getPopularProducts($stall_id);
    $promoProducts   = $productObj->getPromoProducts($stall_id);
    $newProducts     = $productObj->getNewProducts($stall_id);
    
    $popularProdIds = array_column($popularProducts, 'id');
    $promoProdIds   = array_column($promoProducts, 'id');
    $newProdIds     = array_column($newProducts, 'id');
    
    $categories = $productObj->getCategories($stall_id);
    
    $today = date('Y-m-d');
    
    foreach ($results as $product) {
        $catName = 'Category';
        foreach ($categories as $cat) {
            if ($cat['id'] == $product['category_id']) {
                $catName = $cat['name'];
                break;
            }
        }
        
        $cardClickable = true;
        $variations = $stallObj->getProductVariations($product['id']);
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
        
        if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
            $today >= $product['start_date'] && $today <= $product['end_date']) {
            $discountedPrice = $product['base_price'] * ((100 - $product['discount']) / 100);
        }
        ?>
        <div class="col">
            <?php if ($cardClickable): ?>
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal<?= $product['id']; ?>">
            <?php else: ?>
                <a href="#" class="card-link text-decoration-none disabled-card">
            <?php endif; ?>
                    <div class="card position-relative">
                        <?php if (!$cardClickable): ?>
                            <div class="closed">No stock</div>
                        <?php endif; ?>
                        <img src="<?= htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']); ?>">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                        <div class="card-body">
                            <p class="card-text text-muted m-0"><?= htmlspecialchars($catName); ?></p>
                            <h5 class="card-title my-2"><?= htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted m-0"><?= htmlspecialchars($product['description']); ?></p>
                            <?php if (isset($discountedPrice)): ?>
                                <div class="my-3">
                                    <span class="proprice">₱<?= number_format($discountedPrice, 2); ?></span>
                                    <span class="pricebefore small">₱<?= number_format($product['base_price'], 2); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="my-3">
                                    <span class="proprice">₱<?= number_format($product['base_price'], 2); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="m-0">
                                <?php if (in_array($product['id'], $popularProdIds)) { ?>
                                    <span class="opennow">Popular</span>
                                <?php } ?>
                                <?php if (in_array($product['id'], $promoProdIds)) { 
                                    if (isset($discountedPrice)) { ?>
                                        <span class="discount"><?= intval($product['discount']); ?>% off</span>
                                <?php } } ?>
                                <?php if (in_array($product['id'], $newProdIds)) { ?>
                                    <span class="newopen">New</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </a>
        </div>
        <?php
    }
    exit;
}
?>
