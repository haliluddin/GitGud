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
    $stall_id        = decrypt(urldecode($_GET['id']));
    $stall           = $parkObj->getStall($stall_id);
    $products        = $stallObj->getProducts($stall_id);
    $categories      = $productObj->getCategories($stall_id);
    $totalProducts   = $stallObj->getTotalProducts($stall_id);
    $likeCount       = $productObj->getStallLikes($stall_id);
    $likedByUser     = isset($user_id) ? $productObj->isStallLiked($stall_id, $user_id) : false;
    $popularProducts = $productObj->getPopularProducts($stall_id);
    $promoProducts   = $productObj->getPromoProducts($stall_id);
    $newProducts     = $productObj->getNewProducts($stall_id);
    $popularProdIds  = array_column($popularProducts, 'id');
    $promoProdIds    = array_column($promoProducts, 'id');
    $newProdIds      = array_column($newProducts, 'id');
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
            $daysArray              = array_map('trim', explode(',', $days));
            if (in_array($currentDay, $daysArray)) {
                list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
                $openTime24                 = date('H:i', strtotime($openTime));
                $closeTime24                = date('H:i', strtotime($closeTime));
                if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                    $isOpen = true;
                    break;
                }
            }
        }
    }
}

$park              = $parkObj->getPark($stall['park_id']);
$parkOperatingHours = [];
if (!empty($park['operating_hours'])) {
    $parkOperatingHours = explode('; ', $park['operating_hours']);
}

$parkIsOpen = false;
foreach ($parkOperatingHours as $hours) {
    if (strpos($hours, '<br>') !== false) {
        list($days, $timeRange) = explode('<br>', $hours);
        $daysArray              = array_map('trim', explode(',', $days));
        if (in_array($currentDay, $daysArray)) {
            list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
            $openTime24                 = date('H:i', strtotime($openTime));
            $closeTime24                = date('H:i', strtotime($closeTime));
            if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                $parkIsOpen = true;
                break;
            }
        }
    }
}

$isOwner = $stallObj->isOwner($stall_id, $user_id);

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

<style>
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
    <div class="disabled" <?php if (isset($stall['status']) && $stall['status'] === 'Unavailable') { echo 'style="pointer-events: none; opacity: 0.5;"'; } ?>>
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
            </section>
        <?php endforeach; ?>
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
    <br><br><br>
</div>

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


<?php
include_once 'modals.php';
include_once './footer.php';
?>