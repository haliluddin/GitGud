<?php
    include_once 'links.php'; 
    include_once 'header.php'; 
    include_once 'nav.php'; 
    include_once 'modals.php'; 
    require_once __DIR__ . '/classes/stall.class.php';
    require_once __DIR__ . '/classes/product.class.php';

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $user_id = $_SESSION['user']['id'];
        $stallObj = new Stall();
        $stall_id = $stallObj->getStallId($_SESSION['user']['id']);
        $stall = $stallObj->getStall($stall_id);
        $totalProducts = $stallObj->getTotalProducts($stall_id);
        $stall_name = $stall['name'];
        $description = $stall['description'];
        
        $productObj = new Product();
    }
?>
<main>
    <div class="pageinfo pb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex gap-4 align-items-center pagelogo">
                <img src="assets/images/foodpark.jpg" alt="">
                <div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="text-muted m-0">Category</span>
                        <span class="dot text-muted"></span>
                        <span class="text-muted m-0">Category</span>
                        <span class="dot text-muted"></span>
                        <span class="text-muted m-0">Category</span>
                    </div>
                    <h5 class="my-2 fw-bold fs-2"><?php echo $stall_name; ?></h5>
                    <p class="text-muted m-0"><?php echo $description; ?></p>

                    <div class="d-flex gap-2 align-items-center my-2">
                        <span class="pageon">Open now</span>
                        <span class="dot text-muted"></span>
                        <button class="conopepay" data-bs-toggle="modal" data-bs-target="#morestallinfo"><i class="fa-solid fa-circle-info"></i> More info</button>
                    </div>

                    <div class="d-flex gap-5 m-0">
                        <div class="d-flex gap-2">
                            <span>Likes</span>
                            <span class="likpro">999</span>
                        </div>
                        <div class="d-flex gap-2">
                            <span>Products</span>
                            <span class="likpro"><?php echo $totalProducts; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <button class="pagelike" onclick="window.location.href='editpage.php';">Edit Page</button>
        </div>
    </div>

    <div class="d-flex pagefilter align-items-center gap-3">
        <div class="d-flex align-items-center gap-3 leftfilter">
            <form action="#" method="get" class="searchmenu">
                <button type="submit"><i class="fas fa-search fa-lg"></i></button>
                <input type="text" name="search" placeholder="Search in menu">
            </form>
            <a href="#popular" class="nav-link"><i class="fa-solid fa-fire-flame-curved"></i> Popular</a>
            <a href="#new" class="nav-link"><i class="fa-solid fa-ribbon"></i> New</a>
            <a href="#promo" class="nav-link"><i class="fa-solid fa-percent"></i> Promo</a>
        </div>

        <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>

        <div class="d-flex rightfilter gap-3">
            <a href="#category1" class="nav-link">Category 1</a>
            <a href="#category2" class="nav-link">Category 2</a>
            <a href="#category3" class="nav-link">Category 3</a>
            <a href="#category1" class="nav-link">Category 1</a>
            <a href="#category2" class="nav-link">Category 2</a>
            <a href="#category3" class="nav-link">Category 3</a>
            <a href="#category1" class="nav-link">Category 1</a>
            <a href="#category2" class="nav-link">Category 2</a>
            <a href="#category3" class="nav-link">Category 3</a>
        </div>

        <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
    </div>

    <section id="popular" class="pt-3 mt-3">
        <h5 class="mb-3 fw-bold">POPULAR</h5>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="editproduct.php" class="card-link text-decoration-none">
                    <div class="card position-relative">
                        <img src="assets/images/example.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen" style="font-size: 18px;"></i></button>
                        <div class="card-body">
                            <p class="card-text text-muted m-0">Category</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Beef and cheese on a thin crust Pizza</p>
                            <div class="d-flex align-items-center justify-content-between my-3">
                                <div>
                                    <span class="proprice">₱103</span>
                                    <span class="pricebefore small">₱103</span>
                                </div>
                                <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                            </div>                          
                            <div class="m-0">
                                <span class="opennow">Popular</span>
                                <span class="discount">10% off</span>
                                <span class="newopen">New</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php
                $products = $productObj->getProducts($stall_id);

                if ($products) {
                    foreach ($products as $product) {
                        echo '
                            <div class="col">
                                <a href="#" 
                                class="card-link text-decoration-none" 
                                data-bs-toggle="modal" 
                                data-bs-target="#menumodal"
                                data-name="' . htmlspecialchars($product['name']) . '"
                                data-description="' . htmlspecialchars($product['description']) . '"
                                data-price="' . number_format($product['price'], 2) . '"
                                data-image="' . htmlspecialchars($product['file_path']) . '"
                                data-product-id="' . htmlspecialchars($product['id']) . '"
                                data-stall-id="' . htmlspecialchars($stall_id) . '"
                                >
                                    <div class="card position-relative">
                                        <img src="' . htmlspecialchars($product['file_path']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '">
                                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                                        <div class="card-body">
                                            <p class="card-text text-muted m-0">' . htmlspecialchars($product['category']) . '</p>
                                            <h5 class="card-title my-2">' . htmlspecialchars($product['name']) . '</h5>
                                            <p class="card-text text-muted m-0">' . htmlspecialchars($product['description']) . '</p>
                                            <div class="d-flex align-items-center justify-content-between my-3">
                                                <div>
                                                    <span class="proprice">₱' . number_format($product['price'], 2) . '</span>
                                                </div>
                                                <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                                            </div>                          
                                            <div class="m-0">
                                                <span class="opennow">Popular</span>
                                                <span class="discount">10% off</span>
                                                <span class="newopen">New</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        ';
                    }
                }
            ?>
        </div>
    </section>

    <section id="new" class="pt-3 mt-3">
        <h5 class="mb-3 fw-bold">NEW</h5>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="editproduct.php" class="card-link text-decoration-none">
                    <div class="card position-relative">
                        <img src="assets/images/example.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen" style="font-size: 18px;"></i></button>
                        <div class="card-body">
                            <p class="card-text text-muted m-0">Category</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Beef and cheese on a thin crust Pizza</p>
                            <div class="d-flex align-items-center justify-content-between my-3">
                                <div>
                                    <span class="proprice">₱103</span>
                                    <span class="pricebefore small">₱103</span>
                                </div>
                                <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                            </div>                          
                            <div class="m-0">
                                <span class="opennow">Popular</span>
                                <span class="discount">10% off</span>
                                <span class="newopen">New</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section id="promo" class="pt-3 mt-3">
        <h5 class="mb-3 fw-bold">PROMO</h5>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="editproduct.php" class="card-link text-decoration-none">
                    <div class="card position-relative">
                        <img src="assets/images/example.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen" style="font-size: 18px;"></i></button>
                        <div class="card-body">
                            <p class="card-text text-muted m-0">Category</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Beef and cheese on a thin crust Pizza</p>
                            <div class="d-flex align-items-center justify-content-between my-3">
                                <div>
                                    <span class="proprice">₱103</span>
                                    <span class="pricebefore small">₱103</span>
                                </div>
                                <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                            </div>                          
                            <div class="m-0">
                                <span class="opennow">Popular</span>
                                <span class="discount">10% off</span>
                                <span class="newopen">New</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section id="category1" class="pt-3 mt-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h5 class="fw-bold m-0">Category 1</h5>
            <i class="fa-solid fa-pen rename" data-bs-toggle="modal" data-bs-target="#editcategory"></i>  
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="editproduct.php" class="card-link text-decoration-none">
                    <div class="card position-relative">
                        <img src="assets/images/example.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen" style="font-size: 18px;"></i></button>
                        <div class="card-body">
                            <p class="card-text text-muted m-0">Category</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Beef and cheese on a thin crust Pizza</p>
                            <div class="d-flex align-items-center justify-content-between my-3">
                                <div>
                                    <span class="proprice">₱103</span>
                                    <span class="pricebefore small">₱103</span>
                                </div>
                                <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                            </div>                          
                            <div class="m-0">
                                <span class="opennow">Popular</span>
                                <span class="discount">10% off</span>
                                <span class="newopen">New</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section id="category2" class="pt-3 mt-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h5 class="fw-bold m-0">Category 2</h5>
            <i class="fa-solid fa-pen rename" data-bs-toggle="modal" data-bs-target="#editcategory"></i>  
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="editproduct.php" class="card-link text-decoration-none">
                    <div class="card position-relative">
                        <img src="assets/images/example.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen" style="font-size: 18px;"></i></button>
                        <div class="card-body">
                            <p class="card-text text-muted m-0">Category</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Beef and cheese on a thin crust Pizza</p>
                            <div class="d-flex align-items-center justify-content-between my-3">
                                <div>
                                    <span class="proprice">₱103</span>
                                    <span class="pricebefore small">₱103</span>
                                </div>
                                <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                            </div>                          
                            <div class="m-0">
                                <span class="opennow">Popular</span>
                                <span class="discount">10% off</span>
                                <span class="newopen">New</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
    
    <section id="category3" class="pt-3 mt-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h5 class="fw-bold m-0">Category 3</h5>
            <i class="fa-solid fa-pen rename" data-bs-toggle="modal" data-bs-target="#editcategory"></i>  
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="editproduct.php" class="card-link text-decoration-none">
                    <div class="card position-relative">
                        <img src="assets/images/example.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen" style="font-size: 18px;"></i></button>
                        <div class="card-body">
                            <p class="card-text text-muted m-0">Category</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Beef and cheese on a thin crust Pizza</p>
                            <div class="d-flex align-items-center justify-content-between my-3">
                                <div>
                                    <span class="proprice">₱103</span>
                                    <span class="pricebefore small">₱103</span>
                                </div>
                                <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                            </div>                          
                            <div class="m-0">
                                <span class="opennow">Popular</span>
                                <span class="discount">10% off</span>
                                <span class="newopen">New</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
    <script src="assets/js/navigation.js?v=<?php echo time(); ?>"></script>

    <br><br>
</main>
<?php
    include_once './footer.php'; 
?>