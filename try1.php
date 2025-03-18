<?php if(!empty($disabledCart)): ?>
        <div class="border py-3 px-4 rounded-2 bg-light mb-3">
            <h4 class="text-muted">Unavailable Items</h4>
            <?php foreach ($disabledCart as $stallName => $items): 
                foreach ($items as $item):
                    $totalPrice = $item['quantity'] * $item['unit_price'];
                    $variationsText = '';
                    if (!empty($item['variation_names'])) {
                        $variationsText = '<span class="small text-muted">Variation: ' . htmlspecialchars(implode(', ', $item['variation_names'])) . '</span><br>';
                    }
            ?>
                <div class="d-flex border-bottom py-2 cart-item disabled-item" data-stock="<?= htmlspecialchars($item['stock']) ?>">
                    <div class="d-flex gap-3 align-items-center" style="width: 70%">
                        <img src="<?= htmlspecialchars($item['product_image']) ?>" width="80px" height="80px" class="border rounded-2">
                        <div>
                            <span class="fs-5"><?= htmlspecialchars($item['product_name']) ?></span><br>
                            <?= $variationsText ?>
                            <?php if ($item['request']): ?>
                                <span class="small text-muted">"<?= htmlspecialchars($item['request']) ?>"</span>
                            <?php endif; ?>
                            <br>
                            <span class="badge bg-danger">No Stock</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="width: 30%" data-unit-price="<?= $item['unit_price'] ?>">
                        <div class="d-flex align-items-center hlq">
                            <i class="fa-solid fa-minus" style="opacity: 0.5; pointer-events: none;"></i>
                            <span class="ordquanum"><?= htmlspecialchars($item['quantity']) ?></span>
                            <i class="fa-solid fa-plus" style="opacity: 0.5; pointer-events: none;"></i>
                        </div>
                        <div class="fw-bold fs-5">₱<?= number_format($totalPrice, 2) ?></div>
                        <div class="carttop">
                            <button class="carttop" disabled>Delete</button>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach;
            endforeach; ?>
        </div>
    <?php endif; ?>