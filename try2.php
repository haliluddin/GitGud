<?php
    include_once 'header.php';
    include_once 'links.php';
    include_once 'modals.php';
    include_once 'nav.php';
    require_once __DIR__ . '/classes/stall.class.php';


    $stallObj = new Stall();
    
    if ($user['role'] === 'Admin' && isset($_GET['stall_id'])) {
        $stall_id = intval(decrypt(urldecode($_GET['stall_id'])));
    } else {
        $stall_id = $stallObj->getStallId(
            $_SESSION['user']['id'],
            $_SESSION['current_park_id']
        );
    }

?>
<style>
    main{
        padding: 20px 120px;
    }
    #customer {
        max-width: 250px;
        max-height: 250px;
    }
</style>

<main>

    <div class="d-flex justify-content-end mb-3">
        <div data-coreui-start-date="2022/08/03" data-coreui-end-date="2022/08/17" data-coreui-locale="en-US" data-coreui-toggle="date-range-picker"></div>
    </div>

    <div class="bg-white border rounded-2 p-4 w-100 mb-3">

        <div class="mb-2">
            <div class="d-flex align-items-center gap-3">
                <h4 class="m-0 fw-bold mb-1">Sales by Day</h4>
                <span class="small">27 Jul. - 02 Aug.</span>
            </div>
            <span class="small text-muted">Breakdown of total sales and order volume per day. Use this to see whether your stall is trending upwards or downwards over time.</span>
        </div>
        
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" data-coreui-toggle="tab" href="#sales-pane">Sales</a></li>
            <li class="nav-item"><a class="nav-link" data-coreui-toggle="tab" href="#orders-pane">Orders</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="sales-pane">
                <div class="my-3">
                    <h3 class="m-0 fw-bold">₱433</h3>
                    <span>Total Sales</span>
                </div>
                <canvas id="sales" width="400" height="100"></canvas>
                <script>
                    const salesCtx = document.getElementById('sales').getContext('2d');
                    const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 400);
                    salesGradient.addColorStop(0, '#F79043');
                    salesGradient.addColorStop(1, '#FBCAA5');
                    const salesChart = new Chart(salesCtx, {
                        type: 'line',
                        data: {
                            labels: ['07/27', '07/28', '07/29', '07/30', '07/31', '08/01', '08/02'],
                            datasets: [{
                                data: [65, 59, 80, 81, 56, 55, 70],
                                fill: true,
                                backgroundColor: salesGradient,
                                borderColor: '#CD5C08',
                                borderWidth: 2,
                                tension: 0.4,
                                pointBackgroundColor: 'white',
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: { y: { beginAtZero: true } },
                            plugins: { legend: { display: false } }
                        }
                    });
                </script>
            </div>

            <div class="tab-pane fade" id="orders-pane">
                <div class="my-3">
                    <h3 class="m-0 fw-bold">66</h3>
                    <span>Total Orders</span>
                </div>
                <canvas id="orders" width="400" height="100"></canvas>
                <script>
                    let ordersChart;
                    document.querySelector('a[href="#orders-pane"]').addEventListener('shown.coreui.tab', () => {
                        if (!ordersChart) {
                            const ordersCtx = document.getElementById('orders').getContext('2d');
                            const ordersGradient = ordersCtx.createLinearGradient(0, 0, 0, 400);
                            ordersGradient.addColorStop(0, '#F79043');
                            ordersGradient.addColorStop(1, '#FBCAA5');
                            ordersChart = new Chart(ordersCtx, {
                                type: 'line',
                                data: {
                                    labels: ['07/27', '07/28', '07/29', '07/30', '07/31', '08/01', '08/02'],
                                    datasets: [{
                                        data: [65, 59, 80, 81, 56, 55, 60],
                                        fill: true,
                                        backgroundColor: ordersGradient,
                                        borderColor: '#CD5C08',
                                        borderWidth: 2,
                                        tension: 0.4,
                                        pointBackgroundColor: 'white',
                                        pointRadius: 4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: { y: { beginAtZero: true } },
                                    plugins: { legend: { display: false } }
                                }
                            });
                        } else {
                            ordersChart.resize();
                        }
                    });
                </script>
            </div>
        </div>

    </div>

    <div class="d-flex gap-3 mb-3">
        <div class="bg-white border rounded-2 p-4 w-75">
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3">
                    <h4 class="m-0 fw-bold mb-1">Sales by Menu Item</h4>
                    <span class="small">27 Jul. - 02 Aug.</span>
                </div>
                <span class="small text-muted">Ranking of which menu items are the most and least popular. Use this to see which of you menu items are trending up or down over time.</span>
            </div>
            <table class="salestable w-100">
                <tr>
                    <th style="width:12%">Leaderboard</th>
                    <th style="width:60%">Product Name</th>
                    <th style="width:14%" class="text-end">Sales</th>
                    <th style="width:14%" class="text-end">Order Count</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Product 7</td>
                    <td class="text-end">₱220</td>
                    <td class="text-end">22</td>
                </tr>
            </table>
            <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3"></div>
        </div>
        <div class="bg-white border p-4 w-25 rounded-2">
            <h4 class="m-0 fw-bold mb-3">Live Ops Monitor</h4>
            <div class="d-flex align-items-center border rounded-2 py-2 my-2">
                <h4 class="text-danger px-3 m-0 fw-bold">2</h4>
                <div>
                    <p class="m-0 fw-bold">Canceled Orders</p>
                    <span class="small">27 Jul. - 02 Aug.</span>
                </div>
                <i class="fa-solid fa-angle-right ms-auto me-3"></i>
            </div>
            <div class="d-flex align-items-center border rounded-2 py-2 mb-2">
                <h4 class="text-success px-3 m-0 fw-bold">3</h4>
                <div>
                    <p class="m-0 fw-bold">New Customers</p>
                    <span class="small">27 Jul. - 02 Aug.</span>
                </div>
                <i class="fa-solid fa-angle-right ms-auto me-3"></i>
            </div>
            <div class="d-flex align-items-center border rounded-2 py-2 mb-2">
                <h4 class="text-success px-3 m-0 fw-bold">3</h4>
                <div>
                    <p class="m-0 fw-bold">Repeated Customers</p>
                    <span class="small">27 Jul. - 02 Aug.</span>
                </div>
                <i class="fa-solid fa-angle-right ms-auto me-3"></i>
            </div>
            <div class="d-flex align-items-center border rounded-2 py-2">
                <h4 class="text-success px-3 m-0 fw-bold">3</h4>
                <div>
                    <p class="m-0 fw-bold">No Product Sales</p>
                    <span class="small">27 Jul. - 02 Aug.</span>
                </div>
                <i class="fa-solid fa-angle-right ms-auto me-3"></i>
            </div>
        </div>
    </div>
    <div class="d-flex gap-3">
        <div class="bg-white border rounded-2 p-4 w-50">
            <div class="mb-4">
                <h4 class="m-0 fw-bold mb-1">Operations Health</h4>
                <span class="small text-muted">Overview of your stall's operational metrics, including payment method breakdown, dine-in vs take-out performance, average preparation time, and sales lost through cancellations.</span>
            </div>
            <div class="d-flex gap-3 my-3">
                <div class="p-3 d-flex align-items-end border w-50 rounded-2" style="background-color: #f4f4f4;">
                    <div class="w-50">
                        <h5 class="m-0 fw-bold">₱100</h5>
                        <span>Online</span>
                    </div>
                    <span>vs.</span>
                    <div class="w-50 text-end">
                        <h5 class="m-0 fw-bold">₱100</h5>
                        <span>Cash</span>
                    </div>
                </div>
                <div class="p-3 d-flex align-items-end border w-50 rounded-2" style="background-color: #f4f4f4;">
                    <div class="w-50">
                        <h5 class="m-0 fw-bold">₱100</h5>
                        <span>Dine In</span>
                    </div>
                    <span>vs.</span>
                    <div class="w-50 text-end">
                        <h5 class="m-0 fw-bold">₱100</h5>
                        <span>Take Out</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3">
                <div class="p-3 rounded-2 w-50 border" style="background-color: #f4f4f4;"> 
                    <h5 class="m-0 fw-bold mb-1">12 min</h5>
                    <p class="mb-4">Avg. Preparation Time</p>
                    <span class="small">27 Jul. - 02 Aug.</span>
                </div>
                <div class="p-3 rounded-2 w-50 border" style="background-color: #f4f4f4;">
                    <h5 class="m-0 fw-bold mb-1">₱100</h5>
                    <p class="mb-4">Lost Sales Due to Cancel</p>
                    <span class="small">27 Jul. - 02 Aug.</span>
                </div>
            </div>
        </div>
        <div class="bg-white border rounded-2 p-4 w-50">
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3">
                    <h4 class="m-0 fw-bold mb-1">Customer Conversion</h4>
                    <span class="small">27 Jul. - 02 Aug.</span>
                </div>
                <span class="small text-muted">See how often customers who find your stall end up viewing your menu, and how often customers who view your menu end up placing an order.</span>
            </div>
            <div class="d-flex justify-content-center">
                <canvas id="customer"></canvas>
            </div>
            
            <script>
                const custCtx = document.getElementById('customer').getContext('2d');
                new Chart(custCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Viewed Menu', 'Placed Order'],
                        datasets: [{
                            data: [80, 20],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)'
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom', labels: {boxWidth:12, padding:16} },
                            tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.parsed}%` } }
                        }
                    }
                });
            </script>
        </div>
    </div>

</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./assets/js/sales.js?v=<?php echo time(); ?>"></script>
<?php
    include_once './footer.php';
?>
