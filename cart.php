<?php  
include_once 'links.php'; 
include_once 'header.php';
require_once __DIR__ . '/classes/cart.class.php';

$cartObj = new Cart();
if (isset($user_id) && isset($park_id))
    $cartGrouped = $cartObj->getCartGroupedItems($user_id, $park_id);

if (isset($cartGrouped) && !empty($cartGrouped))
    $cartExists = true;
else
    $cartExists = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $payment_method = $_POST['payment_method'] ?? null; 
    $order_type     = $_POST['order_type'] ?? null;     

    $order_id = $cartObj->placeOrder($user_id, $payment_method, $order_type, $cartGrouped);

    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('orderIdDisplay').innerText = '$order_id';
                var cashModal = new bootstrap.Modal(document.getElementById('ifcash'));
                cashModal.show();
            });
          </script>";
}
?>

<main style="padding: 20px 120px;">

    <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white mb-3 carttop">
        <h4 class="fw-bold mb-0">My Cart</h4>
        <button data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" <?php echo $cartExists ? '' : 'disabled'; ?>>Delete all items</button>
    </div>

    
        <?php 
        if (isset($cartGrouped) && !empty($cartGrouped))
            foreach ($cartGrouped as $stallName => $items): 
                $stall_id = $items[0]['stall_id'] ?? 0;
                $supportedMethods = $items[0]['supported_methods'] ?? 'cash,gcash'; 
        ?>
        <div class="border py-3 px-4 rounded-2 bg-white mb-3 stall-group" 
             data-stall-id="<?= htmlspecialchars($stall_id) ?>" 
             data-supported-methods="<?= htmlspecialchars($supportedMethods) ?>">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 stall-header">
                <div class="d-flex gap-2 align-items-center">
                    <span class="fw-bold"><?= htmlspecialchars($stallName) ?></span> 
                    <button class="viewstall border bg-white small px-2" onclick="window.location.href='stall.php'">View Stall</button>
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
            <div class="d-flex border-bottom py-2 cart-item">
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
                        <i class="fa-solid fa-minus" onclick="updateCartQuantity(this, -1, '<?= $item['product_id'] ?>', '<?= urlencode($item['request']) ?>')"></i>
                        <span class="ordquanum"><?= htmlspecialchars($item['quantity']) ?></span>
                        <i class="fa-solid fa-plus" onclick="updateCartQuantity(this, 1, '<?= $item['product_id'] ?>', '<?= urlencode($item['request']) ?>')"></i>
                    </div>
                    <div class="fw-bold fs-5">₱<?= number_format($totalPrice, 2) ?></div>
                    <div class="carttop">
                        <button class="carttop" onclick="deleteCartItem('<?= $item['product_id'] ?>', '<?= urlencode($item['request']) ?>')">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <form method="POST">
        <div class="d-flex justify-content-between align-items-start border py-3 px-4 rounded-2 bg-white">
            <div style="width: 70%">
                <div class="d-flex align-items-center mb-4">
                    <label class="form-label w-25 mb-0 fw-bold">Order Type</label>
                    <div class="cartot btn-group w-75" role="group">
                        <button type="button" class="btn-toggle active rounded" id="dineIn" onclick="document.getElementById('order_type').value='Dine In'">Dine In</button>
                        <button type="button" class="btn-toggle rounded" id="takeOut" onclick="document.getElementById('order_type').value='Take Out'">Take Out</button>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-4">
                    <label class="form-label w-25 mb-0 fw-bold">Payment Method</label>
                    <select class="form-select w-75" id="paymentMethod" name="payment_method" onchange="validatePaymentMethods()" required <?php echo $cartExists ? '' : 'disabled'; ?>>
                        <option value="" disabled selected>Select</option>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                    </select>
                </div>
                <input type="hidden" id="order_type" name="order_type" value="Dine In">
                <div class="d-flex align-items-center">
                    <div class="w-25"></div>
                    <button type="submit" name="place_order" id="placeOrderButton" class="btn <?php echo $cartExists ? 'btn-primary' : 'btn-secondary'; ?> rounded-5" style="width: 250px;" <?php echo $cartExists ? '' : 'disabled'; ?>>Place Order</button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-4">
                <p class="fw-bold fs-5 m-0">Total:</p>
                <h2 class="fw-bold m-0" id="grandTotal" style="color: #CD5C08">₱0.00</h2>
            </div>
        </div>
        <br><br><br><br><br><br>
    </form>
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
<script>
    function updateCartQuantity(button, change, productId, request) {
        const quantitySpan = button.parentElement.querySelector('.ordquanum');
        let quantity = parseInt(quantitySpan.innerText);
        quantity = Math.max(1, quantity + change);
        quantitySpan.innerText = quantity;

        const parentContainer = button.closest('[data-unit-price]');
        const unitPrice = parseFloat(parentContainer.getAttribute('data-unit-price'));
        const totalDiv = parentContainer.querySelector('.fw-bold.fs-5');
        const newTotal = unitPrice * quantity;
        totalDiv.innerText = '₱' + newTotal.toFixed(2);

        updateGrandTotal();
    }

    function deleteCartItem(productId, request) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_cart_item.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4){
                if(xhr.status === 200){
                    var cartItem = document.querySelector(`.cart-item [onclick*="deleteCartItem"][onclick*="'${productId}'"]`);
                    if(cartItem) {
                        var cartItemDiv = cartItem.closest('.cart-item');
                        if(cartItemDiv) {
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
            if(xhr.readyState === 4 && xhr.status === 200){
                window.location.reload();
            }
        };
        xhr.send("user_id=<?= $user_id; ?>&park_id=<?= $park_id; ?>");
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.cart-item').forEach(cartItem => {
            const priceContainer = cartItem.querySelector('[data-unit-price]');
            const unitPrice = parseFloat(priceContainer.getAttribute('data-unit-price'));
            const quantity = parseInt(cartItem.querySelector('.ordquanum').innerText);
            grandTotal += unitPrice * quantity;
        });
        document.getElementById('grandTotal').innerText = '₱' + grandTotal.toFixed(2);
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

    document.querySelectorAll('.btn-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.btn-toggle').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    document.addEventListener('DOMContentLoaded', updateGrandTotal);

</script>

<?php 
include_once 'footer.php'; 
?>
