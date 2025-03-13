<?php
    include_once 'links.php'; 
    include_once 'header.php';
?>
<style>
    main{
        padding: 20px 120px;
    }
   
</style>
<main>

    <form action="centralized.php"  method="POST">
        <input type="hidden" name="status" value="activated">
        <div class="progressbar">
            <div class="progress" id="progress"></div>
            <div class="progress-step progress-step-active" data-title="Information"></div>
            <div class="progress-step" data-title="Terms of Service"></div>
            <div class="progress-step" data-title="Complete"></div>
        </div>

        <div class="form-step form-step-active">
            <span class="fw-bold">Select Participating Stalls</span>
            <div class="d-flex mt-4 mb-5" style="gap: 120px;">
                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Stall1" id="stall1" checked>
                        <label class="form-check-label" for="stall1">Burger Hub</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Stall2" id="stall2" checked>
                        <label class="form-check-label" for="stall2">Pizza Haven</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Stall3" id="stall3" checked>
                        <label class="form-check-label" for="stall3">Sushi Corner</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Stall4" id="stall4" checked>
                        <label class="form-check-label" for="stall4">Grill Shack</label>
                    </div>
                </div>
                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Stall5" id="stall5" checked>
                        <label class="form-check-label" for="stall5">Coffee Bliss</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Stall6" id="stall6" checked>
                        <label class="form-check-label" for="stall6">Juice Bar</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Stall7" id="stall7" checked>
                        <label class="form-check-label" for="stall7">Ice Cream Delight</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="btns-group w-25">
                    <a href="centralized.php" class="button rounded-5">Cancel</a>
                    <a href="#" class="button btn-next rounded-5">Next</a>
                </div>
            </div>
        </div>

        <div class="form-step">
            <span class="fw-bold">Terms of Service</span>
            <div class="bg-white p-4 h-50 overflow-auto mt-2 mb-3 rounded-2">
                <span class="fw-bold">1. Overview</span>
                <p class="m-0 mt-3 mb-5">This document outlines the terms and conditions governing the use of the centralized cash payment system at [Food Park Name]. By using the system, you agree to these terms and conditions. If you do not agree, please refrain from using the System.</p>
                <span class="fw-bold">2. Centralized Payment System</span>
                <p class="m-0 mt-3 mb-5">The System enables customers to make payments for purchases from multiple stalls within the food park at a centralized cashier. This allows customers to combine orders from different stalls and pay for them in a single transaction using cash.</p>
                <span class="fw-bold">3. Payment Process</span>
                <ul class="mt-3 mb-5">
                    <li>Customers can place orders at different stalls, and payments for all orders must be made at the centralized cashier.</li>
                    <li>Once payment is completed, customers will receive a confirmation receipt that serves as proof of payment. The receipt must be presented to the respective stalls to receive the ordered items.</li>
                    <li>This System accepts cash payments only. Other payment methods (e.g., e-wallets) are not processed through this centralized system.</li>
                </ul>
            </div>
            <div class="form-check mb-5">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                <label class="form-check-label" for="flexCheckDefault">I have read and agree to the Terms of Service.</label>
            </div>
            <div class="d-flex justify-content-center">
                <div class="btns-group w-25">
                    <a href="#" class="button btn-prev rounded-5">Previous</a>
                    <a href="#" class="button btn-next rounded-5">Next</a>
                </div>
            </div>
        </div>

        <div class="form-step">
            <br>
            <div class="d-flex justify-content-center mb-5">
                <div class="text-center w-50">
                    <h5 class="fw-bold mb-3">Activation Completed</h5>
                    <p class="m-0">
                        Your centralized cash payment system has been successfully activated. You can now manage payments through a single cashier for all stalls. Remember to keep your cashier station staffed during operating hours to ensure smooth transactions.
                    </p>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <input type="submit" value="OK" class="button rounded-5" style="width: 100px;" />
            </div>
        </div>
    </form>
    <br><br><br><br>
    <script src="assets/js/cen.js?v=<?php echo time(); ?>"></script>
</main>
<?php
    include_once 'footer.php'; 
?>