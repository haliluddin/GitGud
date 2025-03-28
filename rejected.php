<?php
session_start();
include_once 'links.php'; 
include_once 'secondheader.php';
require_once __DIR__ . '/classes/db.class.php';

$userObj = new User();
$rejection_reason = '';

if (isset($_SESSION['user']['id'])) {
    $owner_id = $_SESSION['user']['id'];
    $rejection_reason = $userObj->getRejectionReason($owner_id);
}
?>
<style>
   main {
        display: flex;
        height: calc(100vh - 65.61px); 
        background-color: white;
    }
</style>
<main>
    <div style="background-color: #f4f4f4" class="w-50 d-flex justify-content-center align-items-center text-center">
        <div>
            <img src="assets/images/rejected.png" height="300" width="300">
            <h3 class="fw-bold mb-3">Your food park registration has been declined</h3>
            <i>Reason: Did not meet eligibility criteria for <span class="fw-bold"><?php echo htmlspecialchars($rejection_reason); ?></span></i>
        </div>
    </div>
    <div class="w-50 d-flex justify-content-center align-items-center text-center" style="padding: 100px;">
        <div>
            <h5 class="lh-3">
                Please review your application against our eligibility criteria and update your details accordingly. 
                You may reapply once the necessary adjustments have been made.
            </h5>
            <button class="btn btn-danger rounded-5 py-3 px-5 mt-5" 
                    onclick="window.location.href='parkregistration.php?reapply=1';">
                Register again
            </button>
        </div>
    </div>
</main>
