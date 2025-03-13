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
<div class="d-flex newban justify-content-between align-items-center">
    <div>
        <h1 class="mb-3">Freshly Added to Our Menu!</h1>
        <p class="m-0 mb-5">From snacks to full meals, taste the newest items added to our menu.</p>
        <div class="d-flex gap-3">
            <button class="disatc m-0 w-25">Popular</button>
            <button class="disatc m-0 w-25">Discounted</button>
        </div>
    </div>
    <img src="assets/images/file.png" alt="" width="550px" height="350px">
</div>
<br>
<main>
    <div class="row row-cols-1 row-cols-md-4 g-3">
        <div class="col">
            <a href="#" class="card-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#menumodal">
                <div class="card position-relative">
                    <img src="assets/images/food4.jpg" class="card-img-top" alt="...">
                    <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                    <div class="card-body">
                        <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                        <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                        <div class="d-flex align-items-center justify-content-between m-0">
                            <div>
                                <span class="proprice">₱103</span>
                                <span class="pricebefore small">₱103</span>
                            </div>
                            <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                        </div>                          
                        <div class="mt-3">
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
                    <img src="assets/images/example.jpg" class="card-img-top" alt="...">
                    <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                    <div class="card-body">
                        <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                        <h5 class="card-title my-2">Beef And Mushroom Pizza</h5>
                        <div class="d-flex align-items-center justify-content-between m-0">
                            <div>
                                <span class="proprice">₱103</span>
                                <span class="pricebefore small">₱103</span>
                            </div>
                            <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                        </div>                          
                        <div class="mt-3">
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
                        <div class="d-flex align-items-center justify-content-between m-0">
                            <div>
                                <span class="proprice">₱103</span>
                                <span class="pricebefore small">₱103</span>
                            </div>
                            <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                        </div>                          
                        <div class="mt-3">
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
                        <div class="d-flex align-items-center justify-content-between m-0">
                            <div>
                                <span class="proprice">₱103</span>
                                <span class="pricebefore small">₱103</span>
                            </div>
                            <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                        </div>                          
                        <div class="mt-3">
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
                        <div class="d-flex align-items-center justify-content-between m-0">
                            <div>
                                <span class="proprice">₱103</span>
                                <span class="pricebefore small">₱103</span>
                            </div>
                            <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                        </div>                          
                        <div class="mt-3">
                            <span class="opennow">Popular</span>
                            <span class="discount">10% off</span>
                            <span class="newopen">New</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <br><br><br><br><br><br>
</main> 

<script src="assets/js/filterstalls.js"></script>
<script src="./assets/js/navigation.js?v=<?php echo time(); ?>"></script>
<?php
    include_once 'footer.php'; 
?>
