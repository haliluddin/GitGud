<?php
    include_once 'header.php'; 
    include_once 'links.php'; 
    include_once 'nav.php';
    require_once __DIR__ . '/classes/stall.class.php';
    require_once __DIR__ . '/classes/product.class.php';
    require_once __DIR__ . '/classes/encdec.class.php';

    $stallObj   = new Stall();
    $productObj = new Product();
    
    if ($user['role'] === 'Admin' && isset($_GET['stall_id'])) {
        $stall_id = intval($_GET['stall_id']);
    } else {
        $stall_id = $stallObj->getStallId($_SESSION['user']['id']);
    }    

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['new_category'])) {
            $newCategory = trim($_POST['new_category']);
            $productObj->addCategory($stall_id, $newCategory);
        }
        if (isset($_POST['edit_category'])) {
            $categoryId = $_POST['category_id'];
            $newName    = trim($_POST['category_name']);
            $productObj->updateCategory($categoryId, $newName);
        }
        if (isset($_POST['delete_product'])) {
            $deleteProductId = $_POST['product_id'];
            $productObj->deleteProduct($deleteProductId);
        }
    }

    $products   = $stallObj->getProducts($stall_id);
    $categories = $productObj->getCategories($stall_id);

?>
<style>
    main{ 
        padding: 20px 120px;
    }
</style>

<main>
    <div class="d-flex justify-content-end mb-3">
        <div class="dropdown">
            <button class="addpro dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">+ Add New</button>
            <ul class="dropdown-menu dropdown-menu-end p-0">
                <li><a class="dropdown-item" href="addproduct.php"><i class="fa-solid fa-burger me-2"></i> Item</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addcategory"><i class="fa-solid fa-list me-2"></i> Category</a></li>
            </ul>
        </div>
    </div>
    <div class="accordion" id="categoryAccordion">
        <?php foreach ($categories as $index => $category): ?>
            <div class="accordion-item">
                <h2 class="accordion-header m-0">
                    <button class="accordion-button d-flex align-items-center gap-2 py-4" type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapse<?= $category['id']; ?>" 
                            aria-expanded="true" 
                            aria-controls="collapse<?= $category['id']; ?>">
                        <p class="m-0 fw-bold"><?= htmlspecialchars($category['name']); ?></p>
                        <span class="editcategory" data-bs-toggle="modal" data-bs-target="#editcategory" onclick="event.stopPropagation(); setEditCategory(<?= $category['id']; ?>, '<?= htmlspecialchars($category['name'], ENT_QUOTES); ?>')">Edit Category</span>  
                    </button>
                </h2>
                <div id="collapse<?= $category['id']; ?>" class="accordion-collapse collapse show">
                    <div class="accordion-body pt-0 pb-4">
                        <div class="inventory">
                            <?php 
                            $hasProducts = false;
                            foreach ($products as $product):
                                if ($product['category_id'] == $category['id']):
                                    $hasProducts = true;

                                    $hasStock = false;
                                    $lowStock = false;
                                    $noStock  = true; 

                                    $variations = $stallObj->getProductVariations($product['id']);

                                    if (!empty($variations)) {
                                        $overallStockStatus = 'IN STOCK'; 
                                        foreach ($variations as $variation) {
                                            $options = $stallObj->getVariationOptions($variation['id']);
                                            
                                            $allZero = true;
                                            $allLow  = true;
                                            foreach ($options as $option) {
                                                $optionStock = $stallObj->getStock($product['id'], $option['id']);
                                                if ($optionStock > 0) {
                                                    $allZero = false;
                                                }
                                                if ($optionStock > 5) {
                                                    $allLow = false;
                                                }
                                            }
                                            if ($allZero) {
                                                $overallStockStatus = 'NO STOCK';
                                                break;
                                            }
                                            if ($allLow && $overallStockStatus !== 'NO STOCK') {
                                                $overallStockStatus = 'LOW STOCK';
                                            }
                                        }
                                        if ($overallStockStatus === 'NO STOCK') {
                                            $noStock = true;
                                            $hasStock = false;
                                            $lowStock = false;
                                        } elseif ($overallStockStatus === 'LOW STOCK') {
                                            $lowStock = true;
                                            $hasStock = true;
                                            $noStock  = false;
                                        } else {
                                            $hasStock = true;
                                            $noStock  = false;
                                        }
                                    } else {
                                        if ($product['stock'] > 0) {
                                            $hasStock = true;
                                            $noStock = false;
                                            if ($product['stock'] <= 5) {
                                                $lowStock = true;
                                            }
                                        }
                                    }
                                    ?>

                                    <div class="d-flex justify-content-between productdet rounded-2">
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
                                                        <div class="my-3">
                                                            <span class="proprice">₱<?= number_format($discountedPrice, 2); ?></span>
                                                            <span class="pricebefore small">₱<?= number_format($product['base_price'], 2); ?></span>
                                                        </div>
                                                <?php } else { ?>
                                                        <div class="my-3">
                                                            <span class="proprice">₱<?= number_format($product['base_price'], 2); ?></span>
                                                        </div>
                                                <?php } ?>

                                                <?php if (!empty($variations)): ?>
                                                    <button class="moreinfo" data-bs-toggle="modal" data-bs-target="#moreinfoproduct<?= $product['id']; ?>">
                                                        <i class="fa-solid fa-circle-info"></i> More info
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="proaction d-flex gap-2 mt-3">
                                            <i class="fa-solid fa-box" onclick="window.location.href='stocks.php?id=<?= urlencode(encrypt($product['id'])) ?>';"></i>
                                            <i class="fa-solid fa-pen-to-square" onclick="window.location.href='editproduct.php?id=<?= $product['id']; ?>';"></i>
                                            <i class="fa-solid fa-trash" data-bs-toggle="modal" data-bs-target="#deleteproduct" data-product-id="<?= $product['id']; ?>"></i>
                                        </div>
                                    </div>

                                    <?php if (!empty($variations)): ?>
                                    <div class="modal fade" id="moreinfoproduct<?= $product['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content h-75 overflow-auto">
                                                <div class="modal-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h4 class="fw-bold m-0"><?= htmlspecialchars($product['name']); ?></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <?php foreach ($variations as $variation): ?>
                                                        <div class="p-3 rounded-2 border invvar mb-3">
                                                            <div class="mb-2">
                                                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($variation['name']); ?></h5>
                                                                <span class="small text-muted">Customer is required to select one option</span>
                                                            </div>
                                                            <?php foreach ($stallObj->getVariationOptions($variation['id']) as $option): ?>
                                                                <div class="d-flex justify-content-between py-2 border-bottom align-items-center">
                                                                    <div class="d-flex gap-2 align-items-center">
                                                                        <img src="<?= htmlspecialchars($option['image']); ?>" alt="" width="45px" height="45px" class="rounded-2 border">
                                                                        <span><?= htmlspecialchars($option['name']); ?></span>
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
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                            <?php endif; endforeach; ?>

                            <?php if (!$hasProducts): ?>
                                <p>No products found for this category.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <br><br><br><br><br><br>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addcategory" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="text-center">
                            <h4 class="fw-bold mb-4">Add Category</h4>
                            <div class="form-floating m-0">
                                <input type="text" name="new_category" class="form-control" placeholder="Category" id="new_category">
                                <label for="new_category">Category</label>
                            </div>
                            <div class="mt-5 mb-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editcategory" tabindex="-1" aria-labelledby="editCategoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="text-center">
                            <h4 class="fw-bold mb-4">Edit Category</h4>
                            <input type="hidden" name="category_id" id="editCategoryId" value="">
                            <div class="form-floating m-0">
                                <input type="text" name="category_name" class="form-control" id="editCategoryName" placeholder="Category">
                                <label for="editCategoryName">Category</label>
                            </div>
                            <div class="mt-5 mb-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="edit_category" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteproduct" tabindex="-1" aria-labelledby="deleteProductLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="text-center">
                            <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-exclamation"></i> Delete Product</h4>
                            <p>You are about to delete this product.<br>Are you sure?</p>
                            <input type="hidden" name="product_id" id="deleteProductId" value="">
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
</main>

<script>
    function setEditCategory(categoryId, categoryName) {
        document.getElementById('editCategoryId').value = categoryId;
        document.getElementById('editCategoryName').value = categoryName;
    }

    var deleteProductModal = document.getElementById('deleteproduct');
    deleteProductModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; 
        var productId = button.getAttribute('data-product-id');
        document.getElementById('deleteProductId').value = productId;
    });
</script>

<?php
    include_once './footer.php'; 
?>
