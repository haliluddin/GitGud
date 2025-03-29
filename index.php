<?php
session_start();
include_once 'landingheader.php';
include_once 'links.php';
include_once 'modals.php';
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/park.class.php';
require_once __DIR__ . '/classes/encdec.class.php';
require_once __DIR__ . '/classes/user.class.php';

$userObj = new User();
$parkObj = new Park();
$isLoggedIn = false;
if (isset($_SESSION['user'])) {
    $user = $userObj->getUser($_SESSION['user']['id']);
    if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
        $isLoggedIn = true;
    } else {
        echo '<script> window.location.href = "email/verify_email.php" </script>';
        exit();
    }
} else {
    $user = ['role' => 'Guest'];
}

if (isset($_POST['report_submit'])) {
    if (isset($_SESSION['user'])) {
        $reported_by  = $_SESSION['user']['id'];
        $reported_park = $_POST['reported_park']; 
        $reason       = $_POST['reason'];
        if ($userObj->reportFoodPark($reported_by, $reported_park, $reason)) {
            echo "<script>alert('Report submitted successfully.');</script>";
        } else {
            echo "<script>alert('Error submitting report.');</script>";
        }
    } else {
        echo "<script>alert('You must be logged in to report.');</script>";
    }
}


date_default_timezone_set('Asia/Manila');
$currentDateTime = date("l, F j, Y h:i A");
$currentDay = date('l'); 
$currentTime = date('H:i');

function getNextOpening($operatingHoursArray) {
    if (!empty($operatingHoursArray)) {
        $first = $operatingHoursArray[0];
        if (strpos($first, '<br>') !== false) {
            list($days, $timeRange) = explode('<br>', $first);
            $daysArray = array_map('trim', explode(',', $days));
            $day = !empty($daysArray) ? $daysArray[0] : 'Unknown';
            list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
            return $day . ' ' . date('g:i A', strtotime($openTime));
        }
    }
    return "N/A";
}

$parks = $parkObj->getParks();
$validParks = array_filter($parks, function($park) {
    return $park['business_status'] === 'Approved';
});

?>
<title>GitGud PMS</title>
<style>
.lpseemore {
    background-color: #e5e5e5;
    cursor: pointer;
}
#searchResults {
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    max-height: 250px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
    border-radius: 10px;
}
.search-item {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
    width: 100% !important;
}
.search-item:hover {
    background: #f5f5f5;
}
.search-logo {
    width: 60px !important;
    height: 60px !important;
    border-radius: 50%;
    margin-right: 15px;
}
.search-info {
}
.search-name {
    margin: 0 !important;
    margin-bottom: 7px;
}
.search-location {
    margin: 0 !important;
    font-size: small !important;
    color: gray;
}
.no-results {
    padding: 10px;
    color: gray;
    text-align: center;
}
.closed {
    z-index: 2;
}
</style>
<section class="first">
    <br>
    <div class="firstinside">
        <div>
            <h1>Bringing taste and community together</h1>
            <p>Experience the flavor of connection at your local Food Park</p>
            <form action="" method="post">
                <input type="text" id="searchInput" placeholder="Search Food Park" autocomplete="off">
                <button type="submit"><i class="fas fa-search fa-lg"></i></button>
            </form>
            <div id="searchResults"></div>
        </div>
        <img src="assets/images/first.png">
    </div>
    <br>
</section>

<?php if (!($isLoggedIn && ($user['role'] == 'Admin' || $user['role'] == 'Stall Owner'))): ?>
<section class="second">
    <div class="secondinside">
        <img src="assets/images/owner.jpg" alt="Food Park Owner">
        <div>
            <h1>Promote Your Food Park with Us!</h1>
            <p>
                Looking to attract more customers to your food park? We've got you covered!<br><br>
                We'll list your stalls' menus online and simplify the ordering process, helping you reach hungry customers quickly. From street food to local favorites, we'll boost your park's visibility.<br><br>
                Ready to grow your audience? Let's partner today!
            </p>
            <?php 
            if ($isLoggedIn && ($user['role'] != 'Admin' && $user['role'] != 'Stall Owner')) {
                $status = $userObj->getBusinessStatus($user['id']);
                if ($status == 'Pending Approval') {
                    $url = 'pendingapproval.php';
                } else if ($status == 'Rejected') {
                    $url = 'rejected.php';
                } else {
                    $url = 'parkregistration.php';
                }
                echo '<button onclick="window.location.href=\'' . $url . '\'">Get Started</button>';
            } elseif (!$isLoggedIn) { 
                echo '<button onclick="window.location.href=\'signup.php\'">Get Started</button>';
            } 
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="third">
    <br><br><br>
    <h2>All Food Parks in Zamboanga City</h2>

    <div class="oc mt-4 mb-5"> 
        <button id="openBtn" class="btn btn-outline-secondary">Open</button>
        <button id="closedBtn" class="btn btn-outline-secondary">Closed</button>
        <button id="unavailableBtn" class="btn btn-outline-secondary">Unavailable</button>
    </div>
    
    <?php 
    if (empty($validParks)) { 
        echo "<p class='text-center my-5'>No food parks available at this time.</p>";
    } else { 
    ?>
        <div class="row row-cols-1 row-cols-md-4 g-3">
        <?php 
            foreach ($validParks as $park) { 
                $isOpen = false;
                $operatingHours = explode('; ', $park['operating_hours']);
                foreach ($operatingHours as $hours) {
                    list($days, $timeRange) = explode('<br>', $hours);
                    $daysArray = array_map('trim', explode(',', $days));
                    if (in_array($currentDay, $daysArray)) {
                        list($openTime, $closeTime) = array_map('trim', explode(' - ', $timeRange));
                        $openTime24 = date('H:i', strtotime($openTime));
                        $closeTime24 = date('H:i', strtotime($closeTime));
                        if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                            $isOpen = true;
                            break;
                        }
                    }
                }
                if (isset($park['status']) && $park['status'] === 'Unavailable') {
                    $status = 'unavailable';
                } else {
                    $status = $isOpen ? 'open' : 'closed';
                }
                ?>
                <div class="col park-card border rounded p-0 mx-2" data-status="<?= $status; ?>">
                    <a href="enter_park.php?id=<?= urlencode(encrypt($park['id'])) ?>" class="card-link text-decoration-none">
                        <div class="card border-0" style="position: relative;">
                            <?php 
                            // Display overlay based on status
                            if ($status === 'closed') { 
                                $closedMessage = getNextOpening($operatingHours);
                            ?>
                                <div class="closed text-center">
                                    <div>
                                        <span>Closed until <?= $closedMessage ?></span>
                                        <button class="rounded bg-white small border-0 px-3 py-1 mt-2" style="color:#CD5C08;">Order for later</button>
                                    </div>
                                </div>
                            <?php } elseif ($status === 'unavailable') { ?>
                                <div class="closed text-center">
                                    <span>Unavailable</span>
                                </div>
                            <?php } ?>
                            <img src="<?= $park['business_logo'] ?>" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><?= $park['business_name'] ?></h5>
                                <p class="card-text text-muted">
                                    <i class="fa-solid fa-location-dot me-1"></i>
                                    <?= $park['street_building_house'] ?>, <?= $park['barangay'] ?>, Zamboanga City
                                </p>
                            </div>
                        </div>
                    </a>
                    <div class="text-center p-2 lpseemore rounded-4 mx-3 mb-3 small" 
                        data-bs-toggle="modal" 
                        data-bs-target="#seemorepark" 
                        data-email="<?= htmlspecialchars($park['business_email']) ?>" 
                        data-phone="<?= htmlspecialchars($park['business_phone']) ?>" 
                        data-hours="<?= htmlspecialchars($park['operating_hours']) ?>" 
                        data-reported_park="<?= htmlspecialchars($park['id']) ?>">See more...</div>
                    </div>
            <?php 
            }
            ?>

        </div>
    <?php 
    }
    ?>
    <br><br><br><br><br>
</section>
<?php
include_once 'footer.php';
?>
<div class="modal fade" id="seemorepark" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <span data-phone></span>
                    </div>
                </div>
                <h5 class="fw-bold mb-3">Operating Hours</h5>
                <div class="mb-4" data-hours>
                </div>
                <?php if ($isLoggedIn && $user['role'] == 'Customer'): ?>
                    <button class="border-0 py-2 px-3 rounded-5" data-bs-toggle="modal" data-bs-target="#report" data-reported_user=""> <i class="fa-regular fa-flag me-2 fs-5"></i>Report</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="report" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form method="POST" action="">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <div class="d-flex justify-content-end">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="text-center">
              <h4 class="fw-bold mb-4">Why are you reporting this?</h4>
              <div class="form-floating m-0">
                  <textarea class="form-control" name="reason" placeholder="Reason" id="reason" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                  <label for="reason">Reason</label>
              </div>
              <input type="hidden" name="reported_park" id="reported_park" value="">
              <div class="mt-4 mb-3">
                  <input type="submit" name="report_submit" value="Submit" class="button" />
              </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        const openBtn = document.getElementById('openBtn');
        const closedBtn = document.getElementById('closedBtn');
        const unavailableBtn = document.getElementById('unavailableBtn');
        const parkCards = document.querySelectorAll('.park-card');

        function filterParks(status) {
            parkCards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                if(status === 'all'){
                    card.style.display = '';
                } else {
                    card.style.display = (cardStatus === status) ? '' : 'none';
                }
            });
        }

        openBtn.addEventListener('click', function(){
            if(openBtn.classList.contains('active')){
                openBtn.classList.remove('active');
                filterParks('all');
            } else {
                openBtn.classList.add('active');
                closedBtn.classList.remove('active');
                unavailableBtn.classList.remove('active');
                filterParks('open');
            }
        });

        closedBtn.addEventListener('click', function(){
            if(closedBtn.classList.contains('active')){
                closedBtn.classList.remove('active');
                filterParks('all');
            } else {
                closedBtn.classList.add('active');
                openBtn.classList.remove('active');
                unavailableBtn.classList.remove('active');
                filterParks('closed');
            }
        });

        unavailableBtn.addEventListener('click', function(){
            if(unavailableBtn.classList.contains('active')){
                unavailableBtn.classList.remove('active');
                filterParks('all');
            } else {
                unavailableBtn.classList.add('active');
                openBtn.classList.remove('active');
                closedBtn.classList.remove('active');
                filterParks('unavailable');
            }
        });
    });

</script>

<script>
const modal = document.getElementById('seemorepark');
modal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const email = button.getAttribute('data-email');
    const phone = button.getAttribute('data-phone');
    const hours = button.getAttribute('data-hours');
    const reportedPark = button.getAttribute('data-reported_park'); // change here
    modal.querySelector('.modal-body span[data-email]').textContent = email || 'N/A';
    modal.querySelector('.modal-body span[data-phone]').textContent = phone || 'N/A';
    const hoursContainer = modal.querySelector('.modal-body div[data-hours]');
    hoursContainer.innerHTML = hours ? hours.split('; ').map(hour => "<p>" + hour + "</p>").join('') : '<p>No operating hours available</p>';
    const reportButton = modal.querySelector('button[data-bs-target="#report"]');
    if(reportButton) {
        reportButton.setAttribute('data-reported_park', reportedPark); // update attribute here
    }
});

const reportModal = document.getElementById('report');
reportModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const reportedPark = button.getAttribute('data-reported_park'); // change here
    document.getElementById('reported_park').value = reportedPark ? reportedPark : '';
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $("#searchInput").keyup(function() {
        let query = $(this).val();
        if (query.length > 0) {
            $.ajax({
                url: "search_parks.php",
                method: "POST",
                data: { search: query },
                success: function(data) {
                    $("#searchResults").html(data).show();
                }
            });
        } else {
            $("#searchResults").hide();
        }
    });
    $(document).on("click", ".search-item", function() {
        window.location.href = $(this).data("url");
    });
});
</script>
