<?php
    include_once 'links.php'; 
    include_once 'modals.php'; 
?>
<style>

/* #CD5C08
#FFF5E4
#C1D8C3
#6A9C89
*/
    .penpay, .paypro{
        height: calc(100vh - 65.61px); 
    }
    .centable th{
        border: 2px white solid;
        background-color: #C1D8C3;
        color: #6A9C89;
    }
    .centable td{
        border-bottom: 1px solid #ddd;
    }
    .cenorder:hover, .cenorder.active{
        border: 1px #CD5C08 solid !important;
        cursor: pointer;
    }
</style>
<main>
    <div class="bottom d-flex justify-content-between align-items-center">
        <a href="centralized.php"><img src="assets/images/logo.png" alt="GitGud"></a>
        <a href="cashcollected.php" class="text-decoration-none" style="color:#CD5C08;">Cash Collected <i class="fa-solid fa-arrow-right-long ms-2"></i></a>
    </div> 
    <div class="d-flex">
        <div class="p-4 overflow-auto w-25 penpay" style="background-color: #f4f4f4;">
            <h5 class="m-0 fw-bold my-3">Pending Payment</h5>
            <form action="#" method="get" class="searchmenu rounded-2 mb-2 bg-white py-2">
                <input type="text" name="search" placeholder="Search order" class="w-100">
                <button type="submit" class="m-0 ms-2"><i class="fas fa-search fa-lg small"></i></button>
            </form>
            <div class="d-flex align-items-center bg-white rounded-2 border mb-2 cenorder active">
                <div class="border-end p-3">
                    <span class="small text-muted">Order ID</span>
                    <h2 class="fw-bold m-0" style="color: #CD5C08;">0000</h2>
                </div>
                <div class="p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="small text-muted">Total Price:</span>
                        <h6 class="fw-bold m-0">₱103</h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Time Ordered:</span>
                        <h6 class="fw-bold m-0" style="color: #6A9C89;">01:00 PM</h6>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center bg-white rounded-2 border mb-2 cenorder">
                <div class="border-end p-3">
                    <span class="small text-muted">Order ID</span>
                    <h2 class="fw-bold m-0" style="color: #CD5C08;">0000</h2>
                </div>
                <div class="p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="small text-muted">Total Price:</span>
                        <h6 class="fw-bold m-0">₱103</h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Time Ordered:</span>
                        <h6 class="fw-bold m-0" style="color: #6A9C89;">01:00 PM</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-75 bg-white px-5 py-4 overflow-auto paypro">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h5 class="m-0 fw-bold">Payment Processing</h5>
                <div>
                    <span class="small text-muted">Order ID</span>
                    <h2 class="fw-bold m-0" style="color: #CD5C08;">0000</h2>
                </div>
            </div>
            <div class="border rounded-2 p-3 pb-0 mb-3">
                <h5 class="mb-2">Stall 1</h5>
                <table class="table table-borderless align-middle centable m-0">
                    <thead>
                        <tr>
                            <th class="text-center">Product</th>
                            <th class="text-center">Variations</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end p-3 pe-0 border-0">Total</td>
                            <td class="p-3 text-center border-0 fw-bold">₱66.55</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="border rounded-2 p-3 pb-0 mb-4">
                <h5 class="mb-2">Stall 1</h5>
                <table class="table table-borderless align-middle centable m-0">
                    <thead>
                        <tr>
                            <th class="text-center">Product</th>
                            <th class="text-center">Variations</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end p-3 pe-0 border-0">Total</td>
                            <td class="p-3 text-center border-0 fw-bold">₱66.55</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center mb-4">
                <div class="d-flex gap-5 align-items-center">
                    <span>Total Due</span>
                    <span style="color: #CD5C08;" class="fw-bold fs-5">₱112.00</span>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-5">
                <div>
                    <div class="d-flex align-items-center mb-4" style="gap: 100px">
                        <div class="d-flex gap-5">
                            <span>Total Discount</span>
                            <span>20.00</span>
                        </div>
                        <div class="d-flex gap-5 align-items-center">
                            <span>Amount Received</span>
                            <input type="text" placeholder="Enter here" class="px-2 py-1 m-0">
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 100px">
                        <div class="d-flex gap-5">
                            <span>Total Products</span>
                            <span>20.00</span>
                        </div>
                        <div class="d-flex gap-5 align-items-center">
                            <span>Change Due</span>
                            <span>20.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-5">
                <div class="d-flex gap-3 align-items-center">
                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#cancelorder">Cancel Order</button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmpayment">Confirm Payment</button>
                </div>
            </div>
        </div>
        <div class="w-75 bg-white px-5 py-4 overflow-auto paypro">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h5 class="m-0 fw-bold">Payment Processing</h5>
                <div>
                    <span class="small text-muted">Order ID</span>
                    <h2 class="fw-bold m-0" style="color: #CD5C08;">0000</h2>
                </div>
            </div>
            <div class="border rounded-2 p-3 pb-0 mb-3">
                <h5 class="mb-2">Stall 1</h5>
                <table class="table table-borderless align-middle centable m-0">
                    <thead>
                        <tr>
                            <th class="text-center">Product</th>
                            <th class="text-center">Variations</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end p-3 pe-0 border-0">Total</td>
                            <td class="p-3 text-center border-0 fw-bold">₱66.55</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="border rounded-2 p-3 pb-0 mb-4">
                <h5 class="mb-2">Stall 1</h5>
                <table class="table table-borderless align-middle centable m-0">
                    <thead>
                        <tr>
                            <th class="text-center">Product</th>
                            <th class="text-center">Variations</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                        <tr>
                            <td class="text-center p-3">Milk Shake</td>
                            <td class="text-center p-3">Chocolate, Medium</td>
                            <td class="text-center p-3">3</td>
                            <td class="text-center p-3">₱112.00</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end p-3 pe-0 border-0">Total</td>
                            <td class="p-3 text-center border-0 fw-bold">₱66.55</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center mb-4">
                <div class="d-flex gap-5 align-items-center">
                    <span>Total Due</span>
                    <span style="color: #CD5C08;" class="fw-bold fs-5">₱112.00</span>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-5">
                <div>
                    <div class="d-flex align-items-center mb-4" style="gap: 100px">
                        <div class="d-flex gap-5">
                            <span>Total Discount</span>
                            <span>20.00</span>
                        </div>
                        <div class="d-flex gap-5 align-items-center">
                            <span>Amount Received</span>
                            <input type="text" placeholder="Enter here" class="px-2 py-1 m-0">
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 100px">
                        <div class="d-flex gap-5">
                            <span>Total Products</span>
                            <span>20.00</span>
                        </div>
                        <div class="d-flex gap-5 align-items-center">
                            <span>Change Due</span>
                            <span>20.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-5">
                <div class="d-flex gap-3 align-items-center">
                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#cancelorder">Cancel Order</button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmpayment">>Confirm Payment</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Initially hide all .paypro sections except the first one
        $(".paypro").hide().first().show();

        // Add a click event to .cenorder elements
        $(".cenorder").on("click", function () {
            // Remove the 'active' class from all .cenorder elements
            $(".cenorder").removeClass("active");
            
            // Add the 'active' class to the clicked .cenorder
            $(this).addClass("active");
            
            // Get the index of the clicked .cenorder
            const index = $(".cenorder").index(this);

            // Hide all .paypro sections
            $(".paypro").hide();

            // Show the corresponding .paypro section
            $(".paypro").eq(index).show();
        });
    });
</script>

</main>

