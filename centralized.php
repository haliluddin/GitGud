<?php
include_once 'header.php';
include_once 'links.php'; 
include_once 'modals.php'; 
include_once 'nav.php'; 

// Check the status from the form
$status = isset($_POST['status']) && $_POST['status'] === 'activated';
?>
<style>
    main {
        padding: 20px 120px;
    }
    .cenact {
        color: #CD5C08;
        border: 1px #CD5C08 solid;
    }
</style>
<main>
    <br>
    <?php if ($status): ?>
        <!-- IF ACTIVATED -->
        <div id="activated">
            <button class="addpro" onclick="window.location.href='centralizedcash.php';">
                <i class="fa-regular fa-hand-pointer me-2"></i>Centralized Cash Payment
            </button>
            
            <div class="mt-5">
                <span class="fw-bold">What is Centralized Cash Payment?</span>
                <p class="m-0 mt-3 mb-5">Centralized cash payment allows customers to make a single cash transaction for orders from multiple stalls at the food park. Instead of visiting each stall to pay, they can settle everything at one central cashier.</p>
                <span class="fw-bold">Why Activate?</span>
                <ul class="mt-3 mb-5">
                    <li>Reduce long queues at individual stalls, as customers pay once.</li>
                    <li>Simplify the checkout experience for customers, making their visit more enjoyable.</li>
                    <li>Focus more on preparing orders and less on handling payments.</li>
                </ul>
            </div>
            <div class="bg-white rounded-2 border p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-2">Manage Cashier</h5>
                        <div class="d-flex gap-4 align-items-center border rounded-2 p-1 mt-1">
                            <span class="small text-muted ms-2">Share link to the cashier</span>
                            <i class="fa-regular fa-copy text-dark fs-6 me-2" style="cursor: pointer;"></i>
                        </div>
                    </div>
                    <button class="disatc m-0 small" data-bs-toggle="modal" data-bs-target="#addcashier">+ Add Cashier</button>
                </div>
                <table class="salestable w-100 text-center border-top mt-3">
                    <tr>
                        <th class="pt-2 px-3 text-start">Name</th>
                        <th class="pt-2 px-3">Shift Start Time</th>
                        <th class="pt-2 px-3">Shift End Time</th>
                        <th class="pt-2 px-3">Status</th>
                        <th class="pt-2 px-3">Total Transaction Processed</th>
                        <th class="pt-2 px-3">Total Amount Collected</th>
                        <th class="pt-2 px-3">Action</th>
                    </tr>
                    <tr>
                        <td class="p-3 text-start">Naila Haliluddin</td>
                        <td class="p-3">7:00 AM</td>
                        <td class="p-3">1:00 PM</td>
                        <td class="p-3">Online</td>
                        <td class="p-3">30</td>
                        <td class="p-3">₱12,500.00</td>
                        <td class="fw-normal py-3 tabact">
                            <i class="fa-solid fa-pen-to-square me-2 p-1 small rounded-1" data-bs-toggle="modal" data-bs-target="#editcashier"></i>
                            <i class="fa-solid fa-trash p-1 small rounded-1" data-bs-toggle="modal" data-bs-target="#deletecashier"></i>
                        </td>   
                    </tr>
                    <tr>
                        <td class="p-3 text-start">Naila Haliluddin</td>
                        <td class="p-3">7:00 AM</td>
                        <td class="p-3">1:00 PM</td>
                        <td class="p-3">Online</td>
                        <td class="p-3">30</td>
                        <td class="p-3">₱12,500.00</td>
                        <td class="fw-normal py-3 tabact">
                            <i class="fa-solid fa-pen-to-square me-2 p-1 small rounded-1" data-bs-toggle="modal" data-bs-target="#editcashier"></i>
                            <i class="fa-solid fa-trash p-1 small rounded-1" data-bs-toggle="modal" data-bs-target="#deletecashier"></i>
                        </td>                    
                    </tr>
                </table>
            </div>
        </div>
    <?php else: ?>
        <!-- IF NOT ACTIVATED -->
        <div id="notactivated">
            <div class="cenact d-flex justify-content-between align-items-center bg-white p-4 mb-5">
                <div style="width: 70%;">
                    <h4 class="fw-bold">Centralized Cash Payment</h4>
                    <p class="m-0">Enhance your customer's experience with a smoother, faster payment process. Give them the convenience of paying for all their orders in one go.</p>
                </div>
                <button class="addpro px-5" onclick="window.location.href='cenactivate.php';">Activate</button>
            </div>
            <div>
                <span class="fw-bold">What is Centralized Cash Payment?</span>
                <p class="m-0 mt-3 mb-5">Centralized cash payment allows customers to make a single cash transaction for orders from multiple stalls at the food park. Instead of visiting each stall to pay, they can settle everything at one central cashier.</p>
                <span class="fw-bold">Why Activate?</span>
                <ul class="mt-3 mb-5">
                    <li>Reduce long queues at individual stalls, as customers pay once.</li>
                    <li>Simplify the checkout experience for customers, making their visit more enjoyable.</li>
                    <li>Focus more on preparing orders and less on handling payments.</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <br><br><br><br>
</main>
<?php include_once 'footer.php'; ?>
