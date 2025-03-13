<?php
ob_start();
include_once 'links.php'; 
include_once 'header.php'; 
require_once __DIR__ . '/classes/product.class.php';
require_once __DIR__ . '/classes/stall.class.php';

$productObj = new Product();
$stallObj = new Stall();

$productName = $productCode = $category = $description = $basePrice = $discount = $startDate = $endDate = $imagePath = '';
$imagePathErr = $productNameErr = $productCodeErr = $categoryErr = $descriptionErr = $basePriceErr = $startDateErr = $endDateErr = $discountErr = '';

$stall_id = $stallObj->getStallId($_SESSION['user']['id']);

$selectCategories = $productObj->getCategories($stall_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = clean_input($_POST['productname']);
    // $productCode = clean_input($_POST['productcode']);
    $productCode = uniqid();
    $category = isset($_POST['category']) ? clean_input($_POST['category']) : '';
    $description = clean_input($_POST['description']);
    $basePrice = clean_input($_POST['sellingPrice']);
    $discount = clean_input($_POST['discount'] ?? 0);
    $startDate = !empty($_POST['startDate']) ? clean_input($_POST['startDate']) : NULL;
    $endDate = !empty($_POST['endDate']) ? clean_input($_POST['endDate']) : NULL;
    
    // Validation
    if (empty($productName)) {
        $productNameErr = 'Product name is required.';
    }

    if (empty($productCode)) {
        $productCodeErr = 'Product code is required.';
    } elseif ($productObj->isProductCodeExists($productCode)) {
        $productCodeErr = 'Product code already exists. Choose a different one.';
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
        } elseif ($imageSize > 500000) { // 500KB limit
            $imagePathErr = "Image size must be less than 500KB.";
        } else {
            $imagePath = $targetDir . basename($_FILES["productimage"]["name"]);
            move_uploaded_file($_FILES["productimage"]["tmp_name"], $imagePath);
        }
    } else {
        $imagePathErr = "Product image is required.";
    }

    if (empty($productNameErr) && empty($productCodeErr) && empty($categoryErr) && empty($descriptionErr) && empty($basePriceErr) && empty($imagePathErr)) {
        $productId = $productObj->addProduct($stall_id, $productName, $productCode, $category, $description, $basePrice, $discount, $startDate, $endDate, $imagePath);

        if ($productId) {
            if (isset($_POST['variation_name_1'])) {
                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'variation_name_') !== false) {
                        $variationIndex = explode("_", $key)[2];
                        $variationName = $_POST["variation_title_{$variationIndex}"] ?? "Variation {$variationIndex}";

                        $variationId = $productObj->addVariations($productId, $variationName);

                        foreach ($_POST["variation_name_{$variationIndex}"] as $optionIndex => $optionName) {
                            $addPrice = $_POST["variation_additional_price_{$variationIndex}"][$optionIndex] ?? 0;
                            $subtractPrice = $_POST["variation_subtract_price_{$variationIndex}"][$optionIndex] ?? 0;

                            $variationImagePath = NULL;
                            if (!empty($_FILES["variationimage_{$variationIndex}"]["name"][$optionIndex])) {
                                $variationImagePath = "uploads/" . basename($_FILES["variationimage_{$variationIndex}"]["name"][$optionIndex]);
                                move_uploaded_file($_FILES["variationimage_{$variationIndex}"]["tmp_name"][$optionIndex], $variationImagePath);
                            }

                            $variationOptionId = $productObj->addVariationOptions($variationId, $optionName, $addPrice, $subtractPrice, $variationImagePath);

                            $addStockSuccess = $productObj->addStock($productId, $variationOptionId);
                        }
                    }
                }
            }
            
            header("Location: managemenu.php");
            exit;
        }
    }
}
ob_end_flush();
?>

<style>
    main{
        padding: 40px 200px
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
    .errormessage{
        color: red;
        font-size: small;
    }
    
</style>
<div class="prohelp d-flex align-items-center gap-4 justify-content-center">
    <span class="helpspn">HELP</span>
    <p class="m-0">Provide all the necessary information in the fields below to successfully add a new product to your inventory.</p>
    <a href="">Terms and Conditions <i class="fa-solid fa-arrow-right"></i></a>
</div>
<main>
    <form class="productcon" method="post" enctype="multipart/form-data">
        <div>
            <label for="productimage" class="mb-2">Product Image</label>
            <div class="productimage text-center py-5 px-3 mb-3" id="productimageContainer" onclick="document.getElementById('productimage').click();">
                <div id="placeholderContent">
                    <i class="fa-solid fa-arrow-up-long mb-3"></i>
                    <p class="small m-0">Select an image to upload. Or drag the image file here.</p>
                </div>
                <input type="file" id="productimage" name="productimage" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayProductImage(event)">
            </div>
            <p class="text-muted pirem m-0 mb-3">Recommended size is 160x151. Image must be less than 500kb. Only JPG, JPEG, and PNG formats are allowed. File name can only be in English letters and numbers.</p>
            <span class="errormessage"><?php echo $imagePathErr; ?></span>
            <script>
                function displayProductImage(event) {
                    const file = event.target.files[0];
                    if (file && file.size <= 500 * 1024) { // Ensure file size is less than 500KB
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const productImageContainer = document.getElementById('productimageContainer');
                            const placeholderContent = document.getElementById('placeholderContent');

                            productImageContainer.style.backgroundImage = `url(${e.target.result})`;
                            productImageContainer.style.backgroundSize = 'cover';
                            productImageContainer.style.backgroundPosition = 'center';
                            placeholderContent.style.display = 'none'; // Hide placeholder content
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert('File is too large or not supported. Please select a JPG, JPEG, or PNG image under 500KB.');
                    }
                }
            </script>
        </div>

        <div class="flex-grow-1">
            <div class="input-group m-0 mb-4">
                <label for="productname">Product Name</label>
                <input type="productname" name="productname" id="productname" placeholder="Enter product name"/>     
                <span class="errormessage"><?php echo $productNameErr; ?></span>       
            </div>
            <div class="d-flex gap-3">
                <!-- <div class="input-group m-0 mb-4">
                    <label for="productcode">Product Code</label>
                    <input type="productcode" name="productcode" id="productcode" placeholder="Enter product code"/>  
                    <span class="errormessage"><?php echo $productCodeErr; ?></span>                
                </div> -->
                <div class="input-group m-0 mb-4">
                    <label for="category">Category</label>
                    <select name="category" id="category" style="padding: 10.5px 0.75rem">
                        <option value="" disabled selected>Select</option>
                        <?php
                            foreach($selectCategories as $category){
                                echo '<option value="'.$category['id'].'">'.$category['name'].'</option>';
                            }
                        ?>
                    </select>
                    <span class="errormessage"><?php echo $categoryErr; ?></span>
                </div>
            </div>
            <div class="input-group m-0 mb-4">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Enter product description"></textarea>
                <span class="errormessage"><?php echo $descriptionErr; ?></span>
            </div>
            
            <div class="input-group m-0 mb-4">
                <label for="sellingPrice">Selling Price</label>
                <input type="number" name="sellingPrice" id="sellingPrice" placeholder="Enter selling price" step="0.01"/>
                <span class="errormessage"><?php echo $basePriceErr; ?></span>
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
            <script src="assets/js/variation.js?v=<?php echo time(); ?>"></script>

            <div class="d-flex gap-3">
                <div class="input-group w-50 m-0 mb-4">
                    <label for="discount">Discount (Optional)</label>
                    <input type="number" name="discount" id="discount" placeholder="Enter discount" step="0.01"/>
                    <span class="errormessage"><?php echo $discountErr; ?></span>
                </div>
                <div class="d-flex gap-2 w-50">
                    <div class="input-group m-0 mb-4">
                        <label for="startDate">Start Date</label>
                        <input type="date" name="startDate" id="startDate"/>
                    </div>
                    <div class="input-group m-0 mb-4">
                        <label for="endDate">End Date</label>
                        <input type="date" name="endDate" id="endDate"/>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary send px-5 mt-3">ADD PRODUCT</button><br><br><br><br>
        </div>
    </form>
</main>
<?php
    include_once './footer.php'; 
?>