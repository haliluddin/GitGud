<?php
session_start();
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/park.class.php';
require_once __DIR__ . '/classes/encdec.class.php';
require_once __DIR__ . '/classes/user.class.php';

$parkObj = new Park();
$userObj = new User();

// User permission checks
$user = null;
if (isset($_SESSION['user'])) {
    $user = $userObj->getUser($_SESSION['user']['id']);
} else {
    $user = ['role' => 'Guest'];
}

// Set timezone for operating hours check
date_default_timezone_set('Asia/Manila');
$currentDay = date('l'); 
$currentTime = date('H:i');

if (isset($_POST['search'])) {
    $query = $_POST['search'];
    $parks = $parkObj->searchParks($query); 

    if (!empty($parks)) {
        foreach ($parks as $park) {
            // Check operating hours to determine if park is open
            $isOpen = false;
            
            // Default to open if no operating hours are specified
            if (!isset($park['operating_hours']) || empty($park['operating_hours'])) {
                $isOpen = true; // If no hours specified, assume it's open
            } else {
                $operatingHours = explode('; ', $park['operating_hours']);
                
                // If there are no operating hours entries after splitting, assume open
                if (empty($operatingHours)) {
                    $isOpen = true;
                } else {
                    // Check each operating hours entry
                    foreach ($operatingHours as $hours) {
                        // Handle entries with day specifications
                        if (strpos($hours, '<br>') !== false) {
                            $hoursParts = explode('<br>', $hours);
                            if (count($hoursParts) >= 2) {
                                $days = $hoursParts[0];
                                $timeRange = $hoursParts[1];
                                
                                // Check if today is in the allowed days
                                $daysArray = array_map('trim', explode(',', $days));
                                if (!in_array($currentDay, $daysArray)) {
                                    continue; // Skip if today is not in the allowed days
                                }
                            } else {
                                continue; // Skip if format is incorrect
                            }
                        } else {
                            // If no day specified, assume it applies to all days
                            $timeRange = $hours;
                        }
                        
                        // Parse the time range
                        $timeParts = explode(' - ', $timeRange);
                        if (count($timeParts) < 2) {
                            continue; // Skip if time format is incorrect
                        }
                        
                        // Get opening and closing times
                        list($openTime, $closeTime) = array_map('trim', $timeParts);
                        
                        // Convert to 24-hour format for comparison
                        $openTime24 = date('H:i', strtotime($openTime));
                        $closeTime24 = date('H:i', strtotime($closeTime));
                        
                        // Check if current time falls within operating hours
                        if ($closeTime24 <= $openTime24) {
                            // Overnight window (e.g., 10:00 PM - 2:00 AM)
                            if ($currentTime >= $openTime24 || $currentTime <= $closeTime24) {
                                $isOpen = true;
                                break;
                            }
                        } else {
                            // Same-day window (e.g., 8:00 AM - 5:00 PM)
                            if ($currentTime >= $openTime24 && $currentTime <= $closeTime24) {
                                $isOpen = true;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Set status based on park status and operating hours
            if (isset($park['status']) && $park['status'] === 'Unavailable') {
                $status = 'unavailable';
            } else {
                // If park is approved and has operating hours that include current time, mark as open
                $status = $isOpen ? 'open' : 'closed';
            }
            
            // Check if park has stalls
            $setStatus = $parkObj->getParkStalls($park['id']);
            if (empty($setStatus)) {
                $status = 'unavailable';
            }
            
            // Check permissions
            $canEnter = false;
            if ($user['role'] === 'Admin') {
                $canEnter = true;
            } elseif (isset($user['id']) && $user['id'] == $park['user_id']) {
                $canEnter = true;
            } elseif (isset($user['id']) && $parkObj->isStallOwnerOfPark($user['id'], $park['id'])) {
                $canEnter = true;
            } elseif ($status !== 'unavailable') {
                $canEnter = true;
            }
            
            // Generate the URL only if the user can enter
            $parkUrl = $canEnter ? "enter_park.php?id=" . urlencode(encrypt($park['id'])) : "javascript:void(0)";
            ?>
            <div class="search-item" data-status="<?= $status ?>" data-url="<?= $parkUrl ?>">
                <img src="<?= $park['business_logo'] ?>" class="search-logo">
                <div class="search-info">
                    <p class="search-name"><?= htmlspecialchars($park['business_name']) ?></p>
                    <p class="search-location"><?= htmlspecialchars($park['street_building_house']) ?>, <?= htmlspecialchars($park['barangay']) ?>, Zamboanga City</p>
                    <?php if ($status === 'closed' || $status === 'unavailable') { ?>
                        <span class="search-status"><?= ucfirst($status) ?></span>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p class='no-results'>No food parks found.</p>";
    }
}
?>
