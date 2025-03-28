
<?php
session_start();
include_once 'landingheader.php';
include_once 'links.php';
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/user.class.php';
require_once './email/verification_token.class.php';

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
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    main{ padding: 20px 120px; }
    .salestable th{ padding-top: 10px; width: 10%; }
    .dropdown-menu-center { left: 50% !important; transform: translateX(-50%) !important; }
    .acchead a{ text-decoration: none; color: black; margin-bottom: 8px; }
    button:disabled { background-color: #D3d3d3 !important; }
</style>
<main>
    <div class="nav-container d-flex gap-3 my-2">
        <a href="#all" class="nav-link" data-target="all">Accounts</a>
        <a href="#applications" class="nav-link" data-target="applications">Applications</a>
        <a href="#reports" class="nav-link" data-target="reports">Reports</a>
    </div>

    <!-- Applications Section -->
    <div id="applications" class="w-100 border rounded-2 p-3 bg-white section-content">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold mb-2">Applications</h5>
                <span class="small"><?= $currentDateTime ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center text-muted small gap-4 mt-2 mb-3">
            <form action="#" method="get" class="searchmenu rounded-2">
                <input type="text" name="search_application" placeholder="Search application" style="width: 230px;" value="<?= htmlspecialchars($searchTerm) ?>">
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
            <tbody id="applicationsTableBody">
                <?php
                $getBusinesses = $adminObj->getBusinesses();
                if ($getBusinesses) {
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
                        echo '<td class="fw-normal small py-3 px-4">
                            <i class="fa-solid fa-chevron-down rename small" 
                                data-bs-toggle="modal" 
                                data-bs-target="#moreparkinfo" 
                                data-email="' . htmlspecialchars($business['business_email']) . '"
                                data-phone="' . htmlspecialchars($business['business_phone']) . '"
                                data-hours="' . htmlspecialchars($business['operating_hours']) . '"
                                data-permit="' . htmlspecialchars($business['business_permit']) . '"
                                data-logo="' . htmlspecialchars($business['business_logo']) . '">
                            </i>
                        </td>';
                        echo '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($business['created_at']) . '</td>';
                        echo '<td class="fw-normal small py-3 px-4 status-cell">' . $statusDisplay . '</td>';
                        echo '<td class="fw-normal small py-3 px-4">';
                        echo '<div class="d-flex gap-2 justify-content-center">';
                        $disabled = ($businessStatus == 'Approved' || $businessStatus == 'Rejected') ? 'disabled' : '';
                        echo '<button class="approve-btn bg-success text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px" ' . $disabled . '>Approve</button>';
                        echo '<button class="deny-btn bg-danger text-white border-0 small py-1 rounded-1" data-id="' . htmlspecialchars($business['id']) . '" style="width:60px" ' . $disabled . '>Deny</button>';
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center py-5">No result found</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3"></div>
    </div>
    
    <!-- Approval Confirmation Modal (for Approve action) -->
    <div class="modal fade" id="approvalConfirmModal" tabindex="-1" aria-labelledby="approvalConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="approvalConfirmModalLabel">Confirm Action</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Are you sure you want to <span id="approvalActionText"></span> this application?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmApproval">Yes, Proceed</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Rejection Reason Modal (for Deny action) -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" role="dialog" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="rejectReasonModalLabel">Select Rejection Reason</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p>Select the eligibility criteria that were not met:</p>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="reasonName">
            <label class="form-check-label" for="reasonName">Name</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="reasonEmail">
            <label class="form-check-label" for="reasonEmail">Email</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="reasonPhone">
            <label class="form-check-label" for="reasonPhone">Phone</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="reasonLogo">
            <label class="form-check-label" for="reasonLogo">Logo</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="reasonHours">
            <label class="form-check-label" for="reasonHours">Operating Hours</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="reasonBarangay">
            <label class="form-check-label" for="reasonBarangay">Barangay</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="reasonStreet">
            <label class="form-check-label" for="reasonStreet">Street, Building, House</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="reasonPermit">
            <label class="form-check-label" for="reasonPermit">Permit</label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveRejection">Save changes</button>
        </div>
        </div>
    </div>
    </div>

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
