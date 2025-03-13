<?php 
    include_once 'links.php'; 
    include_once 'header.php';
    include_once 'modals.php';
?>
<style>
    /*
Color Palette:
#CD5C08
#FFF5E4
#C1D8C3
#6A9C89
*/
    main{
        background-color: white;
        padding: 0 120px;
    }
</style>
<div class="d-flex pagefilter gap-5 justify-content-center">
    <a href="#90off" class="nav-link">90% Off Deals</a>
    <a href="#50off" class="nav-link">50% Off Deals</a>
    <a href="#25off" class="nav-link">25% Off Deals</a>
</div>
<div class="dispro">
    <div class="d-flex align-items-center justify-content-between">
        <img src="assets/images/bg.png" width="200px" height="100px">
        <h1 class="m-0 fs-1 fw-bold">Don't Miss Out Limited Time Discount</h1>
        <img src="assets/images/bg.png" width="200px" height="100px">
    </div>
    <div class="tpdiv position-relative">
        <i class="fa-solid fa-arrow-left scroll-arrow left-arrow" style="display: none;"></i>
        <div class="d-flex rightfilter gap-3">
            <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                <div class="card position-relative" style="width: 320px;">
                    <img src="assets/images/food1.jpg" class="card-img-top" alt="...">
                    <div class="position-absolute disother">
                        <span class="opennow">Popular</span>
                        <span class="newopen">New</span>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                        <h5 class="card-title mt-2 mb-4">Beef And Mushroom Pizza</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="pricebefore small">₱103</span>
                                    <span style="color: #6A9C89;"><i class="fa-solid fa-tags"></i>10% off</span>
                                </div>
                                <h1 class="proprice fs-1 my-1">₱103</h1>                         
                                <span class="text-muted">Until March 20, 2024</span>
                            </div>
                            <button class="disatc m-0">ADD</button>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                <div class="card position-relative" style="width: 320px;">
                    <img src="assets/images/food2.jpg" class="card-img-top" alt="...">
                    <div class="position-absolute disother">
                        <span class="opennow">Popular</span>
                        <span class="newopen">New</span>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                        <h5 class="card-title mt-2 mb-4">Beef And Mushroom Pizza</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="pricebefore small">₱103</span>
                                    <span style="color: #6A9C89;"><i class="fa-solid fa-tags"></i>10% off</span>
                                </div>
                                <h1 class="proprice fs-1 my-1">₱103</h1>                         
                                <span class="text-muted">Until March 20, 2024</span>
                            </div>
                            <button class="disatc m-0">ADD</button>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                <div class="card position-relative" style="width: 320px;">
                    <img src="assets/images/food3.jpg" class="card-img-top" alt="...">
                    <div class="position-absolute disother">
                        <span class="opennow">Popular</span>
                        <span class="newopen">New</span>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                        <h5 class="card-title mt-2 mb-4">Beef And Mushroom Pizza</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="pricebefore small">₱103</span>
                                    <span style="color: #6A9C89;"><i class="fa-solid fa-tags"></i>10% off</span>
                                </div>
                                <h1 class="proprice fs-1 my-1">₱103</h1>                         
                                <span class="text-muted">Until March 20, 2024</span>
                            </div>
                            <button class="disatc m-0">ADD</button>
                        </div>
                    </div>
                </div>
            </a>
            <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                <div class="card position-relative" style="width: 320px;">
                    <img src="assets/images/food4.jpg" class="card-img-top" alt="...">
                    <div class="position-absolute disother">
                        <span class="opennow">Popular</span>
                        <span class="newopen">New</span>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                        <h5 class="card-title mt-2 mb-4">Beef And Mushroom Pizza</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="pricebefore small">₱103</span>
                                    <span style="color: #6A9C89;"><i class="fa-solid fa-tags"></i>10% off</span>
                                </div>
                                <h1 class="proprice fs-1 my-1">₱103</h1>                         
                                <span class="text-muted">Until March 20, 2024</span>
                            </div>
                            <button class="disatc m-0">ADD</button>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <i class="fa-solid fa-arrow-right scroll-arrow right-arrow"></i>
    </div>
</div>
<main>
    <section id="90off" class="p-0">
        <div class="d-flex justify-content-between align-items-center py-3">
            <h4 class="m-0">90% Off Deals</h4>
            <select class="form-select m-0" aria-label="Default select example" style="width: 150px;">
                <option selected value="popularity">by Popularity</option>
                <option value="date">by Date</option>
                <option value="likes">by Likes</option>
            </select>
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food1.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food2.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food3.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food5.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
    <br>
    <section id="50off" class="p-0">
        <div class="d-flex justify-content-between align-items-center py-3">
            <h4 class="m-0">50% Off Deals</h4>
            <select class="form-select m-0" aria-label="Default select example" style="width: 150px;">
                <option selected value="popularity">by Popularity</option>
                <option value="date">by Date</option>
                <option value="likes">by Likes</option>
            </select>
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food1.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food2.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food3.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food5.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
    <br>
    <section id="25off" class="p-0">
        <div class="d-flex justify-content-between align-items-center py-3">
            <h4 class="m-0">25% Off Deals</h4>
            <select class="form-select m-0" aria-label="Default select example" style="width: 150px;">
                <option selected value="popularity">by Popularity</option>
                <option value="date">by Date</option>
                <option value="likes">by Likes</option>
            </select>
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food1.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food2.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food3.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/food5.jpg" class="card-img-top" alt="...">
                        <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                         <div class="card-body">
                            <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                            <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                            <p class="card-text text-muted m-0">Until March 20, 2024</p>
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
    <br>
    
    <br><br><br><br><br><br>
</main> 

<script src="assets/js/filterstalls.js"></script>
<script src="./assets/js/navigation.js?v=<?php echo time(); ?>"></script>
<?php
    include_once 'footer.php'; 
?>
