<?php    
include_once 'links.php'; 
include_once 'header.php';
require_once __DIR__ . '/classes/cart.class.php';
require_once __DIR__ . '/classes/paymongo.class.php';

$cartObj = new Cart();
$payMongo = new PayMongoHandler();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $payment_method = $_POST['payment_method'] ?? null; 
    $order_type     = $_POST['order_type'] ?? null;    
    
    $updatedCart = [];
    if (isset($_POST['cart_items']) && is_array($_POST['cart_items'])) {
         foreach ($_POST['cart_items'] as $item) {
              $stallName = $item['stall_name'];
              if (!isset($updatedCart[$stallName])) {
                  $updatedCart[$stallName] = [];
              }
              $item['quantity'] = intval($item['quantity']);
              $item['unit_price'] = floatval($item['unit_price']);
              if (isset($item['variation_option_ids']) && $item['variation_option_ids'] !== '') {
                  $item['variation_option_ids'] = explode(',', $item['variation_option_ids']);
              } else {
                  $item['variation_option_ids'] = [];
              }
              if (isset($item['variation_names']) && $item['variation_names'] !== '') {
                  $item['variation_names'] = explode(',', $item['variation_names']);
              } else {
                  $item['variation_names'] = [];
              }
              $updatedCart[$stallName][] = $item;
         }
    }
    
    $order_id = $cartObj->placeOrder($user_id, $payment_method, $order_type, $updatedCart);
    $cartObj->removeCartItems($user_id, $park_id, $updatedCart);
    
    $amountInCents = 30000; 
    
    if (strtolower($payment_method) === 'gcash') {
        $result = $payMongo->createPaymentLink(
            $amountInCents,
            'Parking Payment',
            ['order_id' => $order_id]
        );
    
        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error']]);
        } else {
            echo "<script>
                    // Open the checkout URL in a new tab
                    window.open('" . $result['checkout_url'] . "', '_blank');
                    // Show the 'ifcashless' modal when the user returns to cart.php
                    document.addEventListener('DOMContentLoaded', function() {
                        var cashlessModal = new bootstrap.Modal(document.getElementById('ifcashless'));
                        cashlessModal.show();
                    });
                  </script>";
        }
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('orderIdDisplay').innerText = '$order_id';
                    var cashModal = new bootstrap.Modal(document.getElementById('ifcash'));
                    cashModal.show();
                });
              </script>";
    }
}

$cartGrouped = $cartObj->getCartGroupedItems($user_id, $park_id);

$availableCart = [];
$disabledCart  = [];

foreach ($cartGrouped as $stallName => $items) {
    foreach ($items as $item) {
        if (intval($item['stock']) >= intval($item['quantity'])) {
            $availableCart[$stallName][] = $item;
        } else {
            $disabledCart[$stallName][] = $item;
        }
    }
}
?>
<style>
    .disabled-stall-group {
        background-color: #fafafa !important;
    }
    .disabled-plus {
      color: gray;
      pointer-events: none;
    }
    .disabled-stall-group .sold{
        color: white;
        background-color: gray;
        border-radius: 10px;
        font-size: 10px;
        padding: 2px 0;
        text-align: center;
        width: 85px;
    }
    .disabled-stall-group .hlq{
        pointer-events: none;
        color: #d3d3d3;
    }
</style>
<main style="padding: 20px 120px;">

    <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white mb-3 carttop">
        <h4 class="fw-bold mb-0">My Cart</h4>
        <?php if(!empty($availableCart) || !empty($disabledCart)): ?>
            <button data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete all items</button>
        <?php endif; ?>
    </div>
    
    <?php if(!empty($availableCart) || !empty($disabledCart)): ?>
        <?php if(!empty($availableCart)): ?>
        <form method="POST">
            <?php 
                $counter = 0;
                foreach ($availableCart as $stallName => $items): 
                    $stall_id = $items[0]['stall_id'] ?? 0;
                    $supportedMethods = $items[0]['supported_methods'] ?? 'cash,gcash'; 
            ?>
                <div class="border py-3 px-4 rounded-2 bg-white mb-3 stall-group" 
                    data-stall-id="<?= htmlspecialchars($stall_id) ?>" 
                    data-supported-methods="<?= htmlspecialchars($supportedMethods) ?>">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 stall-header">
                        <div class="d-flex gap-2 align-items-center">
                            <span class="fw-bold"><?= htmlspecialchars($stallName) ?></span> 
                            <button type="button" class="viewstall border bg-white small px-2" onclick="window.location.href='stall.php'">View Stall</button>
                        </div>
                        <span class="stall-error text-danger" style="font-size: 13px; display:none;">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            This stall does not offer <span class="error-method"></span> payment
                        </span>
                    </div>
                    <?php foreach ($items as $item): 
                        $totalPrice = $item['quantity'] * $item['unit_price'];
                        $variationsText = '';
                        if (!empty($item['variation_names'])) {
                            $variationsText = '<span class="small text-muted">Variation: ' . htmlspecialchars(implode(', ', $item['variation_names'])) . '</span><br>';
                        }
                    ?>
                    <div class="d-flex border-bottom py-2 cart-item" data-stock="<?= htmlspecialchars($item['stock']) ?>">
                        <div class="d-flex gap-3 align-items-center" style="width: 70%">
                            <img src="<?= htmlspecialchars($item['product_image']) ?>" width="80px" height="80px" class="border rounded-2">
                            <div>
                                <span class="fs-5"><?= htmlspecialchars($item['product_name']) ?></span><br>
                                <?= $variationsText ?>
                                <?php if ($item['request']): ?>
                                    <span class="small text-muted">"<?= htmlspecialchars($item['request']) ?>"</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between" style="width: 30%" data-unit-price="<?= $item['unit_price'] ?>">
                            <div class="d-flex align-items-center hlq">
                                <i class="fa-solid fa-minus" onclick="updateCartQuantity(this, -1)"></i>
                                <span class="ordquanum"><?= htmlspecialchars($item['quantity']) ?></span>
                                <i class="fa-solid fa-plus" onclick="updateCartQuantity(this, 1)"></i>
                            </div>
                            <div class="fw-bold fs-5">₱<?= number_format($totalPrice, 2) ?></div>
                            <div class="carttop">
                                <button type="button" class="carttop" onclick="deleteCartItem('<?= $item['product_id'] ?>', '<?= urlencode($item['request']) ?>')">Delete</button>
                            </div>
                        </div>
                        <!-- Hidden inputs for this cart item -->
                        <input type="hidden" name="cart_items[<?= $counter ?>][product_id]" value="<?= htmlspecialchars($item['product_id']) ?>">
                        <input type="hidden" name="cart_items[<?= $counter ?>][stall_id]" value="<?= htmlspecialchars($item['stall_id']) ?>">
                        <input type="hidden" name="cart_items[<?= $counter ?>][stall_name]" value="<?= htmlspecialchars($stallName) ?>">
                        <input type="hidden" name="cart_items[<?= $counter ?>][request]" value="<?= htmlspecialchars($item['request']) ?>">
                        <input type="hidden" name="cart_items[<?= $counter ?>][unit_price]" value="<?= htmlspecialchars($item['unit_price']) ?>">
                        <input type="hidden" class="hidden-quantity" name="cart_items[<?= $counter ?>][quantity]" value="<?= htmlspecialchars($item['quantity']) ?>">
                        <input type="hidden" name="cart_items[<?= $counter ?>][variation_names]" value="<?= !empty($item['variation_names']) ? htmlspecialchars(implode(',', $item['variation_names'])) : '' ?>">
                        <input type="hidden" name="cart_items[<?= $counter ?>][variation_option_ids]" value="<?= !empty($item['variation_option_ids']) ? htmlspecialchars(implode(',', $item['variation_option_ids'])) : '' ?>">
                        <?php $counter++; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <!-- Order details and submit button -->
            <div class="d-flex justify-content-between align-items-start border py-3 px-4 rounded-2 bg-white mb-3">
                <div style="width: 70%">
                    <div class="d-flex align-items-center mb-4">
                        <label class="form-label w-25 mb-0 fw-bold">Order Type</label>
                        <div class="cartot btn-group w-75" role="group">
                            <button type="button" class="btn-toggle active rounded" id="dineIn" onclick="setOrderType('Dine In')">Dine In</button>
                            <button type="button" class="btn-toggle rounded" id="takeOut" onclick="setOrderType('Take Out')">Take Out</button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <label class="form-label w-25 mb-0 fw-bold">Payment Method</label>
                        <select class="form-select w-75" id="paymentMethod" name="payment_method" onchange="validatePaymentMethods()" required>
                            <option value="" disabled selected>Select</option>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                        </select>
                    </div>
                    <input type="hidden" id="order_type" name="order_type" value="Dine In">
                    <div class="d-flex align-items-center">
                        <div class="w-25"></div>
                        <button type="submit" name="place_order" id="placeOrderButton" class="btn btn-primary rounded-5" style="width: 250px;">Place Order</button>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <p class="fw-bold fs-5 m-0">Total:</p>
                    <h2 class="fw-bold m-0" id="grandTotal" style="color: #CD5C08">₱0.00</h2>
                </div>
            </div>
        </form>
        <?php endif; ?>

        <?php if(!empty($disabledCart)): ?>
            <?php foreach ($disabledCart as $stallName => $items): ?>
                <div class="border py-3 px-4 rounded-2 bg-white mb-3 disabled-stall-group">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 stall-header">
                        <div class="d-flex gap-2 align-items-center">
                            <span class="fw-bold"><?= htmlspecialchars($stallName) ?></span> 
                            <button type="button" class="viewstall border bg-white small px-2" onclick="window.location.href='stall.php'">View Stall</button>
                        </div>
                    </div>
                    <?php foreach ($items as $item): 
                        $totalPrice = $item['quantity'] * $item['unit_price'];
                        $variationsText = '';
                        if (!empty($item['variation_names'])) {
                            $variationsText = '<span class="small text-muted">Variation: ' . htmlspecialchars(implode(', ', $item['variation_names'])) . '</span><br>';
                        }
                    ?>
                    <div class="d-flex border-bottom py-2 cart-item" data-stock="<?= htmlspecialchars($item['stock']) ?>">
                        <div class="d-flex gap-3 align-items-center" style="width: 70%">
                            <?php if (intval($item['stock']) > 0): ?>
                                <span class="sold"><?= intval($item['stock']) ?> ITEM<?= intval($item['stock']) > 1 ? 'S' : '' ?> LEFT</span>
                            <?php else: ?>
                                <span class="sold">SOLD OUT</span>
                            <?php endif; ?>
                            <img src="<?= htmlspecialchars($item['product_image']) ?>" width="80px" height="80px" class="border rounded-2">
                            <div>
                                <span class="fs-5"><?= htmlspecialchars($item['product_name']) ?></span><br>
                                <?= $variationsText ?>
                                <?php if ($item['request']): ?>
                                    <span class="small text-muted">"<?= htmlspecialchars($item['request']) ?>"</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between" style="width: 30%" data-unit-price="<?= $item['unit_price'] ?>">
                            <div class="d-flex align-items-center hlq">
                                <i class="fa-solid fa-minus"></i>
                                <span class="ordquanum"><?= htmlspecialchars($item['quantity']) ?></span>
                                <i class="fa-solid fa-plus"></i>
                            </div>
                            <div class="fw-bold fs-5">₱<?= number_format($totalPrice, 2) ?></div>
                            <div class="carttop">
                                <button type="button" class="carttop" onclick="deleteCartItem('<?= $item['product_id'] ?>', '<?= urlencode($item['request']) ?>')">Delete</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: ?>
        <div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
            No items found.
        </div>
    <?php endif; ?>
    <br><br><br><br><br>
</main>

<!-- Delete All Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete all items in your cart?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="deleteAllCartItems()">Delete All</button>
      </div>
    </div>
  </div>
</div>

<!-- Cash Payment Modal -->
<div class="modal fade" id="ifcash" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <i class="fa-regular fa-face-smile mb-3" style="color: #CD5C08; font-size: 80px"></i><br>
                    <span>Thank you for your order!</span>
                    <h5 class="fw-bold mt-2 mb-4">
                        Your Order ID is <span id="orderIdDisplay" style="color: #CD5C08;"></span>
                    </h5>
                    <p class="mb-3">Please proceed to each stall with this Order ID to complete your payment. Once payment is confirmed, your order will be in preparation queue.</p>
                    <span>For more details about your order, go to Purchase.</span>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='purchase.php';">Purchase</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

<!-- Cashless Payment Modal -->
<div class="modal fade" id="ifcashless" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center">
                    <i class="fa-regular fa-face-smile mb-3" style="color: #CD5C08; font-size: 80px"></i><br>
                    <span>Thank you for your order!</span>
                    <h5 class="fw-bold mt-2 mb-4">Your Order ID is <span style="color: #CD5C08;"><?= htmlspecialchars($order_id ?? '0000'); ?></span></h5>
                    <p class="mb-3">Your order at each stall is now in preparation queue. You will be notified when your items are ready for pickup.</p>
                    <span>For more details about your order, go to Purchase.</span>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='purchase.php';">Purchase</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

<script>
    function updateCartQuantity(button, change) {
        const quantitySpan = button.parentElement.querySelector('.ordquanum');
        let currentQuantity = parseInt(quantitySpan.innerText);
        const cartItemDiv = button.closest('.cart-item');
        const stock = parseInt(cartItemDiv.getAttribute('data-stock')) || 0;
        
        let newQuantity = currentQuantity + change;
        newQuantity = Math.max(1, newQuantity);
        if (newQuantity > stock) {
            newQuantity = stock;
        }
        quantitySpan.innerText = newQuantity;
        
        const hiddenInput = cartItemDiv.querySelector('.hidden-quantity');
        if (hiddenInput) {
            hiddenInput.value = newQuantity;
        }
        
        const parentContainer = button.closest('[data-unit-price]');
        const unitPrice = parseFloat(parentContainer.getAttribute('data-unit-price'));
        const totalDiv = parentContainer.querySelector('.fw-bold.fs-5');
        const newTotal = unitPrice * newQuantity;
        totalDiv.innerText = '₱' + newTotal.toFixed(2);
        
        updateGrandTotal();
        
        const plusBtn = parentContainer.querySelector('.fa-plus');
        if (newQuantity >= stock) {
            plusBtn.classList.add('disabled-plus');
        } else {
            plusBtn.classList.remove('disabled-plus');
        }
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.stall-group .cart-item').forEach(cartItem => {
            const priceContainer = cartItem.querySelector('[data-unit-price]');
            if (priceContainer) {
                const unitPrice = parseFloat(priceContainer.getAttribute('data-unit-price'));
                const quantity = parseInt(cartItem.querySelector('.ordquanum').innerText);
                grandTotal += unitPrice * quantity;
            }
        });
        document.getElementById('grandTotal').innerText = '₱' + grandTotal.toFixed(2);
    }

    function deleteCartItem(productId, request) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_cart_item.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function(){
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var cartItem = document.querySelector('.cart-item [onclick*="deleteCartItem"][onclick*="\'' + productId + '\'"]');
                    if (cartItem) {
                        var cartItemDiv = cartItem.closest('.cart-item');
                        if (cartItemDiv) {
                            cartItemDiv.remove();
                        }
                    }
                    updateGrandTotal();
                } else {
                    alert('Failed to delete item.');
                }
            }
        };
        xhr.send("user_id=<?= $user_id; ?>&park_id=<?= $park_id; ?>&product_id=" + productId + "&request=" + encodeURIComponent(request));
    }

    function deleteAllCartItems() {
        var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
        confirmModal.hide();

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_all_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function(){
            if (xhr.readyState === 4 && xhr.status === 200) {
                window.location.reload();
            }
        };
        xhr.send("user_id=<?= $user_id; ?>&park_id=<?= $park_id; ?>");
    }

    function validatePaymentMethods() {
        const selectedMethod = document.getElementById('paymentMethod').value; // "Cash" or "GCash"
        document.querySelectorAll('.stall-group').forEach(stall => {
            const supportedMethods = stall.getAttribute('data-supported-methods')
                .split(',')
                .map(m => m.trim().toLowerCase());
            const errorSpan = stall.querySelector('.stall-error');
            if (!supportedMethods.includes(selectedMethod.toLowerCase())) {
                errorSpan.style.display = 'inline';
                errorSpan.querySelector('.error-method').innerText = selectedMethod;
            } else {
                errorSpan.style.display = 'none';
            }
        });
        updateGrandTotal();
    }

    function setOrderType(type) {
        document.getElementById('order_type').value = type;
        document.querySelectorAll('.btn-toggle').forEach(btn => btn.classList.remove('active'));
        if (type === 'Dine In') {
            document.getElementById('dineIn').classList.add('active');
        } else {
            document.getElementById('takeOut').classList.add('active');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateGrandTotal();
        document.querySelectorAll('.stall-group .cart-item').forEach(cartItem => {
            const quantity = parseInt(cartItem.querySelector('.ordquanum').innerText);
            const stock = parseInt(cartItem.getAttribute('data-stock')) || 0;
            const plusBtn = cartItem.querySelector('.fa-plus');
            if (quantity >= stock) {
                plusBtn.classList.add('disabled-plus');
            }
        });
    });
</script>

<?php 
include_once 'footer.php'; 
?> 
