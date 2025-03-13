<?php 
    include_once 'links.php'; 
    include_once 'header.php'; 
    include_once 'modals.php'; 
?>
<style>
/* #CD5C08
#FFF5E4
#C1D8C3
#6A9C89
*/
    main{
        padding: 20px 120px;
    }
</style>
<main>
    <div class="d-flex justify-content-end">
        <button class="addpro mb-3 prev" onclick="window.history.back();"><i class="fa-solid fa-chevron-left me-2"></i> Previous</button>
    </div>
    <div class="d-flex gap-3">
        <div class="card h-100" style="width: 33%;">
            <div class="position-relative">
                <img src="assets/images/stall2.jpg" class="card-img-top" alt="...">
                <div class="position-absolute rentstatus pending"><i class="fa-solid fa-hourglass-half"></i> Pending: Rent payment is due in 3 days</div>
                <div class="position-absolute d-flex gap-2 smaction">
                    <i class="fa-solid fa-pen-to-square" onclick="window.location.href='editpage.php';"></i>
                    <i class="fa-solid fa-trash-can" data-bs-toggle="modal" data-bs-target="#deletestall"></i>
                </div>
            </div>
            <div class="card-body px-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="d-flex gap-2 align-items-center">
                            <p class="card-text text-muted m-0">Category</p>
                            <span class="dot text-muted"></span>
                            <p class="card-text text-muted m-0">Category</p>
                        </div>
                        <h5 class="card-title my-2 fw-bold">Food Stall Name</h5>
                        <p class="card-text text-muted m-0">Description</p>
                    </div>
                    <div class="smclose">
                        <i class="fa-solid fa-door-closed"></i>
                        <span>CLOSE</span>
                    </div>
                </div>
                <div class="stats py-2 mt-3 mb-2 d-flex justify-content-between align-items-center">
                    <div class="text-center">
                        <div class="d-flex gap-1 align-items-center m-0">
                            <i class="fa-regular fa-heart fs-5"></i>
                            <span class="fw-bold fs-4">56</span>
                        </div>
                        <p class="m-0 small">Likes</p>
                    </div>
                    <div class="text-center">
                        <div class="d-flex gap-1 align-items-center m-0">
                            <i class="fa-regular fa-lemon fs-5"></i>
                            <span class="fw-bold fs-4">668</span>
                        </div>
                        <p class="m-0 small">Orders</p>
                    </div>
                    <div class="text-center">
                        <div class="d-flex gap-1 align-items-center m-0">
                            <i class="fa-regular fa-user fs-5"></i>
                            <span class="fw-bold fs-4">565</span>
                        </div>
                        <p class="m-0 small">Visits</p>
                    </div>
                    <div class="text-center">
                        <div class="d-flex gap-1 align-items-center m-0">
                            <i class="fa-solid fa-peseta-sign fs-5"></i>
                            <span class="fw-bold fs-4">56</span>
                        </div>
                        <p class="m-0 small">AOV</p>
                    </div>
                </div>

                <div class="accordion accordion-flush" id="accCol2">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#col2flu1" aria-expanded="false" aria-controls="col2flu1">Contact information</button>
                        </h2>
                        <div id="col2flu1" class="accordion-collapse collapse" data-bs-parent="#accCol2">
                            <div class="accordion-body p-0 mb-3 small">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Business Email</span>
                                    <span>example@gmail.com</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Business Phone Number</span>
                                    <span class="text-muted">+639123456789</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#col2flu2" aria-expanded="false" aria-controls="col2flu2">Opening Hours</button>
                        </h2>
                        <div id="col2flu2" class="accordion-collapse collapse" data-bs-parent="#accCol2">
                            <div class="accordion-body p-0 mb-3 small">
                                <div class="mb-2">
                                    <p class="mb-1">Monday, Tuesday, Thursday</p>
                                    <span>7AM - 7PM</span>
                                </div>
                                <div class="">
                                    <p class="mb-1">Wednesday, Friday, Saturday</p>
                                    <span>8AM - 9PM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#col2flu3" aria-expanded="false" aria-controls="col2flu3">Payment Method</button>
                        </h2>
                        <div id="col2flu3" class="accordion-collapse collapse" data-bs-parent="#accCol2">
                            <div class="accordion-body p-0 mb-3 small">
                                <ul>
                                    <li class="mb-2">Cash</li>
                                    <li>GCash</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="owner mt-1 py-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="assets/images/user.jpg" alt="">
                        <div>
                            <span class="fw-bold">Naila Haliluddin</span>
                            <p class="m-0">example@gmail.com</p>
                        </div>
                    </div>
                    <i class="text-muted">Owner</i>
                </div>

            </div> 
        </div> 
        <div class="bg-white border rounded-2 p-4" style="width: 67%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2 align-items-center">
                    <h5 class="m-0 fw-bold">Rent Information</h5>
                    <span class="small text-muted">(Nov 19, 2024)</span>
                </div>
                <span class="small py-1 px-2 rounded-5 salesdr" style="color: #CD5C08;" data-bs-toggle="modal" data-bs-target="#addpayment">+ Add New Payment</span>
            </div>
            <div class="d-flex gap-3">
                <div class="w-50 border p-3 rounded-2">
                    <span class="text-muted">Current Rent Details</span>
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
                        <div class="d-flex gap-2 align-items-end">
                            <h3 class="fw-bold m-0">₱100</h3>
                            <span class="text-muted">for 30 Days</span>
                        </div>
                        <div class="border border-success px-2 text-success small rounded-2">Paid</div>
                    </div>
                    <div class="custom-progress-container mb-1">
                        <div class="custom-progress">
                            <div class="custom-progress-bar" style="width: 25%;"></div>
                        </div>
                        <span class="custom-progress-label">25%</span>
                    </div>
                    <span class="small text-muted">24 days before due date</span>
                </div>

                <div class="p-3 rounded-2 w-25 border">
                    <span class="text-muted">Total Amount Paid</span>
                    <div class="d-flex align-items-center gap-2 my-2">
                        <h4 class="fw-bold m-0">₱100</h4>
                        <i class="fa-solid fa-check text-success small fw-bold"></i>
                    </div>
                    <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                    <div class="d-flex gap-1 line-dot mt-4">
                        <div class="w-100 line rounded-5"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>

                <div class="p-3 rounded-2 w-25 border">
                    <span class="text-muted">Total Transaction</span>
                    <div class="d-flex align-items-center gap-2 my-2">
                        <h4 class="fw-bold m-0">2</h4>
                        <i class="fa-solid fa-check text-success small fw-bold"></i>
                    </div>
                    <span class="small" style="color: #ccc;">Jan 04, 2025</span>
                    <div class="d-flex gap-1 line-dot mt-4">
                        <div class="w-100 line rounded-5"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>
            </div>
            <div class="w-100 border rounded-2 p-3 mt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Transaction History</span>
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
                        <th class="pt-2">Date</th>
                        <th class="pt-2">Amount Paid</th>
                        <th class="pt-2">Period Cover</th>
                        <th class="pt-2">Payment Method</th>
                        <th class="pt-2">Action</th>
                    </tr>
                    <tr>
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
        </div>
    </div>
    <br><br><br><br><br>
</main>

<?php 
    include_once 'footer.php'; 
?>