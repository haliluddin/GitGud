<?php
    include_once 'header.php'; 
    include_once 'links.php'; 
    include_once 'modals.php'; 
    include_once 'nav.php'; 
?>
<style>
 
    main{
        padding: 20px 120px;
    }
</style>

<main>
    <div class="nav-container d-flex gap-3 my-2">
        <a href="#all" class="nav-link" data-target="all">My Favorite Stalls</a>
        <a href="#likeditems" class="nav-link" data-target="likeditems">My Favorite Foods</a>
    </div>

    <div id="all" class="section-content">
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="stall.php" class="card-link text-decoration-none bg-white">
                    <div class="card" style="position: relative;">
                        <img src="assets/images/stall1.jpg" class="card-img-top" alt="...">
                        <button class="add"><i class="fa-solid fa-heart"></i></button>
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center">
                            <p class="card-text text-muted m-0">Category</p>
                            <span class="dot text-muted"></span>
                            <p class="card-text text-muted m-0">Category</p>
                        </div>
                            <h5 class="card-title my-2">Food Stall Name</h5>
                            <div class="d-flex justify-content-between">
                                <p class="card-text text-muted m-0">Description</p>
                                <span style="color:#6A9C89;"><i class="fa-solid fa-heart"></i> 200</span>
                            </div>
                            <div class="mt-2">
                                <span class="opennow">Top Rated</span>
                                <span class="discount">With Promo</span>
                                <span class="newopen">New Open</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="stall.php" class="card-link text-decoration-none bg-white">
                    <div class="card" style="position: relative;">
                        <img src="assets/images/stall2.jpg" class="card-img-top" alt="...">
                        <button class="add"><i class="fa-solid fa-heart"></i></button>
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center">
                            <p class="card-text text-muted m-0">Category</p>
                            <span class="dot text-muted"></span>
                            <p class="card-text text-muted m-0">Category</p>
                        </div>
                            <h5 class="card-title my-2">Food Stall Name</h5>
                            <div class="d-flex justify-content-between">
                                <p class="card-text text-muted m-0">Description</p>
                                <span style="color:#6A9C89;"><i class="fa-solid fa-heart"></i> 200</span>
                            </div>
                            <div class="mt-2">
                                <span class="opennow">Top Rated</span>
                                <span class="discount">With Promo</span>
                                <span class="newopen">New Open</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="stall.php" class="card-link text-decoration-none bg-white">
                    <div class="card" style="position: relative;">
                        <img src="assets/images/stall3.jpg" class="card-img-top" alt="...">
                        <button class="add"><i class="fa-solid fa-heart"></i></button>
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center">
                            <p class="card-text text-muted m-0">Category</p>
                            <span class="dot text-muted"></span>
                            <p class="card-text text-muted m-0">Category</p>
                        </div>
                            <h5 class="card-title my-2">Food Stall Name</h5>
                            <div class="d-flex justify-content-between">
                                <p class="card-text text-muted m-0">Description</p>
                                <span style="color:#6A9C89;"><i class="fa-solid fa-heart"></i> 200</span>
                            </div>
                            <div class="mt-2">
                                <span class="opennow">Top Rated</span>
                                <span class="discount">With Promo</span>
                                <span class="newopen">New Open</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
    </div>

    <div id="likeditems" class="section-content d-none">
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <div class="col">
                <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                    <div class="card position-relative">
                        <img src="assets/images/example.jpg" class="card-img-top" alt="...">
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
        </div>
    </div>
 
    <br><br><br><br>

</main>
<script src="./assets/js/navigation.js?v=<?php echo time(); ?>"></script>

<?php
    include_once './footer.php'; 
?>