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
    }
    .ranknumber{
        position: absolute;
        top: 0;
        left: 0;
        padding: 5px 10px;
        background-color: #CD5C08;
        color: white;
        font-weight: bold;
    }
    .fsname:hover{
        text-decoration: underline;
    }
</style>
<main>
    <div class="d-flex pagefilter gap-5 justify-content-center">
        <a href="#alltime" class="nav-link">All Time Favorites</a>
        <a href="#discount" class="nav-link">Discounted Picks</a>
        <a href="#new" class="nav-link">New & Trending</a>
    </div>
    <br>
    <section id="alltime">
        <h4 class="py-3 m-0">All Time Favorites</h4>
        <div class="inventory">
            <a href="#" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative" data-bs-toggle="modal" data-bs-target="#menumodal">
                <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                <h5 class="ranknumber">1</h5>
                <img src="assets/images/food1.jpg" class="h-100 rounded-start-2" width="150px">
                <div class="py-2 px-3 w-100">
                    <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                    <h5 class="card-title my-2" style="color: black;">Beef And Mushroom Pizza</h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="proprice">₱103</span>
                            <span class="pricebefore small">₱103</span>
                        </div>
                        <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                    </div>  
                    <div class="mt-2">
                        <span class="opennow">Popular</span>
                        <span class="discount">10% off</span>
                        <span class="newopen">New</span>
                    </div>
                </div>
            </a>
            <a href="#" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative" data-bs-toggle="modal" data-bs-target="#menumodal">
                <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>

                <h5 class="ranknumber">2</h5>
                <img src="assets/images/food2.jpg" class="rounded-start-2" width="150px">
                <div class="py-2 px-3 w-100">
                    <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                    <h5 class="card-title my-2" style="color: black;">Beef And Mushroom Pizza</h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="proprice">₱103</span>
                            <span class="pricebefore small">₱103</span>
                        </div>
                        <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                    </div>  
                    <div class="mt-2">
                        <span class="opennow">Popular</span>
                        <span class="discount">10% off</span>
                        <span class="newopen">New</span>
                    </div>
                </div>
            </a>

        </div>
    </section>
    <br><br>
    <section id="discount">
        <h4 class="py-3 m-0">Discounted Picks</h4>
        <div class="inventory">
            <a href="#" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative" data-bs-toggle="modal" data-bs-target="#menumodal">
                <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                <h5 class="ranknumber">1</h5>
                <img src="assets/images/food1.jpg" class="h-100 rounded-start-2" width="150px">
                <div class="py-2 px-3 w-100">
                    <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                    <h5 class="card-title my-2" style="color: black;">Beef And Mushroom Pizza</h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="proprice">₱103</span>
                            <span class="pricebefore small">₱103</span>
                        </div>
                        <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                    </div>  
                    <div class="mt-2">
                        <span class="opennow">Popular</span>
                        <span class="discount">10% off</span>
                        <span class="newopen">New</span>
                    </div>
                </div>
            </a>
            <a href="#" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative" data-bs-toggle="modal" data-bs-target="#menumodal">
                <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>

                <h5 class="ranknumber">2</h5>
                <img src="assets/images/food2.jpg" class="h-100 rounded-start-2" width="150px">
                <div class="py-2 px-3 w-100">
                    <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                    <h5 class="card-title my-2" style="color: black;">Beef And Mushroom Pizza</h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="proprice">₱103</span>
                            <span class="pricebefore small">₱103</span>
                        </div>
                        <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                    </div>  
                    <div class="mt-2">
                        <span class="opennow">Popular</span>
                        <span class="discount">10% off</span>
                        <span class="newopen">New</span>
                    </div>
                </div>
            </a>
            <a href="#" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative" data-bs-toggle="modal" data-bs-target="#menumodal">
                <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>

                <h5 class="ranknumber">3</h5>
                <img src="assets/images/food5.jpg" class="h-100 rounded-start-2" width="150px">
                <div class="py-2 px-3 w-100">
                    <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                    <h5 class="card-title my-2" style="color: black;">Beef And Mushroom Pizza</h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="proprice">₱103</span>
                            <span class="pricebefore small">₱103</span>
                        </div>
                        <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                    </div>  
                    <div class="mt-2">
                        <span class="opennow">Popular</span>
                        <span class="discount">10% off</span>
                        <span class="newopen">New</span>
                    </div>
                </div>
            </a>
            <a href="#" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative" data-bs-toggle="modal" data-bs-target="#menumodal">
                <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>

                <h5 class="ranknumber">4</h5>
                <img src="assets/images/food1.jpg" class="h-100 rounded-start-2" width="150px">
                <div class="py-2 px-3 w-100">
                    <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                    <h5 class="card-title my-2" style="color: black;">Beef And Mushroom Pizza</h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="proprice">₱103</span>
                            <span class="pricebefore small">₱103</span>
                        </div>
                        <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                    </div>  
                    <div class="mt-2">
                        <span class="opennow">Popular</span>
                        <span class="discount">10% off</span>
                        <span class="newopen">New</span>
                    </div>
                </div>
            </a>
        </div>
    </section>
    <br><br>
    <section id="new">
        <h4 class="py-3 m-0">New & Trending</h4>
        <div class="inventory">
            <a href="#" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative" data-bs-toggle="modal" data-bs-target="#menumodal">
                <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>
                <h5 class="ranknumber">1</h5>
                <img src="assets/images/food1.jpg" class="h-100 rounded-start-2" width="150px">
                <div class="py-2 px-3 w-100">
                    <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                    <h5 class="card-title my-2" style="color: black;">Beef And Mushroom Pizza</h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="proprice">₱103</span>
                            <span class="pricebefore small">₱103</span>
                        </div>
                        <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                    </div>  
                    <div class="mt-2">
                        <span class="opennow">Popular</span>
                        <span class="discount">10% off</span>
                        <span class="newopen">New</span>
                    </div>
                </div>
            </a>
            <a href="#" class="text-decoration-none bg-white d-flex align-items-center border rounded-2 position-relative" data-bs-toggle="modal" data-bs-target="#menumodal">
                <button class="addtocart position-absolute fw-bold d-flex justify-content-center align-items-center">+</button>

                <h5 class="ranknumber">2</h5>
                <img src="assets/images/food2.jpg" class="h-100 rounded-start-2" width="150px">
                <div class="py-2 px-3 w-100">
                    <p class="card-text text-muted m-0 fsname" onclick="window.location.href='stall.php';">Food Stall Name</p>
                    <h5 class="card-title my-2" style="color: black;">Beef And Mushroom Pizza</h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="proprice">₱103</span>
                            <span class="pricebefore small">₱103</span>
                        </div>
                        <span class="prolikes small"><i class="fa-solid fa-heart"></i> 189</span>
                    </div>  
                    <div class="mt-2">
                        <span class="opennow">Popular</span>
                        <span class="discount">10% off</span>
                        <span class="newopen">New</span>
                    </div>
                </div>
            </a>
        </div>
    </section>
    <br><br><br><br><br><br>
</main> 

<script src="assets/js/filterstalls.js"></script>
<script src="./assets/js/navigation.js?v=<?php echo time(); ?>"></script>
<?php
    include_once 'footer.php'; 
?>
