<?php
ob_start();
include_once 'links.php';
include_once 'header.php';
require_once __DIR__ . '/classes/product.class.php';
require_once __DIR__ . '/classes/stall.class.php';
require_once __DIR__ . '/classes/encdec.class.php';

$productObj = new Product();
$stallObj   = new Stall();

$product_id = isset($_GET['id']) ? intval(urldecode(decrypt($_GET['id']))) : 0;
if ($product_id <= 0) {
    header("Location: managemenu.php");
    exit;
}

$productDetails = $productObj->getProduct($product_id);
if (!$productDetails) {
    echo "Product not found.";
    exit;
}

$stall_id = $productDetails['stall_id']; 
$selectCategories = $productObj->getCategories($stall_id);
$variationsData = $productObj->getProductVariations($product_id); 

$productName   = $productDetails['name'];
$productCode   = isset($productDetails['product_code']) ? $productDetails['product_code'] : '';
$category      = $productDetails['category_id'];
$description   = $productDetails['description'];
$basePrice     = $productDetails['base_price'];
$discount      = isset($productDetails['discount']) ? $productDetails['discount'] : 0;
$startDate     = isset($productDetails['discount_start']) ? $productDetails['discount_start'] : '';
$endDate       = isset($productDetails['discount_end']) ? $productDetails['discount_end'] : '';
$imagePath     = $productDetails['image'];
$initialStock  = isset($productDetails['initial_stock']) ? $productDetails['initial_stock'] : 0;

$imagePathErr      = $productNameErr = $categoryErr = $descriptionErr = "";
$basePriceErr      = $startDateErr = $endDateErr = $discountErr = $initialStockErr = "";
$variationStockErr = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_category'])) {
        $newCategory = trim($_POST['new_category']);
        if (!empty($newCategory)) {
            $productObj->addCategory($stall_id, $newCategory);
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . urlencode(encrypt($product_id)));
        exit;
    } else {
        $productName   = clean_input($_POST['productname']);
        $category      = isset($_POST['category']) ? clean_input($_POST['category']) : '';
        $description   = clean_input($_POST['description']);
        $basePrice     = clean_input($_POST['sellingPrice']);
        $discount      = clean_input($_POST['discount'] ?? 0);
        $startDate     = !empty($_POST['startDate']) ? clean_input($_POST['startDate']) : NULL;
        $endDate       = !empty($_POST['endDate']) ? clean_input($_POST['endDate']) : NULL;
        $initialStock  = clean_input($_POST['initialStock'] ?? '');
        
        if (empty($productName)) {
            $productNameErr = 'Product name is required.';
        }
        if (empty($category)) {
            $categoryErr = 'Category is required.';
        }
        if (empty($description)) {
            $descriptionErr = 'Description is required.';
        }
        if (empty($basePrice) || !is_numeric($basePrice) || $basePrice <= 0) {
            $basePriceErr = 'Selling price must be a positive number.';
        }
        if (!empty($discount) && (!is_numeric($discount) || $discount < 0)) {
            $discountErr = 'Discount must be a non-negative number.';
        }
        $hasVariations = false;
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'variation_name_') !== false) {
                $hasVariations = true;
                break;
            }
        }
        if (!$hasVariations) {
            if (empty($initialStock)) {
                $initialStockErr = 'Initial stock is required.';
            } elseif (!is_numeric($initialStock) || $initialStock < 0) {
                $initialStockErr = 'Initial stock must be a non-negative number.';
            }
        }
        
        if (!empty($_FILES["productimage"]["name"])) {
            $targetDir = "uploads/";
            $imageFileType = strtolower(pathinfo($_FILES["productimage"]["name"], PATHINFO_EXTENSION));
            $imageSize = $_FILES["productimage"]["size"];
            if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                $imagePathErr = "Only JPG, JPEG, and PNG formats are allowed.";
            } elseif ($imageSize > 500000) {
                $imagePathErr = "Image size must be less than 500KB.";
            } else {
                $imagePath = $targetDir . basename($_FILES["productimage"]["name"]);
                move_uploaded_file($_FILES["productimage"]["tmp_name"], $imagePath);
            }
        } else if (!empty($_POST['tempImagePath'])) {
            $imagePath = $_POST['tempImagePath'];
        }
        
        $updatedVariations = null;
        if ($hasVariations) {
            $updatedVariations = [];
            foreach ($_POST as $key => $value) {
                if (preg_match('/^variation_name_(\d+)$/', $key, $matches)) {
                    $varIndex = $matches[1];
                    $updatedVariations[$varIndex]['title'] = $_POST["variation_title_{$varIndex}"] ?? "Variation {$varIndex}";
                    $updatedVariations[$varIndex]['rows'] = [];
                    if (isset($_POST["variation_name_{$varIndex}"])) {
                        foreach ($_POST["variation_name_{$varIndex}"] as $i => $optionName) {
                            $updatedVariations[$varIndex]['rows'][] = [
                                'optionName'    => $optionName,
                                'initialStock'  => $_POST["variation_initial_stock_{$varIndex}"][$i] ?? '',
                                'addPrice'      => $_POST["variation_additional_price_{$varIndex}"][$i] ?? '',
                                'subtractPrice' => $_POST["variation_subtract_price_{$varIndex}"][$i] ?? '',
                                'imageBase64'   => $_POST["variation_image_base64_{$varIndex}"][$i] ?? ''
                            ];
                        }
                    }
                }
            }
        }
        
        if (empty($productNameErr) && empty($categoryErr) && empty($descriptionErr) &&
            empty($basePriceErr) && empty($imagePathErr) && empty($initialStockErr)) {

            // Update the main product details.
            $updatedProduct = $productObj->updateProduct(
                $product_id,
                $stall_id,
                $productName,
                $category,
                $description,
                $basePrice,
                $discount,
                $startDate,
                $endDate,
                $imagePath
            );
            
            $updatedVariationsResult = $productObj->updateProductVariations(
                $product_id,
                $updatedVariations, 
                $hasVariations ? null : $initialStock
            );
            
            if ($updatedProduct && $updatedVariationsResult) {
                header("Location: managemenu.php");
                exit;
            }
        }
    }
}
ob_end_flush();
?>
<style>
    main { padding: 40px 200px; }
    .getcg td { padding: 20px; border-bottom: 1px solid #ddd; line-height: 1.5; }
    .addchogro { text-decoration: none; color: #CD5C08; margin-top: 10px; }
    .addchogro:hover { color: black; }
    .errormessage { color: red; font-size: small; }
    .disabled-stock { background-color: #e9ecef; pointer-events: none; }
    .overlay { display: flex; align-items: center; justify-content: center; }
    .overlay i { width: 24px; }
</style>
<div class="prohelp d-flex align-items-center gap-4 justify-content-center">
    <span class="helpspn">HELP</span>
    <p class="m-0">Edit the product details below to update your product in the inventory.</p>
    <a href="">Terms and Conditions <i class="fa-solid fa-arrow-right"></i></a>
</div>
<main>
    <form class="productcon" method="post" enctype="multipart/form-data">
        <div>
            <label for="productimage" class="mb-2">Product Image</label>
            <div class="productimage text-center py-5 px-3 mb-3" id="productimageContainer" onclick="document.getElementById('productimage').click();">
                <div id="placeholderContent">
                    <?php if (!empty($imagePath)): ?>
                        <img src="<?= $imagePath ?>?t=<?= time() ?>" alt="Product Image" style="max-width:100%;">
                    <?php else: ?>
                        <i class="fa-solid fa-arrow-up-long mb-3"></i>
                        <p class="small m-0">Select an image to upload. Or drag the image file here.</p>
                    <?php endif; ?>
                </div>
                <input type="file" id="productimage" name="productimage" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayProductImage(event)">
            </div>
            <p class="text-muted pirem m-0 mb-3">
                Recommended size is 160x151. Image must be less than 500KB. Only JPG, JPEG, and PNG formats are allowed.
            </p>
            <span class="errormessage"><?php echo $imagePathErr; ?></span>
            <input type="hidden" name="tempImagePath" id="tempImagePath" value="<?php echo htmlspecialchars($imagePath); ?>">
            <script>
                window.addEventListener('load', function() {
                    const tempPath = document.getElementById('tempImagePath').value;
                    if (tempPath) {
                        const container = document.getElementById('productimageContainer');
                        const placeholderContent = document.getElementById('placeholderContent');
                        container.style.backgroundImage = "url(" + tempPath + ")";
                        container.style.backgroundSize = 'cover';
                        container.style.backgroundPosition = 'center';
                        placeholderContent.style.display = 'none';
                    }
                });
                function displayProductImage(event) {
                    const file = event.target.files[0];
                    if (file && file.size <= 500 * 1024) {
                        const formData = new FormData();
                        formData.append('productimage', file);
                        fetch('upload_temp.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const productImageContainer = document.getElementById('productimageContainer');
                                const placeholderContent = document.getElementById('placeholderContent');
                                productImageContainer.style.backgroundImage = "url(" + data.filePath + ")";
                                productImageContainer.style.backgroundSize = 'cover';
                                productImageContainer.style.backgroundPosition = 'center';
                                placeholderContent.style.display = 'none';
                                document.getElementById('tempImagePath').value = data.filePath;
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch(error => { console.error('Error:', error); });
                    } else {
                        alert('File is too large or not supported. Please select a JPG, JPEG, or PNG image under 500KB.');
                    }
                }
            </script>
        </div>
        <div class="flex-grow-1">
            <div class="input-group m-0 mb-4">
                <label for="productname">Product Name</label>
                <input type="text" name="productname" id="productname" placeholder="Enter product name" value="<?php echo htmlspecialchars($productName); ?>"/>
                <span class="errormessage"><?php echo $productNameErr; ?></span>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <div class="input-group m-0 mb-4">
                    <label for="category">Category</label>
                    <select name="category" id="category" style="padding: 10.5px 0.75rem">
                        <option value="" disabled <?php echo empty($category) ? "selected" : ""; ?>>Select</option>
                        <?php
                            foreach ($selectCategories as $cat) {
                                $selected = ($category == $cat['id']) ? "selected" : "";
                                echo '<option value="'.$cat['id'].'" '.$selected.'>'.$cat['name'].'</option>';
                            }
                        ?>
                    </select>
                    <span class="errormessage"><?php echo $categoryErr; ?></span>
                </div>
                <button type="button" class="variation-btn addvar flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addcategory">+ Add Category</button>
            </div>
            <div class="input-group m-0 mb-4">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Enter product description"><?php echo htmlspecialchars($description); ?></textarea>
                <span class="errormessage"><?php echo $descriptionErr; ?></span>
            </div>
            <div class="input-group m-0 mb-4">
                <label for="sellingPrice">Selling Price</label>
                <input type="number" name="sellingPrice" id="sellingPrice" placeholder="Enter selling price" step="0.01" value="<?php echo htmlspecialchars($basePrice); ?>"/>
                <span class="errormessage"><?php echo $basePriceErr; ?></span>
            </div>
            <div class="input-group m-0 mb-4" id="initialStockContainer">
                <label for="initialStock">Initial Stock</label>
                <input type="number" name="initialStock" id="initialStock" placeholder="Enter initial stock" value="<?php echo htmlspecialchars($initialStock); ?>"/>
                <span class="errormessage"><?php echo $initialStockErr; ?></span>
            </div>
            <div class="input-group m-0 mb-4">
                <label for="">Variants (Optional)</label>
                <div class="variation-container">
                    <div class="d-flex justify-content-end pe-3">
                        <button type="button" class="variation-btn addvar" onclick="addVariationForm()">+ Add Variation</button>
                    </div>
                    <div class="variation-forms-wrapper" id="variation-forms-list"></div>
                </div>
            </div>
            <script>
                var variationStockError = <?php echo json_encode($variationStockErr); ?>;
                let variationFormCount = 0;
                function addVariationForm() {
                    variationFormCount++;
                    const variationForm = document.createElement("div");
                    variationForm.className = "variation-form";
                    variationForm.id = `variation-form-${variationFormCount}`;
                    variationForm.innerHTML = `
                        <div class="variation-header"> 
                            <span id="variation-title-${variationFormCount}" class="fw-bold fs-5">Variation ${variationFormCount}</span>
                            <button type="button" class="variation-btn rename" onclick="renameVariation(${variationFormCount})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <input type="hidden" name="variation_title_${variationFormCount}" id="variation-input-${variationFormCount}" value="Variation ${variationFormCount}">
                        </div>
                        <div class="variation-rows-container my-2" id="variation-rows-container-${variationFormCount}">
                            ${createVariationRow(variationFormCount, Date.now())}
                            ${createVariationRow(variationFormCount, Date.now()+1)}
                            ${createVariationRow(variationFormCount, Date.now()+2)}
                        </div>
                        <div class="variation-btn-group">
                            <button type="button" class="variation-btn addrem" onclick="addVariationRow(${variationFormCount})">Add New Row</button>
                            <button type="button" class="variation-btn addrem" onclick="removeVariationForm(${variationFormCount})">Remove Variation</button>
                        </div>
                    `;
                    document.getElementById("variation-forms-list").appendChild(variationForm);
                    document.getElementById("initialStock").disabled = true;
                    document.getElementById("initialStockContainer").classList.add("disabled-stock");
                }
                function createVariationRow(variationFormId, rowId) {
                    return `
                        <div class="variation-row" id="variation-row-${variationFormId}-${rowId}">
                            <div class="variationimage text-center" id="variationimageContainer-${variationFormId}-${rowId}" onclick="triggerFileInput(${variationFormId}, ${rowId})">
                                <div class="overlay">
                                    <i class="fa-solid fa-arrow-up-long mb-1"></i>
                                    <span>Variation Image</span>
                                </div>
                                <input type="file" name="variationimage_${variationFormId}[]" id="variationimage-${variationFormId}-${rowId}" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displaySelectedImage(${variationFormId}, ${rowId})">
                            </div>
                            <input type="hidden" name="variation_image_base64_${variationFormId}[]" id="variation_image_base64_${variationFormId}_${rowId}" value="">
                            <input type="text" name="variation_name_${variationFormId}[]" placeholder="Option Name">
                            <div>
                                <input type="number" name="variation_initial_stock_${variationFormId}[]" placeholder="Stock" min="0" step="1" class="inst">
                                ${variationStockError ? `<span class="errormessage">${variationStockError}</span>` : ''}
                            </div>
                            <div class="d-flex align-items-center addpeso">
                                <input type="number" name="variation_additional_price_${variationFormId}[]" placeholder="0.00" min="0" step="0.01">
                            </div>
                            <div class="d-flex align-items-center minuspeso">
                                <input type="number" name="variation_subtract_price_${variationFormId}[]" placeholder="0.00" min="0" step="0.01">
                            </div>
                            <button type="button" class="variation-btn delete" onclick="removeVariationRow(this)">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    `;
                }
                function triggerFileInput(variationFormId, rowId) {
                    const inputFile = document.getElementById(`variationimage-${variationFormId}-${rowId}`);
                    inputFile.value = ''; 
                    inputFile.click();
                }
                function displaySelectedImage(variationFormId, rowId) {
                    const inputFile = document.getElementById(`variationimage-${variationFormId}-${rowId}`);
                    const imageContainer = document.getElementById(`variationimageContainer-${variationFormId}-${rowId}`);
                    if (inputFile.files.length > 0) {
                        const formData = new FormData();
                        formData.append('productimage', inputFile.files[0]);
                        fetch('upload_temp.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success){
                                imageContainer.style.backgroundImage = "url(" + data.filePath + ")";
                                imageContainer.style.backgroundSize = 'cover';
                                imageContainer.style.backgroundPosition = 'center';
                                imageContainer.querySelector('.overlay').style.display = 'none';
                                document.getElementById(`variation_image_base64_${variationFormId}_${rowId}`).value = data.filePath;
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch(error => { console.error('Error:', error); });
                    }
                }
                function addVariationRow(variationFormId) {
                    const rowId = Date.now();
                    const variationRowsContainer = document.getElementById(`variation-rows-container-${variationFormId}`);
                    variationRowsContainer.insertAdjacentHTML("beforeend", createVariationRow(variationFormId, rowId));
                }
                function removeVariationRow(button) {
                    const variationRow = button.parentNode;
                    variationRow.remove();
                }
                function removeVariationForm(variationFormId) {
                    const variationForm = document.getElementById(`variation-form-${variationFormId}`);
                    variationForm.remove();
                    if (document.getElementById("variation-forms-list").childElementCount === 0) {
                        document.getElementById("initialStock").disabled = false;
                        document.getElementById("initialStockContainer").classList.remove("disabled-stock");
                    }
                }
                function renameVariation(variationFormId) {
                    const newTitle = prompt("Enter a new name for this variation:");
                    if (newTitle && newTitle.trim()) {
                        document.getElementById(`variation-title-${variationFormId}`).textContent = newTitle.trim();
                        document.getElementById(`variation-input-${variationFormId}`).value = newTitle.trim();
                    }
                }
                <?php if (!empty($variationsData)) : ?>
                var savedVariations = <?php echo json_encode($variationsData); ?>;
                for (var varIndex in savedVariations) {
                    addSavedVariation(varIndex, savedVariations[varIndex]);
                }
                if (Object.keys(savedVariations).length > 0) {
                    document.getElementById("initialStock").disabled = true;
                    document.getElementById("initialStockContainer").classList.add("disabled-stock");
                }
                <?php endif; ?>
                function addSavedVariation(index, data) {
                    if (index > variationFormCount) {
                        variationFormCount = parseInt(index);
                    }
                    var variationForm = document.createElement("div");
                    variationForm.className = "variation-form";
                    variationForm.id = "variation-form-" + index;
                    variationForm.innerHTML = '<div class="variation-header">' +
                        '<span id="variation-title-' + index + '" class="fw-bold fs-5">' + data.title + '</span>' +
                        '<button type="button" class="variation-btn rename" onclick="renameVariation(' + index + ')">' +
                        '<i class="fa-solid fa-pen"></i>' +
                        '</button>' +
                        '<input type="hidden" name="variation_title_' + index + '" id="variation-input-' + index + '" value="' + data.title + '">' +
                        '</div>' +
                        '<div class="variation-rows-container my-2" id="variation-rows-container-' + index + '"></div>' +
                        '<div class="variation-btn-group">' +
                        '<button type="button" class="variation-btn addrem" onclick="addVariationRow(' + index + ')">Add New Row</button>' +
                        '<button type="button" class="variation-btn addrem" onclick="removeVariationForm(' + index + ')">Remove Variation</button>' +
                        '</div>';
                    document.getElementById("variation-forms-list").appendChild(variationForm);
                    var container = document.getElementById("variation-rows-container-" + index);
                    data.rows.forEach(function(row) {
                        var rowId = Date.now() + Math.floor(Math.random() * 1000);
                        var rowHtml = '<div class="variation-row" id="variation-row-' + index + '-' + rowId + '">' +
                            '<div class="variationimage text-center" id="variationimageContainer-' + index + '-' + rowId + '" onclick="triggerFileInput(' + index + ', ' + rowId + ')">' +
                            '<div class="overlay">' +
                            '<i class="fa-solid fa-arrow-up-long mb-1"></i>' +
                            '<span>Variation Image</span>' +
                            '</div>' +
                            '<input type="file" name="variationimage_' + index + '[]" id="variationimage-' + index + '-' + rowId + '" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displaySelectedImage(' + index + ', ' + rowId + ')">' +
                            '</div>' +
                            '<input type="hidden" name="variation_image_base64_' + index + '[]" id="variation_image_base64_' + index + '_' + rowId + '" value="' + (row.imageBase64 ? row.imageBase64 : '') + '">' +
                            '<input type="text" name="variation_name_' + index + '[]" placeholder="Option Name" value="' + row.optionName + '">' +
                            '<div>' +
                            '<input type="number" name="variation_initial_stock_' + index + '[]" placeholder="Stock" min="0" step="1" class="inst" value="' + row.initialStock + '">' +
                            '</div>' +
                            '<div class="d-flex align-items-center addpeso">' +
                            '<input type="number" name="variation_additional_price_' + index + '[]" placeholder="0.00" min="0" step="0.01" value="' + row.addPrice + '">' +
                            '</div>' +
                            '<div class="d-flex align-items-center minuspeso">' +
                            '<input type="number" name="variation_subtract_price_' + index + '[]" placeholder="0.00" min="0" step="0.01" value="' + row.subtractPrice + '">' +
                            '</div>' +
                            '<button type="button" class="variation-btn delete" onclick="removeVariationRow(this)">' +
                            '<i class="fa-solid fa-xmark"></i>' +
                            '</button>' +
                            '</div>';
                        container.insertAdjacentHTML("beforeend", rowHtml);
                        if (row.imageBase64) {
                            document.getElementById("variationimageContainer-" + index + "-" + rowId).style.backgroundImage = "url(" + row.imageBase64 + ")";
                            document.getElementById("variationimageContainer-" + index + "-" + rowId).querySelector('.overlay').style.display = 'none';
                        }
                    });
                }
            </script>
            <div class="d-flex gap-3">
                <div class="input-group w-50 m-0 mb-4">
                    <label for="discount">Discount (Optional)</label>
                    <input type="number" name="discount" id="discount" placeholder="Enter discount" step="0.01" value="<?php echo htmlspecialchars($discount); ?>"/>
                    <span class="errormessage"><?php echo $discountErr; ?></span>
                </div>
                <div class="d-flex gap-2 w-50">
                    <div class="input-group m-0 mb-4">
                        <label for="startDate">Start Date</label>
                        <input type="date" name="startDate" id="startDate" value="<?php echo htmlspecialchars($startDate); ?>"/>
                    </div>
                    <div class="input-group m-0 mb-4">
                        <label for="endDate">End Date</label>
                        <input type="date" name="endDate" id="endDate" value="<?php echo htmlspecialchars($endDate); ?>"/>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary send px-5 mt-3">UPDATE PRODUCT</button>
            <br><br><br><br>
        </div>
    </form>
</main>
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

<script>
document.querySelector('form.productcon').addEventListener('submit', function(e) {
    document.querySelectorAll('.variation-stock-error').forEach(function(span) {
        span.remove();
    });
    let hasError = false;
    document.querySelectorAll('input.inst').forEach(function(input) {
        const value = input.value.trim();
        let errorMsg = "";
        if (value === "") {
            errorMsg = "Required.";
        } else if (isNaN(value) || Number(value) < 0) {
            errorMsg = "Non-negative.";
        }
        if (errorMsg !== "") {
            hasError = true;
            const errorSpan = document.createElement('span');
            errorSpan.className = 'errormessage variation-stock-error';
            errorSpan.style.color = 'red';
            errorSpan.style.fontSize = 'small';
            errorSpan.innerText = errorMsg;
            input.parentNode.insertBefore(errorSpan, input.nextSibling);
        }
    });
    if (hasError) {
        e.preventDefault();
    }
});
</script>

<?php include_once './footer.php'; ?>
