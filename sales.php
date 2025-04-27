<?php
    include_once 'header.php';
    include_once 'links.php';
    include_once 'modals.php';
    include_once 'nav.php';
    require_once __DIR__ . '/classes/stall.class.php';
    require_once __DIR__ . '/classes/encdec.class.php';


    $stallObj = new Stall();

    if ($user['role'] === 'Admin' && isset($_GET['stall_id'])) {
        $stall_id = intval(decrypt(urldecode($_GET['stall_id'])));
    } else {
        $stall_id = $stallObj->getStallId(
            $_SESSION['user']['id'],
            $_SESSION['current_park_id']
        );
    }

    $start_date = $_GET['start'] ?? date('Y-m-d', strtotime('-6 days'));
    $end_date   = $_GET['end']   ?? date('Y-m-d');

    $salesByDay    = $stallObj->getSalesByDay($stall_id, $start_date, $end_date);
    $ordersByDay   = $stallObj->getOrdersByDay($stall_id, $start_date, $end_date);
    $menuItems     = $stallObj->getSalesByMenuItem($stall_id, $start_date, $end_date, 10, 0);
    $liveOps       = $stallObj->getLiveOpsMonitor($stall_id, $start_date, $end_date);
    $payBreakdown  = $stallObj->getPaymentMethodBreakdown($stall_id, $start_date, $end_date);
    $typeBreakdown = $stallObj->getOrderTypeBreakdown($stall_id, $start_date, $end_date);
    $avgPrepTime   = $stallObj->getAvgPreparationTime($stall_id, $start_date, $end_date);
    $lostSales     = $stallObj->getLostSalesDueToCancel($stall_id, $start_date, $end_date);
    $conversion    = $stallObj->getCustomerConversion($stall_id, $start_date, $end_date);

    $salesDates  = array_column($salesByDay, 'date');
    $salesVals   = array_column($salesByDay, 'total_sales');
    $ordersDates = array_column($ordersByDay, 'date');
    $ordersVals  = array_column($ordersByDay, 'total_orders');
    $totalSales  = array_sum($salesVals);
    $totalOrders = array_sum($ordersVals);
?>
<style>
    main{ padding: 20px 120px; }
    #customer { max-width: 250px; max-height: 250px; }
</style>

<main>

  <div class="d-flex justify-content-end mb-3">
    <form method="get" class="d-flex gap-2">
      <input type="date" name="start" value="<?=$start_date?>" class="form-control"/>
      <input type="date" name="end"   value="<?=$end_date?>"   class="form-control"/>
      <button class="btn btn-primary">Go</button>
    </form>
  </div>

  <div class="bg-white border rounded-2 p-4 w-100 mb-3">
    <div class="mb-2">
      <div class="d-flex align-items-center gap-3">
        <h4 class="m-0 fw-bold mb-1">Sales by Day</h4>
        <span class="small"><?=date('M j',strtotime($start_date))?> – <?=date('M j',strtotime($end_date))?></span>
      </div>
      <span class="small text-muted">Breakdown of total sales and order volume per day. Use this to see whether your stall is trending upwards or downwards over time.</span>
      </div>

    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link active" data-coreui-toggle="tab" href="#sales-pane">Sales</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-coreui-toggle="tab" href="#orders-pane">Orders</a>
      </li>
    </ul>

    <div class="tab-content">

      <div class="tab-pane fade show active" id="sales-pane">
        <div class="my-3">
          <h3 class="m-0 fw-bold">₱<?=number_format($totalSales,2)?></h3>
          <span>Total Sales</span>
        </div>
        <canvas id="sales" width="400" height="100"></canvas>
      </div>

      <div class="tab-pane fade" id="orders-pane">
        <div class="my-3">
          <h3 class="m-0 fw-bold"><?=$totalOrders?></h3>
          <span>Total Orders</span>
        </div>
        <canvas id="orders" width="400" height="100"></canvas>
      </div>

    </div>
  </div>

  <div class="d-flex gap-3 mb-3">

    <div class="bg-white border rounded-2 p-4 w-75">
      <div class="mb-4">
        <div class="d-flex align-items-center gap-3">
          <h4 class="m-0 fw-bold mb-1">Sales by Menu Item</h4>
          <span class="small"><?=date('M j',strtotime($start_date))?> – <?=date('M j',strtotime($end_date))?></span>
        </div>
        <span class="small text-muted">Ranking of which menu items are the most and least popular. Use this to see which of you menu items are trending up or down over time.</span>
        </div>
      <table class="salestable w-100">
        <tr>
          <th style="width:12%">#</th>
          <th style="width:60%">Product Name</th>
          <th style="width:14%" class="text-end">Sales</th>
          <th style="width:14%" class="text-end">Orders</th>
        </tr>
        <?php foreach($menuItems as $i => $item): ?>
        <tr>
          <td><?=($i+1)?></td>
          <td><?=$item['product_name']?></td>
          <td class="text-end">₱<?=number_format($item['total_sales'],2)?></td>
          <td class="text-end"><?=$item['order_count']?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      <div class="d-flex gap-3 saletabpag align-items-center justify-content-center mt-3"></div>
    </div>

    <div class="bg-white border p-4 w-25 rounded-2">
      <h4 class="m-0 fw-bold mb-3">Live Ops Monitor</h4>

      <?php foreach([
        ['Canceled Orders','canceled_orders','danger'],
        ['New Customers','new_customers','success'],
        ['Repeated Customers','repeated_customers','success'],
        ['No Product Sales','no_product_sales','success'],
      ] as $op): ?>
      <div class="d-flex align-items-center border rounded-2 py-2 mb-2">
        <h4 class="text-<?=$op[2]?> px-3 m-0 fw-bold"><?=$liveOps[$op[1]]?></h4>
        <div>
          <p class="m-0 fw-bold"><?=$op[0]?></p>
          <span class="small"><?=date('M j',strtotime($start_date))?> – <?=date('M j',strtotime($end_date))?></span>
        </div>
        <i class="fa-solid fa-angle-right ms-auto me-3"></i>
      </div>
      <?php endforeach; ?>

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
            <h5 class="m-0 fw-bold">₱<?=number_format($payBreakdown['GCash'] ?? 0,2)?></h5>
            <span>Online</span>
          </div><span>vs.</span>
          <div class="w-50 text-end">
            <h5 class="m-0 fw-bold">₱<?=number_format($payBreakdown['Cash'] ?? 0,2)?></h5>
            <span>Cash</span>
          </div>
        </div>
        <div class="p-3 d-flex align-items-end border w-50 rounded-2" style="background-color: #f4f4f4;">
          <div class="w-50">
            <h5 class="m-0 fw-bold">₱<?=number_format($typeBreakdown['Dine In'] ?? 0,2)?></h5>
            <span>Dine In</span>
          </div><span>vs.</span>
          <div class="w-50 text-end">
            <h5 class="m-0 fw-bold">₱<?=number_format($typeBreakdown['Take Out'] ?? 0,2)?></h5>
            <span>Take Out</span>
          </div>
        </div>
      </div>
      <div class="d-flex gap-3">
        <div class="p-3 rounded-2 w-50 border" style="background-color: #f4f4f4;">
          <h5 class="m-0 fw-bold mb-1"><?=round($avgPrepTime)?> min</h5>
          <p class="mb-4">Avg. Preparation Time</p>
          <span class="small"><?=date('M j',strtotime($start_date))?> – <?=date('M j',strtotime($end_date))?></span>
        </div>
        <div class="p-3 rounded-2 w-50 border" style="background-color: #f4f4f4;">
          <h5 class="m-0 fw-bold mb-1">₱<?=number_format($lostSales,2)?></h5>
          <p class="mb-4">Lost Sales Due to Cancel</p>
          <span class="small"><?=date('M j',strtotime($start_date))?> – <?=date('M j',strtotime($end_date))?></span>
        </div>
      </div>
    </div>

    <div class="bg-white border rounded-2 p-4 w-50">
      <div class="mb-4">
        <div class="d-flex align-items-center gap-3">
          <h4 class="m-0 fw-bold mb-1">Customer Conversion</h4>
          <span class="small"><?=date('M j',strtotime($start_date))?> – <?=date('M j',strtotime($end_date))?></span>
        </div>
        <span class="small text-muted">See how often customers who find your stall end up viewing your menu, and how often customers who view your menu end up placing an order.</span>
      </div>
      <div class="d-flex justify-content-center">
        <canvas id="customer"></canvas>
      </div>
    </div>

  </div>
  <br><br><br><br><br>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const salesLabels = <?=json_encode($salesDates)?>;
  const salesData   = <?=json_encode($salesVals)?>;
  const orderLabels = <?=json_encode($ordersDates)?>;
  const orderData   = <?=json_encode($ordersVals)?>;
  const convData    = [<?=$conversion['viewed']?>, <?=$conversion['ordered']?>];

  const salesCtx = document.getElementById('sales').getContext('2d');
  const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 400);
  salesGradient.addColorStop(0, '#F79043');
  salesGradient.addColorStop(1, '#FBCAA5');
  new Chart(salesCtx, {
    type: 'line',
    data: { labels: salesLabels, datasets: [{ data: salesData, fill:true, backgroundColor:salesGradient, borderColor:'#CD5C08', tension:0.4, pointBackgroundColor:'white', pointRadius:4 }] },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
  });

  let ordersChart;
  document.querySelector('a[href="#orders-pane"]').addEventListener('shown.coreui.tab', () => {
    if (!ordersChart) {
      const ctx = document.getElementById('orders').getContext('2d');
      const grad = ctx.createLinearGradient(0,0,0,400);
      grad.addColorStop(0,'#F79043'); grad.addColorStop(1,'#FBCAA5');
      ordersChart = new Chart(ctx, {
        type: 'line',
        data: { labels: orderLabels, datasets:[{ data:orderData, fill:true, backgroundColor:grad, borderColor:'#CD5C08', tension:0.4, pointBackgroundColor:'white', pointRadius:4 }] },
        options: { responsive:true, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
      });
    } else ordersChart.resize();
  });

  const custCtx = document.getElementById('customer').getContext('2d');
  new Chart(custCtx, {
    type: 'pie',
    data: { labels:['Viewed Menu','Placed Order'], datasets:[{ data:convData, backgroundColor:['rgba(255,99,132,0.7)','rgba(54,162,235,0.7)'], borderColor:'#fff', borderWidth:2 }]},
    options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{boxWidth:12, padding:16}}, tooltip:{ callbacks:{ label: ctx => `${ctx.label}: ${ctx.parsed}%` } } } }
  });
</script>

<?php include_once './footer.php'; ?>
