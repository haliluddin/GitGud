
<?php
session_start();
include_once 'landingheader.php';
include_once 'links.php';
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/user.class.php';

$userObj = new User();
$adminObj = new Admin();
$isLoggedIn = false;
if (isset($_SESSION['user'])) {
    if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
        $isLoggedIn = true;
    } else {
        header('Location: email/verify_email.php');
        exit();
    }
}
date_default_timezone_set('Asia/Manila');
$currentDateTime = date("l, F j, Y h:i A");

$users = $adminObj->getUsers($searchTerm);

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    main{ padding: 20px 120px; }
    .salestable th{ padding-top: 10px; width: 10%; }
    .dropdown-menu-center { left: 50% !important; transform: translateX(-50%) !important; }
    .acchead a{ text-decoration: none; color: black; margin-bottom: 8px; }
</style>
<main>
    <div class="nav-container d-flex gap-3 my-2">
        <a href="#all" class="nav-link" data-target="all">Accounts</a>
        <a href="#applications" class="nav-link" data-target="applications">Applications</a>
        <a href="#reports" class="nav-link" data-target="reports">Reports</a>
        <a href="#onlinepayment" class="nav-link" data-target="onlinepayment">Online Payment</a>
    </div>

    <div id="applications" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Applications</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="#" method="get" class="searchmenu rounded-2">
                <input type="text" name="search" placeholder="Search account" style="width: 230px;" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="m-0 ms-2"><i class="fas fa-search fa-lg small"></i></button>
            </form>
        </div>
        <table class="salestable w-100 text-center border-top">
            <tr>
                <th style="width: 17%;">Owner</th>
                <th style="width: 18%;">Business Name</th>
                <th style="width: 25%;">Location</th>
                <th style="width: 10%;">Other Info</th>
                <th style="width: 10%;">Date Applied</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Action</th>
            </tr>

            <?php
                $getBusinesses = $adminObj->getBusinesses();
                foreach ($getBusinesses as $business) {
                    $businessStatus = htmlspecialchars($business['business_status']);
                    if ($businessStatus == 'Pending Approval') {
                        $statusDisplay = '<span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">Pending</span>';
                    } else if ($businessStatus == 'Approved') {
                        $statusDisplay = '<span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">Accepted</span>';
                    } else if ($businessStatus == 'Rejected') {
                        $statusDisplay = '<span class="small rounded-5 text-danger border border-danger p-1 border-2 fw-bold">Rejected</span>';
                    } else {
                        $statusDisplay = '<span class="small rounded-5 text-muted border border-muted p-1 border-2 fw-bold">' . $businessStatus . '</span>';
                    }

                    echo '<tr>';
                    echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['owner_name']) . '</td>';
                    echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['business_name']) . '</td>';
                    echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['region_province_city']) . ', ' . htmlspecialchars($business['barangay']) . ', ' . htmlspecialchars($business['street_building_house']) . '</td>';
                    // Other cells…
                    echo '<td class="fw-normal small py-3 px-4 status-cell">' . $statusDisplay . '</td>';
                    echo '<td class="fw-normal small py-3 px-4">';
                    echo '<div class="d-flex gap-2 justify-content-center">';
                    echo '<button class="approve-btn bg-success text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px">Approve</button>';
                    echo '<button class="deny-btn bg-danger text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px">Deny</button>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
            ?>
        </table>
        <!-- Approve/Deny Confirmation Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to <span id="actionText"></span> this application?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmAction">Yes, Proceed</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3">
            <!-- Pagination will be dynamically generated -->
        </div>
    </div>

    <!-- More Park Info -->
    <div class="modal fade" id="moreparkinfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold m-0">More Info</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <h5 class="fw-bold mb-3">Business Contact</h5>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Business Email</span>
                            <span data-email></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Business Phone Number</span>
                            <span data-phone class="text-muted"></span>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3">Business Logo</h5>
                    <div class="mb-4">
                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                        <a data-logo href="#" target="_blank"></a>
                    </div>

                    <h5 class="fw-bold mb-3">Operating Hours</h5>
                    <div class="mb-4" data-hours>
                        <!-- Dynamically added operating hours -->
                    </div>

                    <h5 class="fw-bold mb-3">Business Permit</h5>
                    <div class="mb-4">
                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                        <a data-permit href="#" target="_blank"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('moreparkinfo');

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            const email = button.getAttribute('data-email');
            const phone = button.getAttribute('data-phone');
            const hours = button.getAttribute('data-hours');
            const permit = button.getAttribute('data-permit'); 
            const logo = button.getAttribute('data-logo'); 

            modal.querySelector('.modal-body span[data-email]').textContent = email || 'N/A';
            modal.querySelector('.modal-body span[data-phone]').textContent = phone || 'N/A';

            const hoursContainer = modal.querySelector('.modal-body div[data-hours]');
            hoursContainer.innerHTML = hours 
                ? hours.split('; ').map(hour => `<p>${hour}</p>`).join('') 
                : '<p>No operating hours available</p>';

            const permitLink = modal.querySelector('.modal-body a[data-permit]');
            if (permit) {
                permitLink.textContent = permit.split('/').pop(); 
                permitLink.href = permit; 
                permitLink.target = '_blank'; 
            } else {
                permitLink.textContent = 'No permit file';
                permitLink.removeAttribute('href');
                permitLink.removeAttribute('target');
            }

            const logoLink = modal.querySelector('.modal-body a[data-logo]');
            if (logo) {
                logoLink.textContent = logo.split('/').pop(); 
                logoLink.href = logo; 
                logoLink.target = '_blank'; 
            } else {
                logoLink.textContent = 'No logo file';
                logoLink.removeAttribute('href');
                logoLink.removeAttribute('target');
            }
        });
    </script>

    <script src="assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/adminresponse.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/navigation.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/pagination.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/activate.js?v=<?php echo time(); ?>"></script>
    <br><br><br><br>
</main>
<?php
include_once 'footer.php';
?>
