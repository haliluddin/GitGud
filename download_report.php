<?php
session_start();

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/classes/stall.class.php';

use Dompdf\Dompdf;

date_default_timezone_set('Asia/Manila');

$stallObj = new Stall();

if (!empty($_GET['stall_id']) && is_numeric($_GET['stall_id'])) {
    $stall_id = intval($_GET['stall_id']);
} elseif (!empty($_SESSION['user']['id'])) {
    $stall_id = $stallObj->getStallId($_SESSION['user']['id']);
} else {
    die('No stall or user session found.');
}

$stallCreated  = $stallObj->getStallCreationDate($stall_id);
$createdDate   = date("Y-m-d", strtotime($stallCreated));
$currentDate   = date("Y-m-d");
$daysOld       = (strtotime($currentDate) - strtotime($createdDate)) / (60*60*24);

$todayStart    = "$currentDate 00:00:00";
$todayEnd      = "$currentDate 23:59:59";
$yesterdayDate  = date("Y-m-d", strtotime("-1 day", strtotime($currentDate)));
$yesterdayStart = "$yesterdayDate 00:00:00";
$yesterdayEnd   = "$yesterdayDate 23:59:59";

$timePeriods = [
    'today' => [
        'label'   => 'Today',
        'display' => date("M d, Y", strtotime($currentDate)),
        'start'   => $todayStart,
        'end'     => $todayEnd,
        'sales'   => $stallObj->getSalesToday($stall_id, $todayStart, $todayEnd),
    ],
];

if ($daysOld >= 1) {
    $timePeriods['yesterday'] = [
        'label'   => 'Yesterday',
        'display' => date("M d, Y", strtotime($yesterdayDate)),
        'start'   => $yesterdayStart,
        'end'     => $yesterdayEnd,
        'sales'   => $stallObj->getSalesYesterday($stall_id, $yesterdayStart, $yesterdayEnd),
    ];
}

$completeWeeks = floor((($daysOld + 1) / 7));
if ($completeWeeks >= 1) {
    $sevenStart = date("Y-m-d", strtotime("$createdDate + " . (7 * ($completeWeeks - 1)) . " days"));
    $sevenEnd   = date("Y-m-d", strtotime("$sevenStart + 6 days"));
    $timePeriods['seven'] = [
        'label'   => '7 Days',
        'display' => date("M d, Y", strtotime($sevenStart)) . " - " . date("M d, Y", strtotime($sevenEnd)),
        'start'   => "$sevenStart 00:00:00",
        'end'     => "$sevenEnd 23:59:59",
        'sales'   => $stallObj->getSales7Days($stall_id, "$sevenStart 00:00:00", "$sevenEnd 23:59:59"),
    ];
}

$complete30Days = floor((($daysOld + 1) / 30));
if ($complete30Days >= 1) {
    $thirtyStart = date("Y-m-d", strtotime("$createdDate + " . (30 * ($complete30Days - 1)) . " days"));
    $thirtyEnd   = date("Y-m-d", strtotime("$thirtyStart + 29 days"));
    $timePeriods['thirty'] = [
        'label'   => '30 Days',
        'display' => date("M d, Y", strtotime($thirtyStart)) . " - " . date("M d, Y", strtotime($thirtyEnd)),
        'start'   => "$thirtyStart 00:00:00",
        'end'     => "$thirtyEnd 23:59:59",
        'sales'   => $stallObj->getSales30Days($stall_id, "$thirtyStart 00:00:00", "$thirtyEnd 23:59:59"),
    ];
}

$completeYears = floor((($daysOld + 1) / 365));
if ($completeYears >= 1) {
    $yearStart  = date("Y-m-d", strtotime("$createdDate + " . (365 * ($completeYears - 1)) . " days"));
    $yearEnd    = date("Y-m-d", strtotime("$yearStart + 364 days"));
    $timePeriods['year'] = [
        'label'   => '1 Year',
        'display' => date("M d, Y", strtotime($yearStart)) . " - " . date("M d, Y", strtotime($yearEnd)),
        'start'   => "$yearStart 00:00:00",
        'end'     => "$yearEnd 23:59:59",
        'sales'   => $stallObj->getSales1Year($stall_id, "$yearStart 00:00:00", "$yearEnd 23:59:59"),
    ];
}

$periodKey = (isset($_GET['period']) && isset($timePeriods[$_GET['period']]))
           ? $_GET['period']
           : 'today';
$period = $timePeriods[$periodKey];

$productsReport  = $stallObj->getProductsReport($stall_id, $period['start'], $period['end']);
$liveOps         = $stallObj->getLiveOpsMonitor($stall_id, $period['start'], $period['end']);
$opsHealth       = $stallObj->getOperationsHealth($stall_id, $period['start'], $period['end']);
$highestProducts = $stallObj->getHighestSellingProducts($stall_id, $period['start'], $period['end']);

ob_start();
require __DIR__ . '/report_template.php';
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("report_{$periodKey}_" . date('Ymd') . ".pdf", ['Attachment' => true]);
exit;
