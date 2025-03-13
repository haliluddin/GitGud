<?php 
include_once 'header.php';
include_once 'links.php';
include_once 'modals.php';
include_once 'nav.php';
require_once __DIR__ . '/classes/stall.class.php';

$stallObj   = new Stall();
$stall_id   = $stallObj->getStallId($_SESSION['user']['id']);
$stallCreated = $stallObj->getStallCreationDate($stall_id);
$createdDate  = date("Y-m-d", strtotime($stallCreated));
$currentDate  = date("Y-m-d");

$daysOld = (strtotime($currentDate) - strtotime($createdDate))/(60*60*24);

$todayStart = $currentDate . " 00:00:00";
$todayEnd   = $currentDate . " 23:59:59";

$yesterdayDate = date("Y-m-d", strtotime("-1 day", strtotime($currentDate)));
$yesterdayStart = $yesterdayDate . " 00:00:00";
$yesterdayEnd   = $yesterdayDate . " 23:59:59";

$sevenAvailable = $daysOld >= 7;
$sevenStart = date("Y-m-d", strtotime($createdDate));
$sevenEnd   = date("Y-m-d", strtotime($createdDate." +6 days"));

$thirtyAvailable = $daysOld >= 30;
$thirtyStart = date("Y-m-d", strtotime($createdDate));
$thirtyEnd   = date("Y-m-d", strtotime($createdDate." +29 days"));

$yearAvailable = $daysOld >= 365;
$yearStart = date("Y-m-d", strtotime($createdDate));
$yearEnd   = date("Y-m-d", strtotime($createdDate." +364 days"));

$timePeriods = [
    'today' => [
        'label'   => 'Today',
        'display' => date("M d, Y", strtotime($currentDate)),
        'start'   => $todayStart,
        'end'     => $todayEnd,
        'sales'   => $stallObj->getSalesToday($stall_id, $todayStart, $todayEnd)
    ]
];
if($daysOld >= 1) {
    $timePeriods['yesterday'] = [
        'label'   => 'Yesterday',
        'display' => date("M d, Y", strtotime($yesterdayDate)),
        'start'   => $yesterdayStart,
        'end'     => $yesterdayEnd,
        'sales'   => $stallObj->getSalesYesterday($stall_id, $yesterdayStart, $yesterdayEnd)
    ];
}
if($sevenAvailable) {
    $timePeriods['seven'] = [
        'label'   => '7 Days',
        'display' => date("M d, Y", strtotime($sevenStart)) . " - " . date("M d, Y", strtotime($sevenEnd)),
        'start'   => $sevenStart . " 00:00:00",
        'end'     => $sevenEnd . " 23:59:59",
        'sales'   => $stallObj->getSales7Days($stall_id, $sevenStart . " 00:00:00", $sevenEnd . " 23:59:59")
    ];
}
if($thirtyAvailable) {
    $timePeriods['thirty'] = [
        'label'   => '30 Days',
        'display' => date("M d, Y", strtotime($thirtyStart)) . " - " . date("M d, Y", strtotime($thirtyEnd)),
        'start'   => $thirtyStart . " 00:00:00",
        'end'     => $thirtyEnd . " 23:59:59",
        'sales'   => $stallObj->getSales30Days($stall_id, $thirtyStart . " 00:00:00", $thirtyEnd . " 23:59:59")
    ];
}
if($yearAvailable) {
    $timePeriods['year'] = [
        'label'   => '1 Year',
        'display' => date("M d, Y", strtotime($yearStart)) . " - " . date("M d, Y", strtotime($yearEnd)),
        'start'   => $yearStart . " 00:00:00",
        'end'     => $yearEnd . " 23:59:59",
        'sales'   => $stallObj->getSales1Year($stall_id, $yearStart . " 00:00:00", $yearEnd . " 23:59:59")
    ];
}
?>
<style>
    main {
        padding: 20px 120px;
    }
    .section-content { display: none; }
    .section-content.active { display: block; }
</style>
<main>
    <div class="nav-container d-flex gap-3 my-2">
        <?php foreach($timePeriods as $key => $period): ?>
            <a href="#<?php echo $key; ?>" class="nav-link" data-target="<?php echo $key; ?>">
                <?php echo $period['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <?php 
    foreach($timePeriods as $key => $period):
        $productsReport  = $stallObj->getProductsReport($stall_id, $period['start'], $period['end']);
        $liveOps         = $stallObj->getLiveOpsMonitor($stall_id, $period['start'], $period['end']);
        $opsHealth       = $stallObj->getOperationsHealth($stall_id, $period['start'], $period['end']);
        $highestProducts = $stallObj->getHighestSellingProducts($stall_id, $period['start'], $period['end']);
    ?>
    <div id="<?php echo $key; ?>" class="section-content <?php echo $key === 'today' ? 'active' : ''; ?>">
        <div class="d-flex gap-3 mb-3">
            <div class="bg-white border rounded-2 p-4 w-75">
                <div class="w-100 d-flex mb-4">
                    <div class="w-50">
                        <div class="d-flex gap-2 align-items-center mb-2">
                            <h5 class="m-0 fw-bold">Sales <?php echo $period['label']; ?></h5>
                            <span class="small text-muted">(<?php echo $period['display']; ?>)</span>
                        </div>
                        <span class="small" style="color: #CD5C08; cursor: pointer;">
                            Download Report <i class="fa-regular fa-circle-down ms-2"></i>
                        </span>
                    </div>
                    <div class="w-25 text-end">
                        <h4 class="m-0 fw-bold"><?php echo $period['sales']['totalOrders']; ?></h4>
                        <span class="small">Total Orders</span>
                    </div>
                    <div class="w-25 text-end">
                        <h4 class="m-0 fw-bold">₱<?php echo $period['sales']['totalSales']; ?></h4>
                        <span class="small">Total Sales</span>
                    </div>
                </div>
                <table class="salestable w-100" id="outletViewTable">
                    <tr>
                        <th class="w-50">Product Name</th>
                        <th class="text-end w-25">Order Count</th>
                        <th class="text-end w-25">Sales</th>
                    </tr>
                    <?php if($productsReport): foreach($productsReport as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="text-end"><?php echo $product['order_count']; ?></td>
                        <td class="text-end">₱<?php echo $product['sales']; ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </table>
                <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3">
                    <!-- Pagination will be dynamically generated -->
                </div>
            </div>
            <div class="bg-white border p-4 w-25 rounded-2">
                <h5 class="m-0 fw-bold mb-1">Live Ops Monitor</h5>
                <span class="small text-muted">Ongoing issues for your food stall</span>
                <div class="d-flex align-items-center border rounded-2 py-2 my-2">
                    <h4 class="text-danger px-3 m-0 fw-bold"><?php echo $liveOps['canceled_orders']; ?></h4>
                    <div>
                        <p class="m-0 fw-bold">Canceled Orders</p>
                        <span class="text-muted small"><?php echo $period['label']; ?></span>
                    </div>
                    <i class="fa-solid fa-angle-right ms-auto me-3"></i> 
                </div>
                <div class="d-flex align-items-center border rounded-2 py-2 mb-2">
                    <h4 class="text-success px-3 m-0 fw-bold"><?php echo $liveOps['new_customers']; ?></h4>
                    <div>
                        <p class="m-0 fw-bold">New Customers</p>
                        <span class="text-muted small"><?php echo $period['label']; ?></span>
                    </div>
                    <i class="fa-solid fa-angle-right ms-auto me-3"></i> 
                </div>
                <div class="d-flex align-items-center border rounded-2 py-2 mb-2">
                    <h4 class="text-success px-3 m-0 fw-bold"><?php echo $liveOps['repeated_customers']; ?></h4>
                    <div>
                        <p class="m-0 fw-bold">Repeated Customers</p>
                        <span class="text-muted small"><?php echo $period['label']; ?></span>
                    </div>
                    <i class="fa-solid fa-angle-right ms-auto me-3"></i> 
                </div>
                <div class="d-flex align-items-center border rounded-2 py-2">
                    <h4 class="text-danger px-3 m-0 fw-bold"><?php echo $liveOps['no_sales']; ?></h4>
                    <div>
                        <p class="m-0 fw-bold">No Product Sales</p>
                        <span class="text-muted small"><?php echo $period['label']; ?></span>
                    </div>
                    <i class="fa-solid fa-angle-right ms-auto me-3"></i> 
                </div>
            </div>
        </div>
        <div class="d-flex gap-3">
            <div class="bg-white border rounded-2 p-4 w-50">
                <h5 class="m-0 fw-bold mb-1">Operations Health</h5>
                <span class="small text-muted">We found some ongoing issues for your food stall</span>
                <div class="d-flex gap-3 my-3">
                    <div class="p-3 d-flex align-items-end border w-50 rounded-2" style="background-color: #f4f4f4;">
                        <div class="w-50">
                            <h5 class="m-0 fw-bold">₱<?php echo $opsHealth['GCash']; ?></h5>
                            <span>GCash</span>
                        </div>
                        <span>vs.</span>
                        <div class="w-50 text-end">
                            <h5 class="m-0 fw-bold">₱<?php echo $opsHealth['Cash']; ?></h5>
                            <span>Cash</span>
                        </div>
                    </div>
                    <div class="p-3 d-flex align-items-end border w-50 rounded-2" style="background-color: #f4f4f4;">
                        <div class="w-50">
                            <h5 class="m-0 fw-bold">₱<?php echo $opsHealth['Dine In']; ?></h5>
                            <span>Dine In</span>
                        </div>
                        <span>vs.</span>
                        <div class="w-50 text-end">
                            <h5 class="m-0 fw-bold">₱<?php echo $opsHealth['Take Out']; ?></h5>
                            <span>Take Out</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="p-3 rounded-2 w-50 border" style="background-color: #f4f4f4;">
                        <h5 class="m-0 fw-bold mb-1"><?php echo $opsHealth['avg_prep_time']; ?> min</h5>
                        <p class="mb-4">Avg. Preparation Time</p>
                        <span class="text-muted small"><?php echo $period['label']; ?></span>
                    </div>
                    <div class="p-3 rounded-2 w-50 border" style="background-color: #f4f4f4;">
                        <h5 class="m-0 fw-bold mb-1">₱<?php echo $opsHealth['lost_sales']; ?></h5>
                        <p class="mb-4">Lost Sales Due to Cancel</p>
                        <span class="text-muted small"><?php echo $period['label']; ?></span>
                    </div>
                </div>
            </div>
            <div class="bg-white border rounded-2 p-4 w-50">
                <h5 class="m-0 fw-bold mb-1">Highest Selling Product</h5>
                <span class="small text-muted">Top 5 products by orders and sales</span>
                <table class="salestable w-100 mt-4">
                    <tr>
                        <th class="w-50">Product Name</th>
                        <th class="text-end w-25">Order Count</th>
                        <th class="text-end w-25">Sales</th>
                    </tr>
                    <?php if($highestProducts && count($highestProducts) > 0): ?>
                        <?php foreach($highestProducts as $prod): ?>
                        <tr>
                            <td class="fw-normal"><?php echo htmlspecialchars($prod['name']); ?></td>
                            <td class="text-end fw-normal"><?php echo $prod['order_count']; ?></td>
                            <td class="text-end fw-normal">₱<?php echo $prod['sales']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No sales data.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <br><br><br><br><br><br>
</main>
<script src="./assets/js/navigation.js?v=<?php echo time(); ?>"></script>
<script src="./assets/js/sales.js?v=<?php echo time(); ?>"></script>
<?php include_once './footer.php'; ?>
