<?php 
    include_once 'header.php'; 
    include_once 'links.php'; 
    require_once __DIR__ . '/classes/stall.class.php';
    require_once __DIR__ . '/classes/product.class.php';
    require_once __DIR__ . '/classes/encdec.class.php';

    $stallObj = new Stall();
    $productObj = new Product();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
        $prod_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $result = $productObj->deleteProduct($prod_id);
        if ($result) {
            header("Location: managemenu.php");
            exit;
        } else {
            $error = "Failed to delete the product.";
        }
    }

    // Handle delete inventory record
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_inventory'])) {
        $inventory_id = isset($_POST['inventory_id']) ? intval($_POST['inventory_id']) : 0;
        $prod_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $variation_option_id = isset($_POST['variation_option']) && $_POST['variation_option'] ? intval($_POST['variation_option']) : null;
        
        $result = $stallObj->deleteInventory($inventory_id, $prod_id, $variation_option_id);
        if ($result) {
            $success = "Inventory record deleted successfully.";
            // Redirect back to the same page to refresh the data
            if ($variation_option_id) {
                echo '<script>window.location.href = "stocks.php?id=' . urlencode(encrypt($prod_id)) . '&variation_option=' . $variation_option_id . '";</script>';
            } else {
                echo '<script>window.location.href = "stocks.php?id=' . urlencode(encrypt($prod_id)) . '";</script>';
            }
            exit;
        } else {
            $error = "Failed to delete the inventory record.";
        }
    }

    // Handle update inventory record
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_inventory'])) {
        $inventory_id = isset($_POST['inventory_id']) ? intval($_POST['inventory_id']) : 0;
        $prod_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
        $type = isset($_POST['inventory_type']) ? trim($_POST['inventory_type']) : '';
        $variation_option_id = isset($_POST['variation_option']) && $_POST['variation_option'] ? intval($_POST['variation_option']) : null;
        
        if ($quantity <= 0) {
            $error = "Please enter a valid quantity.";
        } else {
            $result = $stallObj->updateInventory($inventory_id, $prod_id, $variation_option_id, $type, $quantity, $reason);
            if ($result) {
                $success = "Inventory record updated successfully.";
                // Redirect back to the same page to refresh the data
                if ($variation_option_id) {
                    echo '<script>window.location.href = "stocks.php?id=' . urlencode(encrypt($prod_id)) . '&variation_option=' . $variation_option_id . '";</script>';
                } else {
                    echo '<script>window.location.href = "stocks.php?id=' . urlencode(encrypt($prod_id)) . '";</script>';
                }
                exit;
            } else {
                $error = "Failed to update the inventory record.";
            }
        }
    }

    $product_id = isset($_GET['id']) ? intval(urldecode(decrypt($_GET['id']))) : 0;

    if ($product_id == 0) {
        // Redirect to managemenu.php using javascript
        echo '<script>window.location.href = "managemenu.php";</script>';
        exit;
    }

    $product = $stallObj->getProductById($product_id);
    $variations = $stallObj->getProductVariations($product['id']);

    if (!empty($variations)) {
        $overallStatus = "IN";
        foreach ($variations as $variation) {
            $options = $stallObj->getVariationOptions($variation['id']);
            $allZero = true;
            $allLow = true;
            foreach ($options as $option) {
                $qty = $stallObj->getStock($product['id'], $option['id']);
                if ($qty > 0) {
                    $allZero = false;
                    if ($qty > 5) {
                        $allLow = false;
                    }
                } else {
                    $allLow = false;
                }
            }
            if ($allZero) {
                $overallStatus = "NO";
                break;
            } elseif ($allLow && $overallStatus !== "NO") {
                $overallStatus = "LOW";
            }
        }
    } else {
        if ($product['stock'] == 0) {
            $overallStatus = "NO";
        } elseif ($product['stock'] <= 5) {
            $overallStatus = "LOW";
        } else {
            $overallStatus = "IN";
        }
    }

    $selected_option = null;
    if (!empty($variations)) {
        if (isset($_GET['variation_option']) && !empty($_GET['variation_option'])) {
            $selected_option = intval($_GET['variation_option']);
        } else {
            $firstVariation = $variations[0];
            $options = $stallObj->getVariationOptions($firstVariation['id']);
            if (!empty($options)) {
                $selected_option = $options[0]['id'];
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['stockin_submit'])) {
            $type = 'Stock In';
            $quantity = isset($_POST['stockin']) ? intval($_POST['stockin']) : 0;
            $reason = isset($_POST['stockinreason']) ? trim($_POST['stockinreason']) : '';
        } elseif (isset($_POST['stockout_submit'])) {
            $type = 'Stock Out';
            $quantity = isset($_POST['stockout']) ? intval($_POST['stockout']) : 0;
            $reason = isset($_POST['stockoutreason']) ? trim($_POST['stockoutreason']) : '';
        }
        $prod_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
       
        if (!empty($variations)) {
            $variation_option_id = isset($_POST['variation_option']) ? intval($_POST['variation_option']) : null;
            if (!$variation_option_id) {
                $error = "Please select a variation option.";
            }
        } else {
            $variation_option_id = null;
        }
        
        if (isset($quantity) && $quantity > 0 && !isset($error)) {
            $result = $stallObj->addInventory($prod_id, $variation_option_id, $type, $quantity, $reason);
            if ($result) {
                $success = "$type record added successfully.";
                $selected_option = $variation_option_id;
            } else {
                $error = "Failed to add inventory record.";
            }
        } else {
            if (!isset($error)) {
                $error = "Please enter a valid quantity.";
            }
        }
    }

    if (!empty($variations)) {
        $stockInRecords = $stallObj->getInventory($product['id'], 'Stock In', $selected_option);
        $stockOutRecords = $stallObj->getInventory($product['id'], 'Stock Out', $selected_option);
    } else {
        $stockInRecords = $stallObj->getInventory($product['id'], 'Stock In');
        $stockOutRecords = $stallObj->getInventory($product['id'], 'Stock Out');
    }

    $variationFilter = (!empty($variations)) ? $selected_option : null;

    $sqlIn = "SELECT SUM(quantity) as total FROM inventory WHERE product_id = :product_id AND type = 'Stock In'";
    if ($variationFilter !== null) {
        $sqlIn .= " AND variation_option_id = :variation_option_id";
    }
    $stmtIn = $stallObj->getConnection()->prepare($sqlIn);
    $stmtIn->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    if ($variationFilter !== null) {
        $stmtIn->bindValue(':variation_option_id', $variationFilter, PDO::PARAM_INT);
    }
    $stmtIn->execute();
    $rowIn = $stmtIn->fetch(PDO::FETCH_ASSOC);
    $totalStockIn = $rowIn['total'] ? $rowIn['total'] : 0;

    $sqlOut = "SELECT SUM(quantity) as total FROM inventory WHERE product_id = :product_id AND type = 'Stock Out'";
    if ($variationFilter !== null) {
        $sqlOut .= " AND variation_option_id = :variation_option_id";
    }
    $stmtOut = $stallObj->getConnection()->prepare($sqlOut);
    $stmtOut->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    if ($variationFilter !== null) {
        $stmtOut->bindValue(':variation_option_id', $variationFilter, PDO::PARAM_INT);
    }
    $stmtOut->execute();
    $rowOut = $stmtOut->fetch(PDO::FETCH_ASSOC);
    $totalStockOut = $rowOut['total'] ? $rowOut['total'] : 0;

    $sqlStock = "SELECT quantity FROM stocks WHERE product_id = :product_id";
    if ($variationFilter !== null) {
        $sqlStock .= " AND variation_option_id = :variation_option_id";
    }
    $stmtStock = $stallObj->getConnection()->prepare($sqlStock);
    $stmtStock->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    if ($variationFilter !== null) {
        $stmtStock->bindValue(':variation_option_id', $variationFilter, PDO::PARAM_INT);
    }
    $stmtStock->execute();
    $rowStock = $stmtStock->fetch(PDO::FETCH_ASSOC);
    $currentStock = $rowStock ? $rowStock['quantity'] : 0;
    $stockValue = $currentStock * $product['base_price'];
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .nav-main{
        padding: 20px 120px;
    }
</style>
<main class="nav-main">
    <div class="d-flex justify-content-end">
        <button class="addpro mb-3 prev" onclick="window.location.href='managemenu.php';">
            <i class="fa-solid fa-chevron-left me-2"></i> Previous
        </button>
    </div>
    <div class="d-flex justify-content-between productdet rounded-2 mb-3">
        <div class="d-flex gap-4 align-items-center proinf">
            <div class="position-relative">
                <img src="<?= htmlspecialchars($product['image']); ?>" alt="">
                <?php if ($overallStatus === "NO"): ?>
                    <div class="prostockstat bg-danger">NO STOCK</div>
                <?php elseif ($overallStatus === "LOW"): ?>
                    <div class="prostockstat bg-warning">LOW STOCK</div>
                <?php else: ?>
                    <div class="prostockstat bg-success">IN STOCK</div>
                <?php endif; ?>
            </div>
            <div>
                <div class="d-flex gap-3 m-0 small">
                    <!-- <span><?= htmlspecialchars($product['code']); ?></span>
                    <span>|</span> -->
                    <span><?= htmlspecialchars($product['category_name']); ?></span>
                </div>
                <h5 class="fw-bold my-2"><?= htmlspecialchars($product['name']); ?></h5>
                <span class="small"><?= htmlspecialchars($product['description']); ?></span>
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
                        <div class="my-3 bp-tm">
                            <span class="proprice">₱<?= number_format($discountedPrice, 2); ?></span>
                            <span class="pricebefore small">₱<?= number_format($product['base_price'], 2); ?></span>
                        </div>
                <?php } else { ?>
                        <div class="my-3 bp-tm">
                            <span class="proprice">₱<?= number_format($product['base_price'], 2); ?></span>
                        </div>
                <?php } ?>
            </div>
        </div>
        
    </div>
    <?php if (!empty($variations)): ?> 
        <?php $isFirst = true; ?>
        <?php foreach ($variations as $variation): ?>
            <div class="p-3 rounded-2 border invvar mb-3 bg-white">
                <div class="mb-2">
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($variation['name']); ?></h5>
                    <span class="small text-muted">Customer is required to select one option</span>
                </div>
                <?php foreach ($stallObj->getVariationOptions($variation['id']) as $option): ?>
                    <div class="d-flex justify-content-between align-items-center variationitem mb-2">
                        <div class="form-check d-flex gap-2 align-items-center">
                            <input 
                                class="form-check-input" 
                                type="radio" 
                                name="variation_option" 
                                value="<?= $option['id']; ?>" 
                                <?= ($selected_option === intval($option['id']) || ($isFirst && $selected_option === null)) ? 'checked' : ''; ?>
                                onchange="handleVariationChange(this.value)">
                            <img src="<?= htmlspecialchars($option['image']); ?>" alt="<?= htmlspecialchars($option['name']); ?>" width="45px" height="45px" class="rounded-2">
                            <label class="form-check-label" for="variation<?= $option['id']; ?>">
                                <?= htmlspecialchars($option['name']); ?>
                            </label>
                        </div>
                        <span class="ip">
                            <?= ($option['add_price'] > 0) ? '+ ₱' . number_format($option['add_price'], 2) : (($option['subtract_price'] > 0) ? '- ₱' . number_format($option['subtract_price'], 2) : 'Free'); ?>
                        </span>
                        <span class="stock-status">
                            <?php 
                            $optionStock = $stallObj->getStock($product['id'], $option['id']);
                            if ($optionStock == 0) {
                                echo '<span class="text-danger">NO STOCK</span>';
                            } elseif ($optionStock <= 5) {
                                echo '<span class="text-warning">LOW STOCK</span>';
                            } else {
                                echo '<span class="text-success">IN STOCK</span>';
                            }
                            ?>
                        </span>
                    </div>
                    <?php $isFirst = false; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="d-flex justify-content-around stostatus bg-white rounded-2 border my-3">
        <div>
            <span><i class="fa-solid fa-arrow-right-to-bracket"></i> Total Stock In</span>
            <h1><?= $totalStockIn ?></h1>
        </div>
        <div>
            <span><i class="fa-solid fa-arrow-right-from-bracket"></i> Total Stock Out</span>
            <h1><?= $totalStockOut ?></h1>
        </div>
        <div>
            <span><i class="fa-solid fa-box"></i> Current Stock</span>
            <h1><?= $currentStock ?></h1>
        </div>
        <div>
            <span><i class="fa-solid fa-sack-dollar"></i> Stock Value</span>
            <h1>P<?= number_format($stockValue, 2) ?></h1>
        </div>
        <div>
            <span><i class="fa-solid fa-spinner"></i> Status</span>
            <?php 
                if ($overallStatus == 'LOW'){
                    echo '<h1 class="text-warning">' . $overallStatus . '</h1>';
                } elseif ($overallStatus == 'NO'){
                    echo '<h1 class="text-danger">' . $overallStatus . '</h1>';
                } elseif ($overallStatus == 'IN'){
                    echo '<h1 class="text-success">' . $overallStatus . '</h1>';
                } else {
                    echo '<h1>' . $overallStatus . '</h1>';
                }
            ?>
        </div>
    </div>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="stockaction d-flex bg-white border rounded-2 mb-5">
        <div class="flex-grow-1 stockleft">
            <span class="text-muted fw-bold">Stock In</span>
            <form class="d-flex stockin mt-1 mb-3" id="stockin-form" method="post">
                <?php if (!empty($variations)): ?>
                    <input type="hidden" name="variation_option" value="<?= $selected_option; ?>">
                <?php endif; ?>
                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                <input type="number" name="stockin" id="stockin" placeholder="# of items">      
                <select name="stockinreason" id="stockinreason">
                    <option value="" selected disabled>Select a reason</option>
                    <option value="Restock">Restock</option>
                    <option value="Inventory Adjustment">Inventory Adjustment</option>
                    <option value="Bulk Orders">Bulk Orders</option>
                    <option value="Others">Others</option>
                </select>
                <input type="submit" name="stockin_submit" value="Go">
            </form>
            <table id="stockin-table">
                <tr>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
                <?php if (!empty($stockInRecords)): ?>
                    <?php foreach ($stockInRecords as $record): ?>
                        <tr>
                            <td><?= date('m/d/Y H:i', strtotime($record['created_at'])); ?></td>
                            <td class="items"><?= htmlspecialchars($record['quantity']); ?></td>
                            <td class="reason"><?= htmlspecialchars($record['reason']); ?></td>
                            <td class="stoaction">
                                <i class="fa-solid fa-pen-to-square edit-icon" data-id="<?= $record['id']; ?>" data-quantity="<?= $record['quantity']; ?>" data-reason="<?= $record['reason']; ?>" data-type="Stock In"></i>
                                <i class="fa-solid fa-trash delete-icon" data-bs-toggle="modal" data-bs-target="#deletestock" data-id="<?= $record['id']; ?>" data-type="Stock In"></i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No Stock In transactions for this option.</td></tr>
                <?php endif; ?>
            </table>
        </div>
        <div class="flex-grow-1 stockright">
            <span class="text-muted fw-bold">Stock Out</span>
            <form class="d-flex stockin mt-1 mb-3" method="post" id="stockout-form">
                <?php if (!empty($variations)): ?>
                    <input type="hidden" name="variation_option" value="<?= $selected_option; ?>">
                <?php endif; ?>
                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                <input type="number" name="stockout" id="stockout" placeholder="# of items">      
                <select name="stockoutreason" id="stockoutreason">
                    <option value="" selected disabled>Select a reason</option>
                    <option value="Spoilage">Spoilage</option>
                    <option value="Expired">Expired</option>
                    <option value="Inventory Adjustment">Inventory Adjustment</option>
                    <option value="Theft or Loss">Theft or Loss</option>
                    <option value="Others">Others</option>
                </select>
                <input type="submit" name="stockout_submit" value="Go">
            </form>
            <table id="stockout-table">
                <tr>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
                <?php if (!empty($stockOutRecords)): ?>
                    <?php foreach ($stockOutRecords as $record): ?>
                        <tr>
                            <td><?= date('m/d/Y H:i', strtotime($record['created_at'])); ?></td>
                            <td class="items"><?= htmlspecialchars($record['quantity']); ?></td>
                            <td class="reason"><?= htmlspecialchars($record['reason']); ?></td>
                            <td class="stoaction">
                                <i class="fa-solid fa-pen-to-square edit-icon" data-id="<?= $record['id']; ?>" data-quantity="<?= $record['quantity']; ?>" data-reason="<?= $record['reason']; ?>" data-type="Stock Out"></i>
                                <i class="fa-solid fa-trash delete-icon" data-bs-toggle="modal" data-bs-target="#deletestock" data-id="<?= $record['id']; ?>" data-type="Stock Out"></i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No Stock Out transactions for this option.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
    <div class="modal fade" id="deleteproduct" tabindex="-1" aria-labelledby="deleteProductLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteProductForm" action="" method="post">
                    <div class="modal-body">
                        <div class="deletemodal text-center">
                            <div class="promptdelete">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <h5>Delete Product</h5>
                                <p>You are about to delete this product.<br>Are you sure?</p>
                            </div>
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <div class="mt-5 mb-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_product" class="btn btn-primary">Delete</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Stock Modal -->
    <div class="modal fade" id="deletestock" tabindex="-1" aria-labelledby="deleteStockLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteStockForm" action="" method="post">
                    <div class="modal-body">
                        <div class="deletemodal text-center">
                            <div class="promptdelete">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <h5>Delete Inventory Record</h5>
                                <p>Are you sure you want to delete this inventory record?</p>
                            </div>
                            <input type="hidden" name="inventory_id" id="delete_inventory_id" value="">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <?php if (!empty($variations)): ?>
                                <input type="hidden" name="variation_option" value="<?= $selected_option; ?>">
                            <?php endif; ?>
                            <div class="mt-5 mb-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_inventory" class="btn btn-primary">Delete</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Stock Modal -->
    <div class="modal fade" id="editstock" tabindex="-1" aria-labelledby="editStockLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editStockForm" action="" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStockLabel">Edit Inventory Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="inventory_id" id="edit_inventory_id" value="">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <input type="hidden" name="inventory_type" id="edit_inventory_type" value="">
                        <?php if (!empty($variations)): ?>
                            <input type="hidden" name="variation_option" value="<?= $selected_option; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="edit_quantity" name="quantity" required min="1">
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <select class="form-control" id="edit_reason" name="reason" required>
                                <option value="" disabled selected>Select a reason</option>
                                <option value="Restock">Restock</option>
                                <option value="Inventory Adjustment">Inventory Adjustment</option>
                                <option value="Bulk Orders">Bulk Orders</option>
                                <option value="Spoilage">Spoilage</option>
                                <option value="Expired">Expired</option>
                                <option value="Theft or Loss">Theft or Loss</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_inventory" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function handleVariationChange(selectedOptionId) {
            window.location.href = "?id=<?= urlencode(encrypt($product['id'])); ?>&variation_option=" + selectedOptionId;
        }
        
        // Edit stock record
        document.querySelectorAll('.edit-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const quantity = this.getAttribute('data-quantity');
                const reason = this.getAttribute('data-reason');
                const type = this.getAttribute('data-type');
                
                document.getElementById('edit_inventory_id').value = id;
                document.getElementById('edit_quantity').value = quantity;
                document.getElementById('edit_inventory_type').value = type;
                
                // Find and select the matching reason option
                const reasonSelect = document.getElementById('edit_reason');
                for (let i = 0; i < reasonSelect.options.length; i++) {
                    if (reasonSelect.options[i].value === reason) {
                        reasonSelect.selectedIndex = i;
                        break;
                    }
                }
                
                // Show the edit modal
                new bootstrap.Modal(document.getElementById('editstock')).show();
            });
        });
        
        // Delete stock record
        document.querySelectorAll('.delete-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('delete_inventory_id').value = id;
            });
        });
        
        document.getElementById('stockin-form').addEventListener('submit', function(e) {
            setTimeout(function(){ location.reload(); }, 500);
        });
        document.getElementById('stockout-form').addEventListener('submit', function(e) {
            setTimeout(function(){ location.reload(); }, 500);
        });
    </script>
    <br><br><br><br>
</main>
<?php
    include_once './footer.php'; 
?>
