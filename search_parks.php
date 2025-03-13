<?php
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/park.class.php';
require_once __DIR__ . '/classes/encdec.class.php';

$parkObj = new Park();

if (isset($_POST['search'])) {
    $query = $_POST['search'];
    $parks = $parkObj->searchParks($query); 

    if (!empty($parks)) {
        foreach ($parks as $park) {
            ?>
            <div class="search-item" data-url="enter_park.php?id=<?= encrypt($park['id']) ?>">
                <img src="<?= $park['business_logo'] ?>" class="search-logo">
                <div class="search-info">
                    <p class="search-name"><?= htmlspecialchars($park['business_name']) ?></p>
                    <p class="search-location"><?= htmlspecialchars($park['street_building_house']) ?>, <?= htmlspecialchars($park['barangay']) ?>, Zamboanga City</p>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p class='no-results'>No food parks found.</p>";
    }
}
?>
