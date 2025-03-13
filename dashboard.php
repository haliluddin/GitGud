<?php
    include_once 'header.php'; 
    include_once 'links.php'; 
    include_once 'nav.php';
    include_once 'modals.php'; 

    $park = $parkObj->getPark($park_id);
    $parkOwner = $parkObj->getParkOwner($park_id);
?>
<style>
    main{
        padding: 20px 120px;
    }
</style>
<main>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="fw-bold m-0">Hello, <?= $parkOwner['owner_name'] ?>!</h2>
        <div class="py-2 px-3 rounded-2 border bg-white w-25 d-flex align-items-center justify-content-between" data-bs-toggle="offcanvas" data-bs-target="#foodparkbranch" aria-controls="foodparkbranch" style="cursor: pointer;">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= $park['business_logo'] ?>" width="50px" height="50px" class="rounded-5">
                <div>
                    <p class="m-0 fw-bold"><?= $park['business_name'] ?></p>
                    <span class="text-muted small"><?= $park['street_building_house'] ?>, <?= $park['barangay'] ?>, Zamboanga City</span>
                </div>
            </div>
            <i class="fa-solid fa-angle-down"></i>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="foodparkbranch" aria-labelledby="foodparkbranchLabel" style="width: 40%;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="foodparkbranchLabel">Manage Food Park</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="text-center mb-4 border-bottom pb-3">
                <div class="profile-picture" data-bs-toggle="modal" data-bs-target="#editfoodpark">
                    <img src="<?= $park['business_logo'] ?>" alt="Profile Picture" class="profile-img rounded-5">
                    <div class="camera-overlay">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                </div>
                <h4 class="fw-bold m-0 mb-1 mt-3"><?= $park['business_name'] ?></h4>
                <span class="text-muted mb-1"><?= $park['street_building_house'] ?>, <?= $park['barangay'] ?>, Zamboanga City, Philippines</span>
                <div class="d-flex gap-2 text-muted align-items-center justify-content-center mb-1">
                    <span><i class="fa-solid fa-envelope"></i> <?= $park['business_email'] ?></span>
                    <span class="dot"></span>
                    <span><i class="fa-solid fa-phone small"></i> +63<?= $park['business_phone'] ?></span>
                </div>
                <button class="variation-btn addrem m-2" data-bs-toggle="modal" data-bs-target="#editfoodpark">Edit Park</button>
                <button class="variation-btn addrem" data-bs-toggle="modal" data-bs-target="#deletepark">Delete Park</button>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1">All Branch</h5>
                    <span class="small text-muted">Currently selected 1/2 branch</span>
                </div>
                <button class="disatc m-0 small" onclick="window.location.href='parkregistration.php';">+ New Branch</button>
            </div>
            <div class="d-flex justify-content-between align-items-center border rounded-2 py-2 px-3 mt-2 selectbranch">
                <div class="d-flex gap-3 align-items-center">
                    <img src="assets/images/foodpark.jpg" width="50px" height="50px" style="border-radius: 50%;">
                    <div class="">
                        <h6>Food Park Name</h6>
                        <p class="text-muted m-0" style="font-size: 12px;">Food Park Location</p>
                        <span class="text-muted" style="font-size: 12px;">10 food stalls</span>
                    </div>
                </div>
                <i class="fa-solid fa-check text-success fw-bold fs-5"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center border rounded-2 py-2 px-3 mt-2 selectbranch">
                <div class="d-flex gap-3 align-items-center">
                    <img src="assets/images/foodpark.jpg" width="50px" height="50px" style="border-radius: 50%;">
                    <div class="">
                        <h6>Food Park Name</h6>
                        <p class="text-muted m-0" style="font-size: 12px;">Food Park Location</p>
                        <span class="text-muted" style="font-size: 12px;">10 food stalls</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3 mt-3">
        <div class="p-3 rounded-2 border bg-white w-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <p class="m-0">Total Food Stalls</p>
                    <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                </div>
                <i class="fa-solid fa-parachute-box dashicon fs-5"></i>
            </div>
            <div class="d-flex align-items-end justify-content-between">
                <h2 class="fw-bold m-0">10</h2>
                <div class="d-flex align-items-center small text-danger gap-1">
                    <i class="fa-solid fa-arrow-down"></i>
                    <span class="text-danger">11%</span>
                </div>
            </div>
        </div>
        <div class="p-3 rounded-2 border bg-white w-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <p class="m-0">Total Sales</p>
                    <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                </div>
                <i class="fa-solid fa-sack-dollar dashicon fs-5"></i>
            </div>
            <div class="d-flex align-items-end justify-content-between">
                <h2 class="fw-bold m-0">₱1,000</h2>
                <div class="d-flex align-items-center small text-success gap-1">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span class="text-success">11%</span>
                </div>
            </div>
        </div>
        <div class="p-3 rounded-2 border bg-white w-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <p class="m-0">Total Transaction</p>
                    <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                </div>
                <i class="fa-solid fa-radiation dashicon fs-5"></i>
            </div>
            <div class="d-flex align-items-end justify-content-between">
                <h2 class="fw-bold m-0">20</h2>
                <div class="d-flex align-items-center small text-success gap-1">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span class="text-success">11%</span>
                </div>
            </div>
        </div>
        <div class="p-3 rounded-2 border bg-white w-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <p class="m-0">Total Visit</p>
                    <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                </div>
                <i class="fa-solid fa-eye dashicon fs-5"></i>
            </div>
            <div class="d-flex align-items-end justify-content-between">
                <h2 class="fw-bold m-0">10</h2>
                <div class="d-flex align-items-center small text-success gap-1">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span class="text-success">11%</span>
                </div>
            </div>
        </div>
        <div class="p-3 rounded-2 border bg-white w-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <p class="m-0">Total Orders</p>
                    <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                </div>
                <i class="fa-solid fa-burger dashicon fs-5"></i>
            </div>
            <div class="d-flex align-items-end justify-content-between">
                <h2 class="fw-bold m-0">10</h2>
                <div class="d-flex align-items-center small text-success gap-1">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span class="text-success">11%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-3 my-3">
        <div class="w-75"> 
            <div class="p-4 rounded-2 border bg-white mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="m-0 fw-bold">Sales by Month</h5>
                    <i class="fa-solid fa-ellipsis"></i>
                </div>
                <div class="mt-3">
                    <canvas id="salesChart" width="100" height="40"></canvas>
                </div>
            </div>
            <div class="p-4 rounded-2 border bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="m-0 fw-bold">Visits by Month</h5>
                    <i class="fa-solid fa-ellipsis"></i>
                </div>
                <div class="mt-3">
                    <canvas id="visitsChart" width="100" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="p-4 rounded-2 border bg-white w-25">
            <h5 class="m-0 fw-bold mb-1">Stall Performance</h5>
            <span class="small text-muted">We found some ongoing issues for your food stall</span>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall1.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">1</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall2.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">2</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall3.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">3</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall4.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">4</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall5.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">5</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall1.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">6</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall1.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">7</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall2.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">8</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall3.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">9</h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall3.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <h5 class="fw-bold" style="color: #CD5C08;">10</h5>
            </div>
        </div>
    </div>
    
    <div class="d-flex gap-3 my-3">
        <div class="p-4 rounded-2 border bg-white w-75">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="m-0 fw-bold">Orders by Month</h5>
                <i class="fa-solid fa-ellipsis"></i>
            </div>
            <div class="mt-3">
                <canvas id="ordersChart" width="100" height="40"></canvas>
            </div>
        </div>
        <div class="bg-white border p-4 w-25 rounded-2">
            <h5 class="m-0 fw-bold mb-1">Live Ops Monitor</h5>
            <span class="small text-muted">We found some ongoing issues for your food stall</span>
            <div class="d-flex align-items-center border rounded-2 py-2 my-2">
                <h4 class="text-danger px-3 m-0 fw-bold">2</h4>
                <div>
                    <p class="m-0 fw-bold">Canceled Orders</p>
                    <span class="text-muted small">Today</span>
                </div>
                <i class="fa-solid fa-angle-right ms-auto me-3"></i> <!-- The ms-auto class pushes the icon to the right -->
            </div>
            <div class="d-flex align-items-center border rounded-2 py-2 mb-2">
                <h4 class="text-success px-3 m-0 fw-bold">3</h4>
                <div>
                    <p class="m-0 fw-bold">Likes</p>
                    <span class="text-muted small">Today</span>
                </div>
                <i class="fa-solid fa-angle-right ms-auto me-3"></i> <!-- The ms-auto class pushes the icon to the right -->
            </div>
            <div class="d-flex align-items-center border rounded-2 py-2 mb-2">
                <h4 class="text-success px-3 m-0 fw-bold">3</h4>
                <div>
                    <p class="m-0 fw-bold">New Customers</p>
                    <span class="text-muted small">Today</span>
                </div>
                <i class="fa-solid fa-angle-right ms-auto me-3"></i> <!-- The ms-auto class pushes the icon to the right -->
            </div>
            <div class="d-flex align-items-center border rounded-2 py-2">
                <h4 class="text-success px-3 m-0 fw-bold">3</h4>
                <div>
                    <p class="m-0 fw-bold">Repeated Customers</p>
                    <span class="text-muted small">Today</span>
                </div>
                <i class="fa-solid fa-angle-right ms-auto me-3"></i> <!-- The ms-auto class pushes the icon to the right -->
            </div>
        </div>
    </div>

    <div class="d-flex gap-3 my-3">
        <div class="w-75 border rounded-2 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0 fw-bold">Transaction History</h5>
                <div class="d-flex align-items-center text-muted small gap-4">
                    <select name="sortOptions" id="sortOptions" class="border-0 text-muted small py-1 px-2">
                        <option value="all">All Transaction</option>
                    </select>
                    <i class="fa-regular fa-circle-down rename"></i>
                    <div class="d-flex gap-2 align-items-center small rename py-1 px-2">
                        <span style="cursor: context-menu;">47s</span>
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                    </div>
                    <i class="fa-solid fa-magnifying-glass rename"></i>
                </div>
            </div>
            <table class="salestable w-100 text-center border-top rounded-2">
                <tr>
                    <th class="pt-2">Food Stall</th>
                    <th class="pt-2">Date</th>
                    <th class="pt-2">Amount Paid</th>
                    <th class="pt-2">Period Cover</th>
                    <th class="pt-2">Payment Method</th>
                    <th class="pt-2">Action</th>
                </tr>
                <tr>
                    <td class="fw-normal py-3">Food Stall Name</td>
                    <td class="fw-normal py-3">07/29/2024 22:59</td>
                    <td class="fw-normal py-3">₱100</td>
                    <td class="fw-normal py-3">30 Days</td>
                    <td class="fw-normal py-3">Cash</td>
                    <td class="fw-normal py-3 tabact">
                        <i class="fa-solid fa-pen-to-square me-2 p-1 small rounded-1" data-bs-toggle="modal" data-bs-target="#editpayment"></i>
                        <i class="fa-solid fa-trash p-1 small rounded-1" onclick="if (confirm('Are you sure you want to delete this payment?')) deletePayment();"></i>
                    </td>
                </tr>
                <tr>
                    <td class="fw-normal py-3">Food Stall Name</td>
                    <td class="fw-normal py-3">07/29/2024 22:59</td>
                    <td class="fw-normal py-3">₱100</td>
                    <td class="fw-normal py-3">30 Days</td>
                    <td class="fw-normal py-3">Cash</td>
                    <td class="fw-normal py-3 tabact">
                        <i class="fa-solid fa-pen-to-square me-2 p-1 small rounded-1" data-bs-toggle="modal" data-bs-target="#editpayment"></i>
                        <i class="fa-solid fa-trash p-1 small rounded-1" onclick="if (confirm('Are you sure you want to delete this payment?')) deletePayment();"></i>
                    </td>
                </tr>
                <tr>
                    <td class="fw-normal py-3">Food Stall Name</td>
                    <td class="fw-normal py-3">07/29/2024 22:59</td>
                    <td class="fw-normal py-3">₱100</td>
                    <td class="fw-normal py-3">30 Days</td>
                    <td class="fw-normal py-3">Cash</td>
                    <td class="fw-normal py-3 tabact">
                        <i class="fa-solid fa-pen-to-square me-2 p-1 small rounded-1" data-bs-toggle="modal" data-bs-target="#editpayment"></i>
                        <i class="fa-solid fa-trash p-1 small rounded-1" onclick="if (confirm('Are you sure you want to delete this payment?')) deletePayment();"></i>
                    </td>
                </tr>
                
            </table>
        </div>
        <div class="p-4 rounded-2 border bg-white w-25">
            <h5 class="m-0 fw-bold mb-1">Overdue Payment</h5>
            <span class="small text-muted">We found some ongoing issues for your food stall</span>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall1.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <i class="fa-solid fa-angle-right"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall2.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <i class="fa-solid fa-angle-right"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex gap-2 align-items-center">
                    <img src="assets/images/stall3.jpg" width="50" height="50" class="rounded-5">
                    <div>
                        <p class="m-0 fw-bold">Food Stall Name</p>
                        <span class="small text-muted">Stall Owner Name</span>
                    </div>
                </div>
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </div>
    </div>
    <br><br><br><br>
</main>
<script src="assets/js/dashboardchart.js?v=<?php echo time(); ?>"></script>
<script src="./assets/js/sales.js?v=<?php echo time(); ?>"></script>

<?php
    include_once './footer.php'; 
?>