<?php
    include_once 'links.php'; 
    include_once 'modals.php'; 
?>
<style>
    main{
        padding: 20px 120px;
    }
    .cashcollected{
        display: grid;
        grid-template-areas: 'a b c d';
        gap: 15px;
    }
    .cencheck:hover, .fa-solid.text-success:hover{
        transform: scale(1.2);
    }
    .cencheck, .fa-solid.text-success {
        cursor: pointer;
        transition: color 0.3s, transform 0.3s;
    }
        .
/* #CD5C08
#FFF5E4
#C1D8C3
#6A9C89
*/
   
</style>
<div class="bottom d-flex justify-content-between align-items-center">
    <a href="centralized.php"><img src="assets/images/logo.png" alt="GitGud"></a>
    <a href="centralizedcash.php" class="text-decoration-none" style="color:#CD5C08;"><i class="fa-solid fa-arrow-left-long me-2"></i> Go Back</a>
</div> 
<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="border rounded-2 p-3 w-25 bg-white">
            <span>Total Cash Received</span>
            <h3 class="m-0 mt-2 fw-bold">₱12,500.00</h3>
        </div>
        <div>

        </div>
    </div>
    
    <div class="cashcollected">
        <div class="border rounded-2 bg-white">
            <h6 class="m-0 border-bottom p-3 text-center">Stall Name 1</h6>
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    <span class="small text-muted">Cash Received</span>
                    <h3 class="m-0 my-2 fw-bold">₱12,500.00</h3>
                    <span>Total Orders: 34</span>
                </div>
                <i class="fa-regular fa-circle-check fs-2 cencheck" onclick="toggleCheck(this)"></i>

            </div>
        </div>
        <div class="border rounded-2 bg-white">
            <h6 class="m-0 border-bottom p-3 text-center">Stall Name 1</h6>
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    <span class="small text-muted">Cash Received</span>
                    <h3 class="m-0 my-2 fw-bold">₱12,500.00</h3>
                    <span>Total Orders: 34</span>
                </div>
                <i class="fa-regular fa-circle-check fs-2 cencheck" onclick="toggleCheck(this)"></i>

            </div>
        </div>
        <div class="border rounded-2 bg-white">
            <h6 class="m-0 border-bottom p-3 text-center">Stall Name 1</h6>
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    <span class="small text-muted">Cash Received</span>
                    <h3 class="m-0 my-2 fw-bold">₱12,500.00</h3>
                    <span>Total Orders: 34</span>
                </div>
                <i class="fa-regular fa-circle-check fs-2 cencheck" onclick="toggleCheck(this)"></i>

            </div>
        </div>
        <div class="border rounded-2 bg-white">
            <h6 class="m-0 border-bottom p-3 text-center">Stall Name 1</h6>
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    <span class="small text-muted">Cash Received</span>
                    <h3 class="m-0 my-2 fw-bold">₱12,500.00</h3>
                    <span>Total Orders: 34</span>
                </div>
                <i class="fa-regular fa-circle-check fs-2 cencheck" onclick="toggleCheck(this)"></i>

            </div>
        </div>
    </div>
    <div class="w-100 border rounded-2 p-4 mt-3 bg-white">
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
                <th class="pt-2">Date & Time</th>
                <th class="pt-2">Order ID</th>
                <th class="pt-2">Stalls</th>
                <th class="pt-2">Total Paid</th>
                <th class="pt-2">Status</th>
                <th class="pt-2">Action</th>
            </tr>
            <tr>
                <td class="fw-normal py-3">2024-10-06 14:30:00</td>
                <td class="fw-normal py-3">0000</td>
                <td class="fw-normal py-3">Stall Name 1, Stall Name 2, Stall Name 3,</td>
                <td class="fw-normal py-3">₱120</td>
                <td class="fw-normal py-3">Completed</td>
                <td class="fw-normal py-3"><i class="fa-solid fa-trash rename small"></i></td>
            </tr>
            <tr>
                <td class="fw-normal py-3">2024-10-06 14:30:00</td>
                <td class="fw-normal py-3">0000</td>
                <td class="fw-normal py-3">Stall Name 1, Stall Name 2, Stall Name 3,</td>
                <td class="fw-normal py-3">₱120</td>
                <td class="fw-normal py-3">Completed</td>
                <td class="fw-normal py-3"><i class="fa-solid fa-trash rename small"></i></td>
            </tr>
        </table>
    </div>
    <br><br><br><br>
    <script>
        function toggleCheck(element) {
            if (element.classList.contains('fa-regular')) {
                element.classList.remove('fa-regular', 'cencheck');
                element.classList.add('fa-solid', 'text-success');
            } else {
                element.classList.remove('fa-solid', 'text-success');
                element.classList.add('fa-regular', 'cencheck');
            }
        }

    </script>
</main>

