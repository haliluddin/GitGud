<?php 
    include_once 'header.php'; 
    include_once 'links.php'; 
    include_once 'nav.php';
    include_once 'modals.php';
    require_once __DIR__ . '/classes/stall.class.php';

    $stallObj = new Stall();

    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $product = $stallObj->getProductById($product_id);
    $variations = $stallObj->getProductVariations($product['id']);

    $hasStock = false;
    $lowStock = false;
    $noStock = true; 

    if (!empty($variations)) {
        $allLowStock = true;
        foreach ($variations as $variation) {
            $options = $stallObj->getVariationOptions($variation['id']);
            foreach ($options as $option) {
                $optionStock = $stallObj->getStock($product['id'], $option['id']);
                if ($optionStock > 0) {
                    $hasStock = true;
                    $noStock = false;
                    if ($optionStock > 5) {
                        $allLowStock = false;
                    }
                } else {
                    $allLowStock = false;
                }
            }
        }
        $lowStock = $hasStock && $allLowStock;
    } else {
        if ($product['stock'] > 0) {
            $hasStock = true;
            $noStock = false;
            if ($product['stock'] <= 5) {
                $lowStock = true;
            }
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

    if ($currentStock == 0) {
        $status = "NO";
    } elseif ($currentStock <= 5) {
        $status = "LOW";
    } else {
        $status = "IN";
    }
?>
<style>
    main{
        padding: 20px 120px;
    }
</style>
<main>
    <div class="d-flex justify-content-end">
        <button class="addpro mb-3 prev" onclick="window.location.href='managemenu.php';">
            <i class="fa-solid fa-chevron-left me-2"></i> Previous
        </button>
    </div>
    <div class="d-flex justify-content-between productdet rounded-2 mb-3">
        <div class="d-flex gap-4 align-items-center proinf">
            <div class="position-relative">
                <img src="<?= htmlspecialchars($product['image']); ?>" alt="">
                <?php if ($noStock): ?>
                    <div class="prostockstat bg-danger">NO STOCK</div>
                <?php elseif ($lowStock): ?>
                    <div class="prostockstat bg-warning">LOW STOCK</div>
                <?php else: ?>
                    <div class="prostockstat bg-success">IN STOCK</div>
                <?php endif; ?>
            </div>
            <div>
                <div class="d-flex gap-3 m-0 small">
                    <span><?= htmlspecialchars($product['code']); ?></span>
                    <span>|</span>
                    <span><?= htmlspecialchars($product['category_name']); ?></span>
                </div>
                <h5 class="fw-bold my-2"><?= htmlspecialchars($product['name']); ?></h5>
                <span class="small"><?= htmlspecialchars($product['description']); ?></span>
                <div class="d-flex gap-5 align-items-center propl">
                    <span class="proprice">P<?= number_format($product['base_price'], 2); ?></span>
                    <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                </div>
            </div>
        </div>
        <div class="proaction d-flex gap-2 mt-3">
            <i class="fa-solid fa-pen-to-square" onclick="window.location.href='editproduct.php';"></i>
            <i class="fa-solid fa-trash" data-bs-toggle="modal" data-bs-target="#deleteproduct"></i>
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
                    <div class="d-flex justify-content-between py-2 border-bottom align-items-center">
                        <div class="d-flex gap-2 align-items-center">
                            <input type="radio" 
                                   name="variation_option" 
                                   value="<?= $option['id']; ?>" 
                                   <?= ($selected_option === intval($option['id']) || ($isFirst && $selected_option === null)) ? 'checked' : ''; ?>
                                   onchange="handleVariationChange(this.value)">
                            <img src="<?= htmlspecialchars($option['image']); ?>" alt="" width="45px" height="45px" class="rounded-2 border">
                            <span><?= htmlspecialchars($option['name']); ?></span>
                        </div>
                        <span class="ip">
                            <?= ($option['add_price'] > 0) ? '+ ₱' . number_format($option['add_price'], 2) : 
                                 (($option['subtract_price'] > 0) ? '- ₱' . number_format($option['subtract_price'], 2) : 'Free'); ?>
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
                if ($status == 'LOW'){
                    echo '<h1 class="text-warning">' . $status . '</h1>';
                } elseif ($status == 'NO'){
                    echo '<h1 class="text-danger">' . $status . '</h1>';
                } elseif ($status == 'IN'){
                    echo '<h1 class="text-success">' . $status . '</h1>';
                } else {
                    echo '<h1>' . $status . '</h1>';
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
                    <option value="">Select a reason</option>
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
                                <i class="fa-solid fa-pen-to-square edit-icon"></i>
                                <i class="fa-solid fa-trash" data-bs-toggle="modal" data-bs-target="#deletestock"></i>
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
            <form class="d-flex stockin mt-1 mb-3" method="post">
                <?php if (!empty($variations)): ?>
                    <input type="hidden" name="variation_option" value="<?= $selected_option; ?>">
                <?php endif; ?>
                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                <input type="number" name="stockout" id="stockout" placeholder="# of items">      
                <select name="stockoutreason" id="stockoutreason">
                    <option value="">Select a reason</option>
                    <option value="Spoilage">Spoilage</option>
                    <option value="Expired">Expired</option>
                    <option value="Inventory Adjustment">Inventory Adjustment</option>
                    <option value="Theft or Loss">Theft or Loss</option>
                    <option value="Others">Others</option>
                </select>
                <input type="submit" name="stockout_submit" value="Go">
            </form>
            <table>
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
                                <i class="fa-solid fa-pen-to-square edit-icon"></i>
                                <i class="fa-solid fa-trash" data-bs-toggle="modal" data-bs-target="#deletestock"></i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No Stock Out transactions for this option.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
    
    <script>
        function handleVariationChange(selectedOptionId) {
            window.location.href = "?id=<?= $product['id']; ?>&variation_option=" + selectedOptionId;
        }
        
        document.querySelectorAll('.edit-icon').forEach(icon => {
            icon.addEventListener('click', function () {
                const row = this.closest('tr');
                const items = row.querySelector('.items').textContent.trim();
                const reason = row.querySelector('.reason').textContent.trim();
                const isStockIn = row.closest('table').id === 'stockin-table';

                if (isStockIn) {
                    document.getElementById('stockin').value = items;
                    const reasonDropdown = document.getElementById('stockinreason');
                    for (let option of reasonDropdown.options) {
                        if (option.textContent === reason) {
                            option.selected = true;
                            break;
                        }
                    }
                } else {
                    document.getElementById('stockout').value = items;
                    const reasonDropdown = document.getElementById('stockoutreason');
                    for (let option of reasonDropdown.options) {
                        if (option.textContent === reason) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            });
        });
    </script>
    <br><br> <br><br>
</main>

<?php
    include_once './footer.php'; 
?>
