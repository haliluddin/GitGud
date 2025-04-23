<?php
session_start();
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';

$adminObj = new Admin();
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';
$reports = $adminObj->searchReports($searchTerm);
$output = '';

if ($reports) {
    foreach ($reports as $report) {
        $fullReporter = htmlspecialchars($report['reporter_first'] . ' ' . $report['reporter_last']);
        $fullReported = htmlspecialchars($report['reported_first'] . ' ' . $report['reported_last']);
        $status = $report['status'];
        if ($status == 'Pending') {
            $statusHTML = '<span class="small rounded-5 text-warning border border-warning p-1 border-2 fw-bold">Pending</span>';
        } elseif ($status == 'Rejected') {
            $statusHTML = '<span class="small rounded-5 text-danger border border-danger p-1 border-2 fw-bold">Rejected</span>';
        } elseif ($status == 'Resolved') {
            $statusHTML = '<span class="small rounded-5 text-success border border-success p-1 border-2 fw-bold">Resolved</span>';
        }
        $output .= '<tr>';
        $output .= '<td class="fw-normal small py-3 px-4">' . $fullReporter . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . $fullReported . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($report['reason']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . htmlspecialchars($report['created_at']) . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">' . $statusHTML . '</td>';
        $output .= '<td class="fw-normal small py-3 px-4">';
        if ($report['status'] == 'Pending') {
            $output .= '<form method="POST" action="" style="display:inline-block; margin-right:5px;">
                            <input type="hidden" name="report_id" value="' . $report['id'] . '">
                            <input type="hidden" name="action" value="resolve">
                            <input type="submit" name="report_update" value="Resolve" class="bg-success text-white border-0 small py-1 rounded-1" style="width:60px;">
                        </form>';
            $output .= '<form method="POST" action="" style="display:inline-block;">
                            <input type="hidden" name="report_id" value="' . $report['id'] . '">
                            <input type="hidden" name="action" value="reject">
                            <input type="submit" name="report_update" value="Reject" class="bg-danger text-white border-0 small py-1 rounded-1" style="width:60px;">
                        </form>';
        } else {
            $output .= '<input type="button" value="Resolve" class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px;" disabled>
                        <input type="button" value="Reject" class="bg-muted text-white border-0 small py-1 rounded-1" style="width:60px;" disabled>';
        }
        $output .= '</td>';
        $output .= '</tr>';
    }
} else {
    $output .= "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><script>Swal.fire({icon: 'info', title: 'No Results', text: 'No result found.', confirmButtonColor: '#CD5C08'});</script>";
}

echo $output;
?>
