<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    *{
        font-family: Arial, Helvetica, sans-serif;
    }
    .first, .oh td{
        width: 50%;
    }
    .second, .third{
        width: 25%;
    }
    .head{
        border-bottom: none;
    }
    .section h2, .section h3, .section h4{
        margin: 0;
        padding-bottom: 5px;
    }
    table{
        border-collapse: collapse;
        width: 100%;
    }
    .section{
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 25px;
        margin-bottom: 20px;
    }
    .head td{
        padding-bottom: 25px;
    }
    .headtwo td, .headthree td{
        border-bottom: 1px solid #ddd;
    }
    .headthree td{
        padding: 12px 0;
        font-weight: bold;
    }
    .headtwo td{
        padding: 5px 0;
        color: gray;
        font-weight: bold;
        font-size: small;
    }
    .lom {
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    .lom td {
        padding: 15px 0;
        border: 1px solid #ddd;
    }
    .lom tr td:first-child {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        border-right: none;
    }
    .lom tr td:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        border-left: none;
    }
    .oh{
        border-collapse: separate;
        border-spacing: 15px;
    }
    .gcdt {
        border: 1px solid #ddd;
        padding: 15px;
        background-color: #f4f4f4;
        border-radius: 5px;
    }
    .per{
        color: gray;
        font-size: small;
    }
  </style>
</head>
<body>
    <div class="section">
        <table>
            <tr class="head">
                <td class="first">
                    <h2>Sales <?= $period['label'] ?></h2>
                    <span class="per"><?= $period['display'] ?></span>
                </td>
                <td class="second">
                    <h2><?= $period['sales']['totalOrders'] ?></h2>
                    <span>Total Orders</span>
                </td>
                <td class="third">
                    <h2><?= number_format($period['sales']['totalSales'],2) ?></h2>
                    <span>Total Sales</span>
                </td>
            </tr>
            <tr class="headtwo">
                <td>Product Name</td>
                <td>Order Count</td>
                <td>Sales</td>
            </tr>
            <?php if (!empty($productsReport)): foreach ($productsReport as $p): ?>
            <tr class="headthree">
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= $p['order_count'] ?></td>
                <td><?= number_format($p['sales'],2) ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr class="headthree">
                <td colspan="3" style="text-align:center">No products available.</td>
            </tr>
            <?php endif; ?>
        </table>
      </tr>  
    </div>

    <!-- Live Ops Monitor -->
    <div class="section">
        <div style="margin-bottom: 10px;">
            <h2>Live Ops Monitor</h2>
            <span>Ongoing issues for your food stall</span>
        </div>
        <table class="lom">
            <tr>
                <td style="color:red; width: 10%; text-align: center;"><h2><?= $liveOps['canceled_orders'] ?></h2></td>
                <td>
                    <h4>Canceled Orders</h4>
                    <span class="per"><?= $period['label'] ?></span>
                </td>
            </tr>
            <tr>
                <td style="color:green; width: 10%; text-align: center;"><h2><?= $liveOps['new_customers'] ?></h2></td>
                <td>
                    <h4>New Customers</h4>
                    <span class="per"><?= $period['label'] ?></span>
                </td>
            </tr>
            <tr>
                <td style="color:green; width: 10%; text-align: center;"><h2><?= $liveOps['repeated_customers'] ?></h2></td>
                <td>
                    <h4>Repeated Customers</h4>
                    <span class="per"><?= $period['label'] ?></span>
                </td>
            </tr>
            <tr>
                <td style="color:red; width: 10%; text-align: center;"><h2><?= $liveOps['no_sales'] ?></h2></td>
                <td>
                    <h4>No Product Sales</h4>
                    <span class="per"><?= $period['label'] ?></span>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <div style="margin-bottom: 10px;">
            <h2>Operations Health</h2>
            <span>Ongoing issues for your food stall</span>
        </div>
        <table class="oh">
            <tr>
                <td class="gcdt">
                    <table>
                        <tr>
                            <td style="text-align: left;"><h3><?= $opsHealth['GCash'] ?></h3></td>
                            <td style="text-align: right;" colspan="2"><h3><?= $opsHealth['GCash'] ?></h3></td>
                        </tr>
                        <tr>
                            <td style="text-align: left">GCash</td>
                            <td style="text-align: center">vs.</td>
                            <td style="text-align: right">Cash</td>
                        </tr>
                    </table>
                </td>
                <td class="gcdt">
                    <table>
                        <tr>
                            <td style="text-align: left;"><h3><?= $opsHealth['Dine In'] ?></h3></td>
                            <td style="text-align: right;" colspan="2"><h3><?= $opsHealth['Take Out'] ?></h3></td>
                        </tr>
                        <tr>
                            <td style="text-align: left">Dine In</td>
                            <td style="text-align: center">vs.</td>
                            <td style="text-align: right">Take Out</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="gcdt">
                    <table>
                        <tr>
                            <td>
                                <h3><?= $opsHealth['avg_prep_time'] ?> min</h3>
                                <span>Avg. Prep Time</span><br><br>
                                <span class="per"><?= $period['label'] ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="gcdt">
                    <table>
                        <tr>
                            <td>
                                <h3><?= $opsHealth['lost_sales'] ?></h3>
                                <span>Lost Sales</span><br><br>
                                <span class="per"><?= $period['label'] ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <div style="margin-bottom: 20px;">
            <h2>Highest Selling Products</h2>
            <span>Top 5 products by orders and sales</span>
        </div>
        <table>
            <tr class="headtwo">
                <td>Product Name</td>
                <td>Order Count</td>
                <td>Sales</td>
            </tr>
            <?php if (!empty($highestProducts)): foreach ($highestProducts as $h): ?>
            <tr class="headthree">
                <td><?= htmlspecialchars($h['name']) ?></td>
                <td><?= $h['order_count'] ?></td>
                <td><?= number_format($h['sales'],2) ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr class="headthree">
                <td colspan="3" style="text-align:center">No products available.</td>
            </tr>
            <?php endif; ?>
        </table>
      </tr>  
    </div>

</body>
</html>
