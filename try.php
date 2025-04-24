<?php
    include_once 'links.php'; 
    include_once 'header.php'; 
    require_once __DIR__ . '/classes/product.class.php';
    require_once __DIR__ . '/classes/stall.class.php';
    require_once __DIR__ . '/classes/park.class.php';
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

    date_default_timezone_set('Asia/Manila');
    $currentTime = date('H:i');
    $currentDay  = date('l'); 

    $isOpen = false;
    if (!empty($stall['stall_operating_hours'])) {
        $hoursArray = explode('; ', $stall['stall_operating_hours']);
        foreach ($hoursArray as $hours) {
            if (strpos($hours, '<br>') !== false) {
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

    $park = $parkObj->getPark($stall['park_id']);
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

    if (isset($_POST['report_submit'])) {
        if (isset($_SESSION['user'])) {
            $reported_by  = $_SESSION['user']['id'];
            $reported_stall = $_POST['reported_stall'];
            $reason       = $_POST['reason'];
            if ($userObj->reportFoodStall($reported_by, $reported_stall, $reason)) {
                echo "<script>Swal.fire({icon: 'success', title: 'Report Sent!', text: 'Your report was submitted.', confirmButtonColor: '#CD5C08'});</script>";
            } else {
                echo "<script>Swal.fire({icon: 'error', title: 'Submission Failed', text: 'There was an error submitting your report. Please try again.', confirmButtonColor: '#CD5C08'});</script>";
            }
        } else {
            echo "<script>Swal.fire({icon: 'warning', title: 'Login Required', text: 'Please log in to submit a report.', confirmButtonColor: '#CD5C08'});</script>";
        }
    }
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<div style="background-color: #f4f4f4;">

    <div class="disabled" <?php if(isset($stall['status']) && $stall['status'] === 'Unavailable') { echo 'style="pointer-events: none; opacity: 0.5;"'; } ?>>

        <?php if (empty($products)): ?>
            <br><br>
            <section class="mt-5 text-center">
                No products are available in this stall at the moment.
            </section>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                <section id="category<?= $cat['id']; ?>" class="pt-3 mt-3">
                    <h5><?= htmlspecialchars($cat['name']); ?></h5>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
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
                                                        <p class="card-text text-muted m-0"> <?= htmlspecialchars($product['category_name']); ?></p>
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

                                                        <!-- change 1 -->
                                                        <div class="mb-3">
                                                            <div class="text-small">
                                                                <div data-coreui-toggle="rating" data-coreui-value="3"></div>
                                                                <span>3.5/5</span>
                                                                <span class="text-muted">(156)</span>
                                                            </div>
                                                            <button data-toggle="modal" data-target="#productratings">See reviews</button>
                                                        </div>
                                  
                                                        <div class="modal fade" id="productratings" tabindex="-1" role="dialog" aria-labelledby="productratingsTitle" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLongTitle">Reviews</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                                            <div>
                                                                                <h1>3.5</h1>
                                                                                <div data-coreui-toggle="rating" data-coreui-value="3"></div>
                                                                                <span>All ratings (156)</span>
                                                                            </div>
                                                                            <div class="nav-container d-flex gap-3 my-2 flex-wrap">
                                                                                <a href="#all" class="nav-link" data-target="all">Top reviews</a>
                                                                                <a href="#newest" class="nav-link" data-target="applications">Newest</a>
                                                                                <a href="#highestrating" class="nav-link" data-target="reports">Highest Rating</a>
                                                                                <a href="#lowestrating" class="nav-link" data-target="reports">Lowest Rating</a>
                                                                            </div>
                                                                            <div id="all" class="border rounded-2 p-3 bg-white section-content">
                                                                                <h5>Username</h5>
                                                                                <div class="d-flex">
                                                                                    <div data-coreui-toggle="rating" data-coreui-value="3"></div>
                                                                                    <span>2025-04-23 20:15</span>
                                                                                </div>
                                                                                <p>fast hot delicious</p>
                                                                                <span><i class="fa-solid fa-thumbs-up"></i>Helpful</span>
                                                                            </div>

                                                                            <div id="newest" class="border rounded-2 p-3 bg-white section-content">
                                                                                
                                                                            </div>

                                                                            <div id="highestrating" class="border rounded-2 p-3 bg-white section-content">
                                                                                
                                                                            </div>

                                                                            <div id="lowestrating" class="border rounded-2 p-3 bg-white section-content">
                                                                                
                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

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
                        <?php 
                                endif;
                            endforeach;
                        ?>
                        <?php if (!$hasProducts): ?>
                            <div class="w-100">
                               No products found for this category.
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php foreach ($products as $product): ?>
                    <!-- Modal for this product -->
                    <div class="modal fade menumodal" id="menumodal<?= $product['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?= $product['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form class="modal-content">
                                <div class="modal-body p-0">
                                    <div class="card border-0 position-relative rounded-0">
                                        <img src="<?= htmlspecialchars($product['image']); ?>" class="card-img-top custom-img rounded-0" alt="<?= htmlspecialchars($product['name']); ?>">
                                        <div class="card-body">
                                            <p class="card-text text-muted m-0"> <?= htmlspecialchars($product['category_name']); ?></p>
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

                                            <?php 
                                            $variations = $stallObj->getProductVariations($product['id']);
                                            if (!empty($variations)) {
                                                $totalVariationStock = 0;
                                                foreach ($variations as $variation) {
                                                    foreach ($stallObj->getVariationOptions($variation['id']) as $option) {
                                                        $totalVariationStock += $stallObj->getStock($product['id'], $option['id']);
                                                    }
                                                }
                                                $displayStock = $totalVariationStock;
                                            } else {
                                                $displayStock = $product['stock'] ?? 0;
                                            }
                                            ?>
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
                                                            <?php 
                                                            foreach ($stallObj->getVariationOptions($variation['id']) as $option):
                                                                $optionStock = $stallObj->getStock($product['id'], $option['id']);
                                                            ?>
                                                                <div class="d-flex align-items-center justify-content-between variationitem mb-2 <?= ($optionStock <= 0 ? 'variationitem-disabled' : ''); ?>" onclick="if(!this.querySelector('input').disabled){ document.getElementById('variation<?= $option['id']; ?>').click(); }">
                                                                    <span class="small text-center me-4 pe-2 border-end" style="color: #CD5C08;">Stocks<br><?= $optionStock; ?></span>
                                                                    <div class="form-check d-flex gap-2 align-items-center flex-grow-1">
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
                                    <span class="small text-center" style="color: #CD5C08;">
                                        <?= $displayStock; ?> stocks available
                                    </span>
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
                                                Swal.fire(xhr.responseText);
                                            }
                                        };
                                        xhr.send(params);
                                    }
                                </script>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>

        <script src="assets/js/navigation.js?v=<?= time(); ?>"></script>
    </div>
    <br><br><br>
</div>

<?php
    include_once 'modals.php'; 
    include_once './footer.php'; 
?> 