<?php
ob_end_clean();
ob_start();
include_once 'links.php'; 
include_once 'header.php';
require_once __DIR__ . '/classes/stall.class.php';
require_once __DIR__ . '/classes/product.class.php';
require_once __DIR__ . '/classes/encdec.class.php';
    
$productObj = new Product();
$stallObj = new Stall();

$product_id = isset($_GET['id']) ? intval(urldecode(decrypt($_GET['id']))) : 0;
$product = $productObj->getProductById($product_id);
if (!$product) {
    // Redirect to managemenu.php using javascript
    echo '<script>window.location.href = "managemenu.php";</script>';
    exit;
}

// Get columns
$productName = $product['name'];
$productCode = $product['code'];
$category = $product['category_id'];
$stall_id = $stallObj->getStallId($_SESSION['user']['id']);
$selectCategories = $productObj->getCategories($stall_id);
$description = $product['description'];
$basePrice = $product['base_price'];
$discount = $product['discount'];
$startDate = $product['start_date'];
$endDate = $product['end_date'];
$imagePath = $product['image'];

$imagePathErr = $productNameErr = $categoryErr = $descriptionErr = $basePriceErr = $startDateErr = $endDateErr = $discountErr = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_category'])) {
        $newCategory = trim($_POST['new_category']);
        if (!empty($newCategory)) {
            $productObj->addCategory($stall_id, $newCategory);
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . urlencode(encrypt($product_id)));
        exit;
    } else {
        $productName = clean_input($_POST['productname']);
        $category    = isset($_POST['category']) ? clean_input($_POST['category']) : '';
        $description = clean_input($_POST['description']);
        $basePrice   = clean_input($_POST['sellingPrice']);
        $discount    = clean_input($_POST['discount'] ?? 0);
        $startDate   = !empty($_POST['startDate']) ? clean_input($_POST['startDate']) : NULL;
        $endDate     = !empty($_POST['endDate']) ? clean_input($_POST['endDate']) : NULL;

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

        if (empty($productNameErr) && empty($categoryErr) && empty($descriptionErr) &&
            empty($basePriceErr) && empty($imagePathErr)) {
                
            $productObj->updateProduct($product_id, $productName, $category, $description, $basePrice, $discount, $startDate, $endDate, $imagePath);
            header("Location: managemenu.php");
            exit;
        }
    }
}
ob_end_flush();
?>
<style>
    main{
        padding: 40px 200px;
    }
    .getcg td{
        padding: 20px;
        border-bottom: 1px solid #ddd;
        line-height: 1.5;
    }
    .getcg .st{
        vertical-align: top;
    }
    .addchogro{
        text-decoration: none;
        color: #CD5C08;
        margin-top: 10px;
    }
    .addchogro:hover{
        color: black;
    }
    .errormessage {
        color: red;
        font-size: small;
    }
    .disabled-stock {
        background-color: #e9ecef;
        pointer-events: none;
    }
</style>
<div class="prohelp d-flex align-items-center gap-4 justify-content-center">
    <span class="helpspn">HELP</span>
    <p class="m-0">Provide all the necessary information in the fields below to edit your product details.</p>
    <a href="">Terms and Conditions <i class="fa-solid fa-arrow-right"></i></a>
</div>
<main>
    <form class="productcon" method="post" enctype="multipart/form-data">
        <div>
            <label for="productimage" class="mb-2">Product Image</label>
            <div class="productimage text-center py-5 px-3 mb-3" 
                id="productimageContainer" 
                onclick="document.getElementById('productimage').click();" 
                style="background-image: url('<?php echo $imagePath; ?>'); background-size: cover; background-position: center;">
                <div id="placeholderContent">
                    <i class="fa-solid fa-arrow-up-long mb-3"></i>
                    <p class="small m-0">Select an image to upload. Or drag the image file here.</p>
                </div>
                <input type="file" id="productimage" name="productimage" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayProductImage(event)">
            </div>
            <p class="text-muted pirem m-0 mb-3">Recommended size is 160x151. Image must be less than 500kb. Only JPG, JPEG, and PNG formats are allowed. File name can only be in English letters and numbers.</p>
            <span class="errormessage"><?php echo $imagePathErr; ?></span>
            <input type="hidden" name="tempImagePath" id="tempImagePath" value="<?php echo isset($_POST['tempImagePath']) ? htmlspecialchars($_POST['tempImagePath']) : $imagePath; ?>">
        </div>
        
        <div class="flex-grow-1">
            <div class="input-group m-0 mb-4">
                <label for="productname">Product Name</label>
                <input type="text" name="productname" id="productname" placeholder="Enter product name" value="<?php echo htmlspecialchars($productName); ?>"/>
                <span class="errormessage"><?php echo $productNameErr; ?></span>
            </div>
            <div class="d-flex gap-3">
                <div class="input-group m-0 mb-4">
                    <label for="category">Category</label>
                    <select name="category" id="category" style="padding: 10.5px 0.75rem">
                        <option value="">Select</option>
                        <?php
                            foreach ($selectCategories as $cat) {
                                $selected = ($category == $cat['id']) ? "selected" : "";
                                echo '<option value="'.$cat['id'].'" '.$selected.'>'.$cat['name'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <span class="errormessage"><?php echo $categoryErr; ?></span>
                <button type="button" class="variation-btn addvar flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addcategory">+ Add Category</button>
            </div>
            <div class="input-group m-0 mb-4">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Enter product description"><?php echo htmlspecialchars($description); ?></textarea>
                <span class="errormessage"><?php echo $descriptionErr; ?></span>
            </div>
            
            <div class="d-flex gap-3">
                <div class="input-group m-0 mb-4">
                    <label for="sellingPrice">Selling Price</label>
                    <input type="number" name="sellingPrice" id="sellingPrice" placeholder="Enter selling price" step="0.01" value="<?php echo htmlspecialchars($basePrice); ?>"/>
                    <span class="errormessage"><?php echo $basePriceErr; ?></span>
                </div>
            </div>

            <!-- Variation -->
            <div class="input-group m-0 mb-4">
                <label for="">Variants (Optional)</label>
                <div class="variation-container">
                    <div class="d-flex justify-content-end pe-3">
                        <button type="button" class="variation-btn addvar" onclick="addVariationForm()">+ Add Variation</button>
                    </div>
                    <div class="variation-forms-wrapper" id="variation-forms-list"></div>
                </div>
            </div>
            <script src="./assets/js/editvariation.js?v=<?php echo time(); ?>"></script>

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
                        <span class="errormessage"><?php echo $startDateErr; ?></span>
                    </div>
                    <div class="input-group m-0 mb-4">
                        <label for="endDate">End Date</label>
                        <input type="date" name="endDate" id="endDate" value="<?php echo htmlspecialchars($endDate); ?>"/>
                        <span class="errormessage"><?php echo $endDateErr; ?></span>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary send px-5 mt-3">UPDATE PRODUCT</button><br><br><br><br>
        </div>
    </form>
</main>

<!-- Add Category Modal -->
<div class="modal fade" id="addcategory" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_category">New Category</label>
                        <input type="text" class="form-control" name="new_category" id="new_category" placeholder="Enter new category">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function displayProductImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageUrl = e.target.result;
                document.getElementById('productimageContainer').style.backgroundImage = `url('${imageUrl}')`;
                document.getElementById('placeholderContent').style.display = 'none';
                document.getElementById('tempImagePath').value = '';
            };
            reader.readAsDataURL(file);
        }
    }
</script>

<?php
    include_once './footer.php'; 
?>
