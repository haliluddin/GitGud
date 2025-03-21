<?php
include_once 'links.php'; 
include_once 'header.php'; 
require_once __DIR__ . '/classes/product.class.php';
require_once __DIR__ . '/classes/stall.class.php';
require_once __DIR__ . '/classes/encdec.class.php';

$stallObj   = new Stall();
$productObj = new Product();

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
    $promoProdIds = array_column($promoProducts, 'id');
    $newProdIds = array_column($newProducts, 'id');
}

date_default_timezone_set('Asia/Manila');
$currentTime = date('H:i');
$isOpen = false;
if (!empty($stall['stall_operating_hours'])) {
    $hoursArray = explode('; ', $stall['stall_operating_hours']);
    foreach ($hoursArray as $hours) {
        if (strpos($hours, '<br>') !== false) {
            list($days, $timeRange) = explode('<br>', $hours);
            $today = date('D'); 
            if (stripos($days, $today) !== false) {
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

if (!$isOpen) {
    // Redirect to park page if stall is closed using javascript
    echo '<script>window.location.href = "park.php";</script>';
    exit;
}
?>
<style>
  .liked {
      background-color: gray; 
  }
  .closed {
      z-index: 2;
  }
  .disabled-card {
      pointer-events: none;
  }
  .disabled-btn {
      background-color: gray !important;
      cursor: not-allowed;
  }
  .disabled-plus {
      color: gray;
      pointer-events: none;
  }
  .variationitem-disabled {
    pointer-events: none;
}
.variationitem-disabled img {
    opacity: 0.5;
}
.variationitem-disabled span {
    color: #6c757d;
}
.variationitem-disabled:hover {
    background-color: #FFF5E4;
}
</style>
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
                        <?php if ($isOpen): ?>
                            <span class="pageon">Open now</span>
                        <?php else: ?>
                            <span class="pageon">Closed now</span>
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

    <?php if (!empty($popularProducts)): ?>
        <section id="popular" class="pt-3 mt-3">
            <h5>Popular Products</h5>
            <div class="row row-cols-1 row-cols-md-4 g-3">
                <?php foreach ($popularProducts as $product): 
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
                                        <p class="card-text text-muted m-0"><?= htmlspecialchars($cat['name']); ?></p>
                                        <h5 class="card-title my-2"><?= htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text text-muted m-0"><?= htmlspecialchars($product['description']); ?></p>
                                        <?php
                                             $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                            if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                $today >= $product['start_date'] && $today <= $product['end_date']) {
                                                $discountedPrice = $product['base_price'] * ((100 - $product['discount']) / 100);
                                        ?>
                                                <div class="my-3">
                                                    <span class="proprice">₱<?= number_format($discountedPrice, 2); ?></span>
                                                    <span class="pricebefore small">₱<?= number_format($product['base_price'], 2); ?></span>
                                                </div>
                                        <?php } else { ?>
                                                <div class="my-3">
                                                    <span class="proprice">₱<?= number_format($product['base_price'], 2); ?></span>
                                                </div>
                                        <?php } ?>
                                        <div class="m-0">
                                            <?php if (in_array($product['id'], $popularProdIds)) { ?>
                                                <span class="opennow">Popular</span>
                                            <?php } ?>
                                            <?php if (in_array($product['id'], $promoProdIds)) { 
                                                 $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                                if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                    $today >= $product['start_date'] && $today <= $product['end_date']) { ?>
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
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($newProducts)): ?>
        <section id="new" class="pt-3 mt-3">
            <h5>New Products</h5>
            <div class="row row-cols-1 row-cols-md-4 g-3">
                <?php foreach ($newProducts as $product): 
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
                                        <p class="card-text text-muted m-0"><?= htmlspecialchars($cat['name']); ?></p>
                                        <h5 class="card-title my-2"><?= htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text text-muted m-0"><?= htmlspecialchars($product['description']); ?></p>
                                        <?php
                                             $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                            if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                $today >= $product['start_date'] && $today <= $product['end_date']) {
                                                $discountedPrice = $product['base_price'] * ((100 - $product['discount']) / 100);
                                        ?>
                                                <div class="my-3">
                                                    <span class="proprice">₱<?= number_format($discountedPrice, 2); ?></span>
                                                    <span class="pricebefore small">₱<?= number_format($product['base_price'], 2); ?></span>
                                                </div>
                                        <?php } else { ?>
                                                <div class="my-3">
                                                    <span class="proprice">₱<?= number_format($product['base_price'], 2); ?></span>
                                                </div>
                                        <?php } ?>
                                        <div class="m-0">
                                            <?php if (in_array($product['id'], $popularProdIds)) { ?>
                                                <span class="opennow">Popular</span>
                                            <?php } ?>
                                            <?php if (in_array($product['id'], $promoProdIds)) { 
                                                 $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                                if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                    $today >= $product['start_date'] && $today <= $product['end_date']) { ?>
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
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($promoProducts)): ?>
        <section id="promo" class="pt-3 mt-3">
            <h5>Promo Products</h5>
            <div class="row row-cols-1 row-cols-md-4 g-3">
                <?php foreach ($promoProducts as $product): 
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
                                        <p class="card-text text-muted m-0"><?= htmlspecialchars($cat['name']); ?></p>
                                        <h5 class="card-title my-2"><?= htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text text-muted m-0"><?= htmlspecialchars($product['description']); ?></p>
                                        <?php
                                             $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                            if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                $today >= $product['start_date'] && $today <= $product['end_date']) {
                                                $discountedPrice = $product['base_price'] * ((100 - $product['discount']) / 100);
                                        ?>
                                                <div class="my-3">
                                                    <span class="proprice">₱<?= number_format($discountedPrice, 2); ?></span>
                                                    <span class="pricebefore small">₱<?= number_format($product['base_price'], 2); ?></span>
                                                </div>
                                        <?php } else { ?>
                                                <div class="my-3">
                                                    <span class="proprice">₱<?= number_format($product['base_price'], 2); ?></span>
                                                </div>
                                        <?php } ?>
                                        <div class="m-0">
                                            <?php if (in_array($product['id'], $popularProdIds)) { ?>
                                                <span class="opennow">Popular</span>
                                            <?php } ?>
                                            <?php if (in_array($product['id'], $promoProdIds)) { 
                                                 $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                                if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                    $today >= $product['start_date'] && $today <= $product['end_date']) { ?>
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
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

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
                                                <p class="card-text text-muted m-0"><?= htmlspecialchars($cat['name']); ?></p>
                                                <h5 class="card-title my-2"><?= htmlspecialchars($product['name']); ?></h5>
                                                <p class="card-text text-muted m-0"><?= htmlspecialchars($product['description']); ?></p>
                                                <?php
                                                     $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    }                                                    
                                                    if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                        $today >= $product['start_date'] && $today <= $product['end_date']) {
                                                        $discountedPrice = $product['base_price'] * ((100 - $product['discount']) / 100);
                                                ?>
                                                        <div class="my-3">
                                                            <span class="proprice">₱<?= number_format($discountedPrice, 2); ?></span>
                                                            <span class="pricebefore small">₱<?= number_format($product['base_price'], 2); ?></span>
                                                        </div>
                                                <?php } else { ?>
                                                        <div class="my-3">
                                                            <span class="proprice">₱<?= number_format($product['base_price'], 2); ?></span>
                                                        </div>
                                                <?php } ?>
                                                <div class="m-0">
                                                    <?php if (in_array($product['id'], $popularProdIds)) { ?>
                                                        <span class="opennow">Popular</span>
                                                    <?php } ?>
                                                    <?php if (in_array($product['id'], $promoProdIds)) { 
                                                         $today = date('Y-m-d');
                                                        if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                            $product['discount'] = 0.00;
                                                            $product['start_date'] = null;
                                                            $product['end_date'] = null;
                                                        } 
                                                        if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                            $today >= $product['start_date'] && $today <= $product['end_date']) { ?>
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
                            <!-- Modal for this product -->
                            <div class="modal fade menumodal" id="menumodal<?= $product['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?= $product['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form class="modal-content">
                                        <div class="modal-body p-0">
                                            <div class="card border-0 position-relative rounded-0">
                                                <img src="<?= htmlspecialchars($product['image']); ?>" class="card-img-top custom-img rounded-0" alt="<?= htmlspecialchars($product['name']); ?>">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <div class="card-body">
                                                    <p class="card-text text-muted m-0"><?= htmlspecialchars($cat['name']); ?></p>
                                                    <h5 class="card-title my-2"><?= htmlspecialchars($product['name']); ?></h5>
                                                    <p class="card-text text-muted m-0"><?= htmlspecialchars($product['description']); ?></p>
                                                    <?php
                                                         $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                                        if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                            $today >= $product['start_date'] && $today <= $product['end_date']) {
                                                            $discountedPrice = $product['base_price'] * ((100 - $product['discount']) / 100);
                                                    ?>
                                                            <div class="my-3">
                                                                <span class="proprice">₱<?= number_format($discountedPrice, 2); ?></span>
                                                                <span class="pricebefore small">₱<?= number_format($product['base_price'], 2); ?></span>
                                                            </div>
                                                    <?php } else { ?>
                                                            <div class="my-3">
                                                                <span class="proprice">₱<?= number_format($product['base_price'], 2); ?></span>
                                                            </div>
                                                    <?php } ?>
                                                    <div class="m-0">
                                                        <?php if (in_array($product['id'], $popularProdIds)) { ?>
                                                            <span class="opennow">Popular</span>
                                                        <?php } ?>
                                                        <?php if (in_array($product['id'], $promoProdIds)) { 
                                                             $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount'] = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date'] = null;
                                                    } 
                                                            if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) &&
                                                                $today >= $product['start_date'] && $today <= $product['end_date']) { ?>
                                                                <span class="discount"><?= intval($product['discount']); ?>% off</span>
                                                        <?php } } ?>
                                                        <?php if (in_array($product['id'], $newProdIds)) { ?>
                                                            <span class="newopen">New</span>
                                                        <?php } ?>
                                                    </div>
                                                    <hr>
                                                    <?php 
                                                        $variations = $stallObj->getProductVariations($product['id']);
                                                        if (!empty($variations)):
                                                    ?>
                                                            <?php foreach ($variations as $variation): ?>
                                                                <div class="vrtn mt-3">
                                                                    <div class="variation-group mb-3" data-variation-id="<?= $variation['id']; ?>">
                                                                        <div class="d-flex justify-content-between variation mb-2">
                                                                            <div>
                                                                                <h5 class="mb-0"><?= htmlspecialchars($variation['name']); ?></h5>
                                                                                <span class="mt-2">Select 1</span>
                                                                            </div>
                                                                            <span class="mx-2 variationspan rounded-4 px-2 py-1 m-0">Required</span>
                                                                        </div>
                                                                        <?php foreach ($stallObj->getVariationOptions($variation['id']) as $option):
                                                                            $optionStock = $stallObj->getStock($product['id'], $option['id']);
                                                                        ?>
                                                                            <div class="d-flex align-items-center justify-content-between variationitem mb-2 <?= ($optionStock <= 0 ? 'variationitem-disabled' : ''); ?>" onclick="if(!this.querySelector('input').disabled){ document.getElementById('variation<?= $option['id']; ?>').click(); }">
                                                                                <div class="form-check d-flex gap-2 align-items-center">
                                                                                    <input 
                                                                                        class="form-check-input" 
                                                                                        type="radio" 
                                                                                        name="variation_<?= $variation['id']; ?>"  
                                                                                        id="variation<?= $option['id']; ?>"
                                                                                        data-addprice="<?= $option['add_price']; ?>"
                                                                                        data-subtractprice="<?= $option['subtract_price']; ?>"
                                                                                        data-stock="<?= $optionStock; ?>"
                                                                                        <?= ($optionStock <= 0 ? 'disabled' : ''); ?>
                                                                                        onchange="updateAddToCartButtonState(<?= $product['id']; ?>)"
                                                                                    >
                                                                                    <img src="<?= htmlspecialchars($option['image']); ?>" alt="<?= htmlspecialchars($option['name']); ?>" width="45px" height="45px" class="rounded-2">
                                                                                    <label class="form-check-label" for="variation<?= $option['id']; ?>">
                                                                                        <?= htmlspecialchars($option['name']); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <span class="px-2">
                                                                                    <?= ($option['add_price'] > 0)
                                                                                        ? '+ ₱' . number_format($option['add_price'], 2)
                                                                                        : (($option['subtract_price'] > 0)
                                                                                            ? '- ₱' . number_format($option['subtract_price'], 2)
                                                                                            : 'Free'); ?>
                                                                                </span>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                    <?php endif; ?>
                                                    <div class="speins mt-4 mb-5">
                                                        <div class="mb-3">
                                                            <h5 class="mb-0">Special Instructions</h5>
                                                            <span class="mt-2">Special requests are subject to the restaurant's approval. Tell us here!</span>
                                                        </div>
                                                        <div class="input-group m-0">
                                                            <textarea 
                                                                name="specialinstructions<?= $product['id'] ?>" 
                                                                id="specialinstructions<?= $product['id'] ?>" 
                                                                placeholder="e.g. No mayo (Optional)" 
                                                                class="rounded-2" 
                                                                rows="3"
                                                            ></textarea>
                                                        </div>                
                                                    </div>
                                                    <span class="proprice" style="display:none;">₱<?= number_format($product['base_price'], 2); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 ordquantity">
                                            <div class="d-flex align-items-center">
                                                <i class="fa-solid fa-minus" onclick="updateQuantity(<?= $product['id']; ?>, -1)"></i>
                                                <span id="quantity<?= $product['id']; ?>" class="ordquanum"
                                                    <?php if(empty($variations)) echo 'data-stock="' . ($product['stock'] ?? 0) . '"'; ?>
                                                >1</span>
                                                <?php if(!empty($variations)): ?>
                                                    <i class="fa-solid fa-plus disabled-plus" id="plusBtn<?= $product['id']; ?>"></i>
                                                    <?php else: ?>
                                                        <i class="fa-solid fa-plus <?php echo (($product['stock'] ?? 0) <= 1 ? 'disabled-plus' : ''); ?>" 
                                                        id="plusBtn<?= $product['id']; ?>" 
                                                        onclick="updateQuantity(<?= $product['id']; ?>, 1)"></i>
                                                    <?php endif; ?>
                                            </div>
                                            <?php if(!empty($variations)): ?>
                                                <button type="button" class="btn btn-primary w-100 disabled-btn" id="addToCartBtn<?= $product['id']; ?>" onclick="addToCart(<?= $product['id']; ?>)" disabled>
                                                    Add to cart
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-primary w-100" id="addToCartBtn<?= $product['id']; ?>" onclick="addToCart(<?= $product['id']; ?>)">
                                                    Add to cart
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <script>
                                            const modalElement = document.getElementById("menumodal<?= $product['id']; ?>");
                                            modalElement.addEventListener("show.bs.modal", function() {
                                                updateAddToCartButtonState(<?= $product['id']; ?>);
                                                updateModalPlusState(<?= $product['id']; ?>);
                                            });
                                            
                                            function updateAddToCartButtonState(productId) {
                                                const modal = document.getElementById("menumodal" + productId);
                                                const variationGroups = modal ? modal.querySelectorAll(".variation-group") : [];
                                                const addToCartBtn = document.getElementById("addToCartBtn" + productId);
                                                const plusBtn = document.getElementById("plusBtn" + productId);
                                                let allSelected = true;
                                                
                                                variationGroups.forEach(group => {
                                                    if (!group.querySelector("input[type='radio']:checked")) {
                                                        allSelected = false;
                                                    }
                                                });
                                                
                                                if (allSelected) {
                                                    addToCartBtn.classList.remove("disabled-btn");
                                                    addToCartBtn.disabled = false;
                                                    updateModalPlusState(productId);
                                                } else {
                                                    addToCartBtn.classList.add("disabled-btn");
                                                    addToCartBtn.disabled = true;
                                                    if (plusBtn) {
                                                        plusBtn.classList.add("disabled-plus");
                                                        plusBtn.onclick = null;
                                                    }
                                                }
                                            }
                                            
                                            function updateModalPlusState(productId) {
                                                const quantitySpan = document.getElementById("quantity" + productId);
                                                let currentQuantity = parseInt(quantitySpan.innerText);
                                                let maxAvailable = 0;
                                                const modal = document.getElementById("menumodal" + productId);
                                                const variationGroups = modal ? modal.querySelectorAll(".variation-group") : [];
                                                
                                                if (variationGroups.length > 0) {
                                                    let allSelected = true;
                                                    let selectedStocks = [];
                                                    variationGroups.forEach(group => {
                                                        const selected = group.querySelector("input[type='radio']:checked");
                                                        if (!selected) {
                                                            allSelected = false;
                                                        } else {
                                                            selectedStocks.push(parseInt(selected.dataset.stock));
                                                        }
                                                    });
                                                    maxAvailable = allSelected ? Math.min(...selectedStocks) : 1;
                                                } else {
                                                    maxAvailable = parseInt(quantitySpan.dataset.stock);
                                                }
                                                
                                                const plusBtn = document.getElementById("plusBtn" + productId);
                                                if (currentQuantity >= maxAvailable) {
                                                    plusBtn.classList.add("disabled-plus");
                                                    plusBtn.onclick = null;
                                                } else {
                                                    plusBtn.classList.remove("disabled-plus");
                                                    plusBtn.onclick = function() { updateQuantity(productId, 1); };
                                                }
                                            }
                                            
                                            function updateQuantity(productId, change) {
                                                const quantitySpan = document.getElementById("quantity" + productId);
                                                let currentQuantity = parseInt(quantitySpan.innerText);
                                                let maxAvailable = 0;
                                                const modal = document.getElementById("menumodal" + productId);
                                                const variationGroups = modal ? modal.querySelectorAll(".variation-group") : [];
                                                
                                                if (variationGroups.length > 0) {
                                                    let allSelected = true;
                                                    let selectedStocks = [];
                                                    variationGroups.forEach(group => {
                                                        const selected = group.querySelector("input[type='radio']:checked");
                                                        if (!selected) {
                                                            allSelected = false;
                                                        } else {
                                                            selectedStocks.push(parseInt(selected.dataset.stock));
                                                        }
                                                    });
                                                    maxAvailable = allSelected ? Math.min(...selectedStocks) : 1;
                                                } else {
                                                    maxAvailable = parseInt(quantitySpan.dataset.stock);
                                                }
                                                
                                                let newQuantity = currentQuantity + change;
                                                newQuantity = Math.max(1, newQuantity);
                                                if (newQuantity > maxAvailable) {
                                                    newQuantity = maxAvailable;
                                                }
                                                quantitySpan.innerText = newQuantity;
                                                updateModalPlusState(productId);
                                                updateAddToCartButtonState(productId);
                                            }
                                            
                                            function addToCart(productId) {
                                                const addToCartBtn = document.getElementById("addToCartBtn" + productId);
                                                if (addToCartBtn.disabled) { return; }
                                                
                                                let quantity = parseInt(document.getElementById("quantity" + productId).innerText);
                                                let specialInstructions = document.getElementById("specialinstructions" + productId)?.value || '';
                                                let modal = document.getElementById("menumodal" + productId);
                                                let variationGroups = modal.querySelectorAll(".variation-group");
                                                let variationOptionIds = [];
                                                let priceElement = modal.querySelector(".proprice");
                                                
                                                if (!priceElement) {
                                                    console.error("Price element (.proprice) not found in modal.");
                                                    return;
                                                }
                                                
                                                let basePriceText = priceElement.innerText;
                                                let basePrice = parseFloat(basePriceText.replace("₱", "").trim());
                                                
                                                variationGroups.forEach(group => {
                                                    let checked = group.querySelector("input[type='radio']:checked");
                                                    if (checked) {
                                                        variationOptionIds.push(checked.id.replace("variation", ""));
                                                    }
                                                });
                                                
                                                let params = "product_id=" + productId + "&quantity=" + quantity + "&base_price=" + basePrice + "&request=" + encodeURIComponent(specialInstructions);
                                                
                                                variationOptionIds.forEach(id => {
                                                    params += "&variation_options[]=" + id;
                                                });
                                                
                                                let xhr = new XMLHttpRequest();
                                                xhr.open("POST", "add_to_cart.php", true);
                                                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                                xhr.onreadystatechange = function () {
                                                    if (xhr.readyState === 4 && xhr.status === 200) {
                                                        alert(xhr.responseText);
                                                    }
                                                };
                                                xhr.send(params);
                                            }
                                        </script>
                                    </form>
                                </div>
                            </div>
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