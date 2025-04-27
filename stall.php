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

    if (isset($user_id)) {
        $isOwner = $stallObj->isOwner($stall_id, $user_id);
    } else {
        $isOwner = false;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_submit'])) {
        $rid      = intval($_POST['review_id']);
        $response = trim($_POST['seller_response']);
        if ($isOwner && $rid && $response !== '') {
            $stallObj->saveSellerResponse($rid, $response);
            echo "<script>
                Swal.fire({ icon: 'success', title: 'Reply submitted!', text: 'Your stall response has been saved.', showConfirmButton: false });
            </script>";
        }
        $u = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES);
        echo "<meta http-equiv=\"refresh\" content=\"2;url={$u}\">";
        exit;
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
.ratemodal .modal-header,
.ratemodal .modal-footer {
    position: sticky;
    z-index: 1020;
    background-color: white;
}
.ratemodal .modal-header { top: 0; }
.ratemodal .modal-footer { bottom: 0; }
.ratemodal { max-height: 80vh; overflow-y: auto; }
.modal-dialog { max-width: 50vw; }
.helpful-toggle.active { color: #CD5C08; }
.helpful-toggle.active i.fa-regular { display: none; }
.helpful-toggle.active i.fa-solid  { display: inline-block; }
.helpful-toggle i.fa-solid         { display: none; }
textarea:focus { outline: none; box-shadow: none; border: 1px solid #ccc; }
</style>
<div style="background-color: #f4f4f4;">

    <div class="pageinfo pb-4">
        <div class="d-flex justify-content-between">
            <div class="d-flex gap-4 align-items-center pagelogo">
                <img src="<?= htmlspecialchars($stall['logo']); ?>" alt="Stall Logo">
                <div>
                    <div class="d-flex gap-2 align-items-center flex-wrap lh-1">
                        <?php 
                        $stall_categories = explode(',', $stall['stall_categories']); 
                        foreach ($stall_categories as $index => $cat): ?>
                            <p class="card-text text-muted m-0"><?= trim($cat); ?></p>
                            <?php if ($index !== array_key_last($stall_categories)): ?>
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <h5 class="my-2 fw-bold fs-2"><?= $stall['name']; ?></h5>
                    <p class="text-muted m-0"><?= $stall['description']; ?></p>
                    <div class="stall-info">
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

                            <div class="mx-2 d-flex align-items-center gap-1">
                                <?php 
                                    $stallStats = $productObj->getStallAverageRating($stall_id);
                                    $stallCount = $productObj->getStallRatingCount($stall_id);
                                ?>
                                <div data-coreui-item-count="1" data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="<?= round($stallStats); ?>"></div>
                                <span><?= number_format($stallStats,1); ?>/5.0</span>
                                <span class="text-muted">(<?= $stallCount; ?>)</span>
                                <button type="button" class="conopepay" data-bs-toggle="modal" data-bs-target="#stallReviewsModal">See reviews</button>
                            </div>

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
            </div>
            <div class="d-flex align-items-center">
                <div class="stall-action">
                    <?php if (isset($user_id) && $user_id == $stall['user_id']): ?>
                        <button class="pagelike" onclick="window.location.href='editpage.php?id=<?= urlencode(encrypt($stall['id'])) ?>&source=stall';">Edit Page</button>
                        <?php else:
                            if (isset($user_id)) { ?>
                                <button id="likeBtn" class="pagelike <?= $likedByUser ? 'liked' : ''; ?>">
                                    <?= $likedByUser ? 'Liked' : 'Like'; ?>
                                </button>
                        <?php } else {
                            ?>  
                            <button id="likeBtn" class="pagelike" onclick="window.location.href='signin.php';">Like</button>
                        <?php } ?>
                    <?php endif; ?>
                </div>
                <?php if ($user && isset($user['role']) && $user['role'] === 'Customer'): ?>
                    <div class="dropdown" style="align-self: flex-start;">
                        <i class="fa-solid fa-ellipsis-vertical mores" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"></i>
                        <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#reportstall" data-reported_stall="<?= htmlspecialchars($stall['id']); ?>">Report</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal fade" id="reportstall" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <form method="POST" action="">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="text-center">
                                    <h4 class="fw-bold mb-4">Why are you reporting this?</h4>
                                    <div class="form-floating m-0">
                                        <textarea class="form-control" name="reason" placeholder="Reason" id="reason" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                                        <label for="reason">Reason</label>
                                    </div>
                                    <input type="hidden" name="reported_stall" id="reported_stall" value="">
                                    <div class="mt-4 mb-3">
                                        <input type="submit" name="report_submit" value="Submit" class="button" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                const reportStallModal = document.getElementById('reportstall');
                reportStallModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const reportedStall = button.getAttribute('data-reported_stall');
                    document.getElementById('reported_stall').value = reportedStall ? reportedStall : '';
                });
            </script>
        </div>
        <div class="stall-info-tm mt-2">
            <div class="info-tm">
                <div class="d-flex gap-2 align-items-center other-tm">
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
                <div class="d-flex gap-5 m-0 other-tm">
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
            <div >
                <?php if (isset($user_id) && $user_id == $stall['user_id']): ?>
                    <button class="pagelike" onclick="window.location.href='editpage.php?id=<?= urlencode(encrypt($stall['id'])) ?>&source=stall';">Edit Page</button>
                    <?php else: ?>
                    <button id="likeBtn" class="pagelike <?= $likedByUser ? 'liked' : ''; ?>">
                        <?= $likedByUser ? 'Liked' : 'Like'; ?>
                    </button>
                <?php endif; ?>
            </div> 
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

    <div class="disabled" <?php if(isset($stall['status']) && $stall['status'] === 'Unavailable') { echo 'style="pointer-events: none; opacity: 0.5;"'; } ?>>
        <section id="searchResultsSection" class="pt-3 mt-3" style="display: none; ">
            <h5 id="searchHeader"></h5>
            <div id="searchResultsContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3"></div>
        </section>

        <?php if (!empty($popularProducts)): ?>
            <section id="popular" class="pt-3 mt-3">
                <h5>Popular Products</h5>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
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
                                            <?php $stats = $productObj->getProductRatingStats($product['id']); ?>
                                            <div class="mb-3 d-flex align-items-center small gap-1">
                                                <div data-coreui-item-count="1" data-coreui-size="sm" data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="1"></div>
                                                <span><?= number_format($stats['avg_rating'],1); ?>/5.0</span>
                                                <span class="text-muted">(<?= $stats['total_ratings']; ?>)</span>
                                                <button type="button" class="bg-white text-decoration-none border-0 rename py-1 px-2 text-dark" data-bs-toggle="modal" data-bs-target="#productratingsModal<?= $product['id']; ?>">See reviews</button>
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
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($newProducts)): ?>
            <section id="new" class="pt-3 mt-3">
                <h5>New Products</h5>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
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
                                            <?php $stats = $productObj->getProductRatingStats($product['id']); ?>
                                            <div class="mb-3 d-flex align-items-center small gap-1">
                                                <div data-coreui-item-count="1" data-coreui-size="sm" data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="1"></div>
                                                <span><?= number_format($stats['avg_rating'],1); ?>/5.0</span>
                                                <span class="text-muted">(<?= $stats['total_ratings']; ?>)</span>
                                                <button type="button" class="bg-white text-decoration-none border-0 rename py-1 px-2 text-dark" data-bs-toggle="modal" data-bs-target="#productratingsModal<?= $product['id']; ?>">See reviews</button>
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
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($promoProducts)): ?>
            <section id="promo" class="pt-3 mt-3">
                <h5>Promo Products</h5>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
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
                                            <?php $stats = $productObj->getProductRatingStats($product['id']); ?>
                                            <div class="mb-3 d-flex align-items-center small gap-1">
                                                <div data-coreui-item-count="1" data-coreui-size="sm" data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="1"></div>
                                                <span><?= number_format($stats['avg_rating'],1); ?>/5.0</span>
                                                <span class="text-muted">(<?= $stats['total_ratings']; ?>)</span>
                                                <button type="button" class="bg-white text-decoration-none border-0 rename py-1 px-2 text-dark" data-bs-toggle="modal" data-bs-target="#productratingsModal<?= $product['id']; ?>">See reviews</button>
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
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

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
                        <?php $hasProducts = false; foreach ($products as $product): ?>
                            <?php if ($product['category_id'] == $cat['id']): ?>
                                <?php
                                    $hasProducts = true;
                                    $variations   = $stallObj->getProductVariations($product['id']);
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
                                            if (!$variationHasStock) { $cardClickable = false; break; }
                                        }
                                    } else {
                                        $cardClickable = (($product['stock'] ?? 0) > 0);
                                    }
                                ?>
                                <div class="col">
                                    <a href="#" class="card-link text-decoration-none<?= $cardClickable ? '" data-bs-toggle="modal" data-bs-target="#menumodal' . $product['id'] . '"' : ' disabled-card"'; ?>>
                                        <div class="card position-relative">
                                            <?php if (!$cardClickable): ?>
                                                <div class="closed">No stock</div>
                                            <?php endif; ?>
                                            <img src="<?= htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']); ?>">
                                            <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                                            <div class="card-body">
                                                <p class="card-text text-muted m-0"><?= htmlspecialchars($product['category_name']); ?></p>
                                                <h5 class="card-title my-2"><?= htmlspecialchars($product['name']); ?></h5>
                                                <p class="card-text text-muted m-0"><?= htmlspecialchars($product['description']); ?></p>
                                                <?php
                                                    $today = date('Y-m-d');
                                                    if ($product['discount'] > 0 && !is_null($product['end_date']) && $today > $product['end_date']) {
                                                        $product['discount']   = 0.00;
                                                        $product['start_date'] = null;
                                                        $product['end_date']   = null;
                                                    }
                                                    if ($product['discount'] > 0 && !is_null($product['start_date']) && !is_null($product['end_date']) && $today >= $product['start_date'] && $today <= $product['end_date']) {
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
                                                <?php $stats = $productObj->getProductRatingStats($product['id']); ?>
                                                <div class="mb-3 d-flex align-items-center small gap-1">
                                                    <div data-coreui-item-count="1" data-coreui-size="sm" data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="1"></div>
                                                    <span><?= number_format($stats['avg_rating'],1); ?>/5.0</span>
                                                    <span class="text-muted">(<?= $stats['total_ratings']; ?>)</span>
                                                    <button type="button" class="bg-white text-decoration-none border-0 rename py-1 px-2 text-dark" data-bs-toggle="modal" data-bs-target="#productratingsModal<?= $product['id']; ?>">See reviews</button>
                                                </div>
                                                <div class="m-0">
                                                    <?php if (in_array($product['id'], $popularProdIds)): ?><span class="opennow">Popular</span><?php endif; ?>
                                                    <?php if (in_array($product['id'], $promoProdIds) && $product['discount'] > 0 && $today >= $product['start_date'] && $today <= $product['end_date']): ?><span class="discount"><?= intval($product['discount']); ?>% off</span><?php endif; ?>
                                                    <?php if (in_array($product['id'], $newProdIds)): ?><span class="newopen">New</span><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!$hasProducts): ?>
                            <div class="w-100">No products found for this category.</div>
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
                                            <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                    Swal.fire({icon: 'success', title: 'Added to Cart', text: xhr.responseText, confirmButtonColor: '#CD5C08'});
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

        <?php foreach ($products as $product): ?>
            <?php
                $stats          = $productObj->getProductRatingStats($product['id']);
                $breakdown      = $productObj->getProductRatingBreakdown($product['id']);
                $reviews        = $productObj->getProductReviews($product['id']);
                $reviews_top    = $reviews;
                $reviews_newest = $reviews;
                usort($reviews_newest, fn($a,$b) => strtotime($b['created_at']) - strtotime($a['created_at']));
                $reviews_high   = $reviews;
                usort($reviews_high, fn($a,$b) => $b['rating_value'] - $a['rating_value']);
                $reviews_low    = $reviews;
                usort($reviews_low, fn($a,$b) => $a['rating_value'] - $b['rating_value']);
                $sid = $product['id'];
            ?>
            <div class="modal fade" id="productratingsModal<?= $sid; ?>" tabindex="-1" role="dialog" aria-labelledby="productratingsTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content ratemodal">
                        <div class="modal-header">
                            <h5 class="modal-title">Rate &amp; Reviews</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="background:#F4F4F4;">
                            <div class="d-flex justify-content-between mb-3 p-4 border rounded bg-white">
                                <div class="w-50">
                                    <h1 class="m-0 fw-bold"><?= number_format($stats['avg_rating'],1); ?></h1>
                                    <div data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="<?= round($stats['avg_rating']); ?>"></div>
                                    <span>All ratings (<?= $stats['total_ratings']; ?>)</span>
                                </div>
                                <div class="w-50">
                                    <?php foreach (range(5,1) as $star):
                                        $count = $breakdown[$star] ?? 0;
                                        $pct   = $stats['total_ratings'] > 0 ? ($count/$stats['total_ratings']*100) : 0;
                                    ?>
                                        <div class="d-flex align-items-center small gap-1">
                                            <span><?= $star; ?></span>
                                            <div data-coreui-item-count="1" data-coreui-size="sm" data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="1"></div>
                                            <div class="w3-light-grey w3-round-xlarge" style="width:100%;height:10px;">
                                                <?php if ($count > 0): ?><div class="w3-container w3-round-xlarge" style="width:<?= $pct; ?>%;height:10px;background:#CD5C08"></div><?php endif; ?>
                                            </div>
                                            <span><?= $count; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="nav-container d-flex gap-3 mb-2">
                                <a href="#all<?= $sid; ?>" class="nav-link active">Top reviews</a>
                                <a href="#newest<?= $sid; ?>" class="nav-link">Newest</a>
                                <a href="#highrating<?= $sid; ?>" class="nav-link">Highest Rating</a>
                                <a href="#lowrating<?= $sid; ?>" class="nav-link">Lowest Rating</a>
                            </div>

                            <?php foreach ([['all', $reviews_top], ['newest', $reviews_newest], ['highrating', $reviews_high], ['lowrating', $reviews_low]] as [$key, $list]): ?>
                                <div id="<?= $key . $sid; ?>" class="section-content <?= $key === 'all' ? 'active d-block' : 'd-none'; ?>">
                                    <?php if (empty($list)): ?>
                                        <p class="p-5 border rounded-2 bg-white text-center">No reviews yet.</p>
                                    <?php else: foreach ($list as $rev): ?>
                                        <?php $userHasHelped = isset($_SESSION['user']) ? $productObj->hasUserMarkedHelpful($rev['id'], $_SESSION['user']['id']) : false; ?>
                                        <div class="p-4 border rounded-2 bg-white mb-3">
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($rev['first_name']); ?></h6>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div data-coreui-size="sm" data-coreui-toggle="rating" data-coreui-read-only="true" data-coreui-value="<?= $rev['rating_value']; ?>"></div>
                                                <small class="text-muted"><?= htmlspecialchars($rev['created_at']); ?></small>
                                            </div>
                                            <?php if (!empty($rev['comment'])): ?><p class="my-3"><?= htmlspecialchars($rev['comment']); ?></p><?php endif; ?>
                                            <div class="my-3">
                                                <?php if ($isOwner && empty($rev['seller_response'])): ?>
                                                    <button type="button" class="reply-btn text-white border-0 rounded-1 py-1 px-3" style="background-color: #CD5C08" data-review-id="<?= $rev['id']; ?>">Reply</button>
                                                    <form method="POST" class="reply-form d-none mb-3" data-review-id="<?= $rev['id']; ?>">
                                                        <input type="hidden" name="reply_submit" value="1">
                                                        <input type="hidden" name="review_id" value="<?= $rev['id']; ?>">
                                                        <label class="fw-bold" for="seller_response_<?= $rev['id']; ?>">Your Reply:</label>
                                                        <textarea id="seller_response_<?= $rev['id']; ?>" name="seller_response" class="w-100 px-3 py-2 rounded-1 border mb-2" placeholder="Write your response here..." required></textarea>
                                                        <button type="submit" class="text-white border-0 rounded-1 py-1 px-3" style="background-color: #CD5C08">Reply</button>
                                                        <button type="button" class="cancel-reply-btn text-white border-0 rounded-1 py-1 px-3" style="background-color: gray">Cancel</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($rev['seller_response'])): ?><p class="p-3 rounded-2 mb-3" style="background-color:#f4f4f4">Stall Response: <?= htmlspecialchars($rev['seller_response']); ?></p><?php endif; ?>
                                            <div class="d-flex gap-3 align-items-center border rounded-2">
                                                <img src="<?= htmlspecialchars($product['image']); ?>" width="55" height="55" class="border rounded-start" alt="">
                                                <div>
                                                    <span><?= htmlspecialchars($product['name']); ?></span><br>
                                                    <?php if (!empty($rev['variations'])): ?><span class="small text-muted">Variation: <?= htmlspecialchars($rev['variations']); ?></span><?php endif; ?>
                                                </div>
                                            </div>
                                            <?php $iconClass = $userHasHelped ? 'fa-solid' : 'fa-regular'; ?>
                                            <div class="small text-end mt-2 helpful-toggle<?= $userHasHelped ? ' active' : ''; ?>" style="cursor:pointer;" data-review-id="<?= $rev['id']; ?>">
                                                <i class="<?= $iconClass ?> fa-thumbs-up"></i>
                                                <span>Helpful <span class="helpful-count"><?= (int)($rev['helpful_count'] ?? 0); ?></span></span>
                                            </div>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="modal fade" id="stallReviewsModal" tabindex="-1" aria-labelledby="stallReviewsTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content ratemodal">
                    <div class="modal-header">
                        <h5 class="modal-title">All Stall Reviews</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="background:#F4F4F4;">
                        <div class="d-flex justify-content-between mb-3 p-4 border rounded bg-white">
                            <div class="w-50">
                                <h1 class="m-0 fw-bold"><?= number_format($stallStats,1); ?></h1>
                                <div data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="<?= round($stallStats); ?>"></div>
                                <span>All ratings (<?= $stallCount; ?>)</span>
                            </div>
                            <div class="w-50">
                                <?php 
                                    $stallBreak = $productObj->getStallRatingBreakdown($stall_id);
                                    foreach (range(5,1) as $star):
                                        $count = $stallBreak[$star] ?? 0;
                                        $pct   = $stallCount > 0 ? ($count/$stallCount*100) : 0;
                                ?>
                                    <div class="d-flex align-items-center small gap-1">
                                        <span><?= $star; ?></span>
                                        <div data-coreui-item-count="1" data-coreui-size="sm" data-coreui-read-only="true" data-coreui-toggle="rating" data-coreui-value="1"></div>
                                        <div class="w3-light-grey w3-round-xlarge" style="width:100%;height:10px;">
                                            <?php if ($count > 0): ?><div class="w3-container w3-round-xlarge" style="width:<?= $pct; ?>%;height:10px;background:#CD5C08"></div><?php endif; ?>
                                        </div>
                                        <span><?= $count; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <!-- Tabs -->
                        <div class="nav-container d-flex gap-3 mb-2">
                            <a href="#stallAll" class="nav-link active">Top reviews</a>
                            <a href="#stallNewest" class="nav-link">Newest</a>
                            <a href="#stallHigh" class="nav-link">Highest Rating</a>
                            <a href="#stallLow" class="nav-link">Lowest Rating</a>
                        </div>

                        <?php
                            $reviewsTop    = $productObj->getStallReviews($stall_id, 'helpful');
                            $reviewsNewest = $productObj->getStallReviews($stall_id, 'newest');
                            $reviewsHigh   = $productObj->getStallReviews($stall_id, 'highest');
                            $reviewsLow    = $productObj->getStallReviews($stall_id, 'lowest');
                            $sections = [
                                ['stallAll', $reviewsTop],
                                ['stallNewest', $reviewsNewest],
                                ['stallHigh', $reviewsHigh],
                                ['stallLow', $reviewsLow]
                            ];
                        ?>
                        <?php foreach ($sections as list($key, $list)): ?>
                            <div id="<?= $key; ?>" class="section-content <?= $key==='stallAll'? 'active d-block' : 'd-none'; ?>">
                                <?php if (empty($list)): ?>
                                    <p class="p-5 border rounded-2 bg-white text-center">No reviews yet.</p>
                                <?php else: foreach ($list as $rev):
                                    $userHasHelped = isset($_SESSION['user'])
                                    && $productObj->hasUserMarkedHelpful($rev['id'], $_SESSION['user']['id']);
                                ?>
                                    <div class="p-4 border rounded-2 bg-white mb-3">
                                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($rev['first_name']); ?></h6>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div data-coreui-size="sm" data-coreui-toggle="rating" data-coreui-read-only="true" data-coreui-value="<?= $rev['rating_value']; ?>"></div>
                                            <small class="text-muted"><?= htmlspecialchars($rev['created_at']); ?></small>
                                        </div>
                                        <?php if (!empty($rev['comment'])): ?><p class="my-3"><?= htmlspecialchars($rev['comment']); ?></p><?php endif; ?>
                                        <div class="my-3">
                                            <?php if ($isOwner && empty($rev['seller_response'])): ?>
                                                <button type="button" class="reply-btn text-white border-0 rounded-1 py-1 px-3" style="background-color: #CD5C08" data-review-id="<?= $rev['id']; ?>">Reply</button>
                                                <form method="POST" class="reply-form d-none mb-3" data-review-id="<?= $rev['id']; ?>">
                                                    <input type="hidden" name="reply_submit" value="1">
                                                    <input type="hidden" name="review_id" value="<?= $rev['id']; ?>">
                                                    <label class="fw-bold" for="seller_response_<?= $rev['id']; ?>">Your Reply:</label>
                                                    <textarea id="seller_response_<?= $rev['id']; ?>" name="seller_response" class="w-100 px-3 py-2 rounded-1 border mb-2" placeholder="Write your response here..." required></textarea>
                                                    <button type="submit" class="text-white border-0 rounded-1 py-1 px-3" style="background-color: #CD5C08">Reply</button>
                                                    <button type="button" class="cancel-reply-btn text-white border-0 rounded-1 py-1 px-3" style="background-color: gray">Cancel</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($rev['seller_response'])): ?><p class="p-3 rounded-2 mb-3" style="background-color:#f4f4f4">Stall Response: <?= htmlspecialchars($rev['seller_response']); ?></p><?php endif; ?>
                                        <div class="d-flex gap-3 align-items-center border rounded-2">
                                            <img src="<?= htmlspecialchars($rev['product_image']); ?>" width="55" height="55" class="border rounded-start" alt="">
                                            <div>
                                                <span><?= htmlspecialchars($rev['product_name']); ?></span><br>
                                                <?php if (!empty($rev['variations'])): ?><span class="small text-muted">Variation: <?= htmlspecialchars($rev['variations']); ?></span><?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="small text-end mt-2 helpful-toggle<?= $userHasHelped ? ' active' : '';?>" data-review-id="<?= $rev['id'];?>" style="cursor:pointer;">
                                            <i class="<?= $userHasHelped ? 'fa-solid' : 'fa-regular'; ?> fa-thumbs-up"></i>
                                            <span>Helpful <span class="helpful-count"><?= $rev['helpful_count']; ?></span></span>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        <?php endforeach; ?>
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

        <script>
            document.querySelectorAll('.helpful-toggle').forEach(el => {
                el.addEventListener('click', async () => {
                    const reviewId = el.dataset.reviewId;
                    if (!reviewId) return;
                    try {
                        const resp = await fetch('toggle_helpful.php', {
                            method: 'POST',
                            headers: {'Content-Type':'application/x-www-form-urlencoded'},
                            body: new URLSearchParams({ review_id: reviewId })
                        });
                        const json = await resp.json();
                        if (!json.success) return Swal.fire({ icon: 'warning', title: 'Oops', text: json.message });
                        el.classList.toggle('active', json.toggledOn);
                        const icon = el.querySelector('i');
                        icon.classList.toggle('fa-regular', !json.toggledOn);
                        icon.classList.toggle('fa-solid', json.toggledOn);
                        el.querySelector('.helpful-count').innerText = json.count;
                    } catch (err) {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Could not update. Please try again.' });
                    }
                });
            });
        </script>
        <script>
            document.querySelectorAll('.reply-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.reviewId;
                    btn.classList.add('d-none');
                    document.querySelector(`.reply-form[data-review-id="${id}"]`).classList.remove('d-none');
                });
            });
            document.querySelectorAll('.cancel-reply-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('.reply-form');
                    const id   = form.dataset.reviewId;
                    form.classList.add('d-none');
                    document.querySelector(`.reply-btn[data-review-id="${id}"]`).classList.remove('d-none');
                });
            });
        </script>
        <script>
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('shown.bs.modal', () => {
                    const navLinks = modal.querySelectorAll('.nav-container .nav-link');
                    navLinks.forEach(n => n.classList.remove('active'));
                    const topNav = navLinks[0];
                    topNav.classList.add('active');
                    const panes = modal.querySelectorAll('.section-content');
                    panes.forEach(p => { p.classList.remove('active','d-block'); p.classList.add('d-none'); });
                    const targetId   = topNav.getAttribute('href');
                    const targetPane = modal.querySelector(targetId);
                    if (targetPane) { targetPane.classList.remove('d-none'); targetPane.classList.add('active','d-block'); }
                });
            });
        </script>
        <script>
            const stallModal = document.getElementById('stallReviewsModal');
            stallModal.addEventListener('shown.bs.modal', () => {
                const navLinks = stallModal.querySelectorAll('.nav-container .nav-link');
                navLinks.forEach(n => n.classList.remove('active'));
                navLinks[0].classList.add('active');
                const panes = stallModal.querySelectorAll('.section-content');
                panes.forEach(p => { p.classList.remove('active','d-block'); p.classList.add('d-none'); });
                const target = stallModal.querySelector('#stallAll');
                target.classList.remove('d-none'); target.classList.add('active','d-block');

                stallModal.querySelectorAll('.reply-btn').forEach(btn => {
                btn.onclick = () => {
                    const card = btn.closest('.p-4');               // the review “card”
                    const id   = btn.dataset.reviewId;
                    btn.classList.add('d-none');
                    card
                    .querySelector(`.reply-form[data-review-id="${id}"]`)
                    .classList.remove('d-none');
                };
                });

                // handle Cancel button
                stallModal.querySelectorAll('.cancel-reply-btn').forEach(btn => {
                btn.onclick = () => {
                    const form = btn.closest('.reply-form');
                    const id   = form.dataset.reviewId;
                    form.classList.add('d-none');
                    stallModal
                    .querySelector(`.reply-btn[data-review-id="${id}"]`)
                    .classList.remove('d-none');
                };
                });
            });
            stallModal.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    stallModal.querySelectorAll('.section-content').forEach(p => p.classList.add('d-none'));
                    stallModal.querySelector(targetId).classList.remove('d-none');
                    stallModal.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        </script>
        <script>
            document
                .querySelectorAll('.nav-container')
                .forEach(navContainer => {
                const links = navContainer.querySelectorAll('.nav-link');
                links.forEach(link => {
                    link.addEventListener('click', e => {
                    e.preventDefault();
                    links.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');

                    const modal = link.closest('.modal-content') || link.closest('.modal');
                    modal
                        .querySelectorAll('.section-content')
                        .forEach(p => {
                        p.classList.remove('active','d-block');
                        p.classList.add('d-none');
                        });

                    const target = modal.querySelector(link.getAttribute('href'));
                    if (target) {
                        target.classList.remove('d-none');
                        target.classList.add('active','d-block');
                    }
                    });
                });
            });
        </script>

    </div>
</div>

<?php
    include_once 'modals.php'; 
    include_once './footer.php'; 
?> 