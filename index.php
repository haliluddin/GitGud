<?php
    session_start();
    
    //include_once 'header.php';
    include_once 'landingheader.php';
    include_once 'links.php'; 
    include_once 'modals.php';
    require_once __DIR__ . '/classes/db.class.php';
    require_once __DIR__ . '/classes/park.class.php';
    require_once __DIR__ . '/classes/encdec.class.php';

    $userObj = new User();
    $parkObj = new Park();
    $isLoggedIn = false;
    
    if (isset($_SESSION['user'])) {
        if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
            $isLoggedIn = true;
        } else {
            header('Location: email/verify_email.php');
            exit();
        }
    }
?>
<title>GitGud PMS</title>
<style>
    .lpseemore{
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

<section class="second">
    <div class="secondinside">
        <img src="assets/images/owner.jpg">
        <div>
            <h1>Promote Your Food Park with Us!</h1>
            <p>
                Looking to attract more customers to your food park? We've got you covered!<br><br>
                We'll list your stalls' menus online and simplify the ordering process, helping you reach hungry customers quickly. From street food to local favorites, we'll boost your park's visibility.<br><br>
                Ready to grow your audience? Let's partner today!
            </p>
            <?php if ($isLoggedIn) { ?>
                <button onclick="window.location.href='parkregistration.php'">Get Started</button>
            <?php } else { ?>
                <button onclick="window.location.href='signup.php'">Get Started</button>
            <?php } ?>
        </div>
    </div>
</section>

<section class="third">
    <br><br><br>
    <h2>All Food Parks in Zamboanga City</h2><br>
    
    <div class="row row-cols-1 row-cols-md-4 g-3">
        <?php 
            $parks = $parkObj->getParks();
            date_default_timezone_set('Asia/Manila'); 
            $currentDay = date('l'); 
            $currentTime = date('H:i');
            foreach ($parks as $park) { 
                if ($park['business_status'] != 'Reject' && $park['business_status'] != 'Pending Approval') {
                    //$uniqueLink = "./park.php?id=" . $park['url'];
                    
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

                    $statusClass = $isOpen ? 'opennow' : 'cnow';
                    $statusText = $isOpen ? 'Open Now' : 'Closed';
                    ?>
                    <div class="col">
                        <div class="card">
                            <a href="enter_park.php?id=<?= encrypt($park['id']) ?>" class="card-link text-decoration-none">
                                <img src="<?= $park['business_logo'] ?>" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title text-dark"><?= $park['business_name'] ?></h5>
                                    <p class="card-text text-muted"><i class="fa-solid fa-location-dot me-2"></i><?= $park['street_building_house'] ?>, <?= $park['barangay'] ?>, Zamboanga City</p>
                                    <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                </div>
                            </a>
                            <div class="text-center p-2 lpseemore rounded-4 mx-3 mb-3 small" data-bs-toggle="modal" data-bs-target="#seemorepark"
                            data-email="<?= htmlspecialchars($park['business_email']) ?>"
                            data-phone="<?= htmlspecialchars($park['business_phone']) ?>"
                            data-hours="<?= htmlspecialchars($park['operating_hours']) ?>">See more...</div>
                        </div>
                    </div>
            <?php 
                }
            }
            ?>
    </div>
    <br><br><br>
</section>
<?php
    include_once 'footer.php'; 
?>

<!-- See more food park -->
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
                    <!-- Dynamically added operating hours -->
                </div>

                <button class="border-0 py-2 px-3 rounded-5 me-2"><i class="fa-regular fa-copy me-2 fs-5"></i>Share Link</button>
                <button class="border-0 py-2 px-3 rounded-5" data-bs-toggle="modal" data-bs-target="#report"><i class="fa-regular fa-flag me-2 fs-5"></i>Report</button>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('seemorepark');

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // Get data attributes
        const email = button.getAttribute('data-email');
        const phone = button.getAttribute('data-phone');
        const hours = button.getAttribute('data-hours');

        // Populate modal fields
        modal.querySelector('.modal-body span[data-email]').textContent = email || 'N/A';
        modal.querySelector('.modal-body span[data-phone]').textContent = phone || 'N/A';

        // Populate operating hours
        const hoursContainer = modal.querySelector('.modal-body div[data-hours]');
        hoursContainer.innerHTML = hours
            ? hours.split('; ').map(hour => `<p>${hour}</p>`).join('')
            : '<p>No operating hours available</p>';
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

    // Handle selection from dropdown
    $(document).on("click", ".search-item", function() {
        window.location.href = $(this).data("url");
    });
});
</script>
