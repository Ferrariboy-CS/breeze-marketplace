<?php
include 'check.php';
include './includes/header.php';

$userId = $_SESSION['id'];

$userIdSafe = (int) $userId;
$orders = $query->select(
    "orders o LEFT JOIN deliveries d ON d.order_id = o.id",
    "o.id, o.address, o.area, o.total_current, o.total_old, o.payment_method, o.payment_status, o.card_last4, o.paid_at, o.status AS order_status, o.created_at, d.status AS delivery_status, d.driver_id, d.assigned_at, d.picked_at, d.delivered_at",
    "WHERE o.user_id = $userIdSafe ORDER BY o.created_at DESC"
);

$itemsByOrder = [];
$orderIds = array_column($orders, 'id');
if (!empty($orderIds)) {
    $idsList = implode(',', array_map('intval', $orderIds));
    $itemsResult = $query->executeQuery("SELECT oi.order_id, oi.quantity, oi.price_current, oi.price_old, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id IN ($idsList)");
    while ($row = $itemsResult->fetch_assoc()) {
        $itemsByOrder[$row['order_id']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="icon" href="./src/images/Favicon.png">
    <link rel="stylesheet" href="./src/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="./src/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="./src/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="./src/css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="./src/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="./src/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="./src/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="./src/css/style.css" type="text/css">
    <style>
        .orders-card { border: 1px solid #eee; border-radius: 10px; box-shadow: 0 6px 16px rgba(0,0,0,0.05); padding: 16px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; color: #fff; }
        .badge-awaiting_driver { background: #ffc107; color: #000; }
        .badge-assigned { background: #17a2b8; }
        .badge-delivered { background: #28a745; }
        .badge-failed { background: #dc3545; }
        .badge-pending { background: #6c757d; }
        .badge-paid { background: #28a745; }
        .badge-pendingpay { background: #ffc107; color: #000; }
        .badge-failedpay { background: #dc3545; }
        .items-toggle { color: #7fad39; font-weight: 600; cursor: pointer; text-decoration: none; }
        .items-toggle:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="orders-page">
        <section class="product spad" style="padding-top: 40px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="orders-card">
                            <h3 class="mb-3">My Orders & Delivery Status</h3>
                            <?php if (empty($orders)): ?>
                                <p>You have no orders yet.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Total</th>
                                                <th>Items</th>
                                                <th>Payment</th>
                                                <th>Order Status</th>
                                                <th>Delivery Status</th>
                                                <th>Driver</th>
                                                <th>Placed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $idx => $order): ?>
                                                <?php
                                                    $orderBadge = 'badge-' . str_replace(' ', '_', strtolower($order['order_status']));
                                                    $deliveryBadge = 'badge-' . ($order['delivery_status'] ? str_replace(' ', '_', strtolower($order['delivery_status'])) : 'pending');
                                                    $payBadgeClass = $order['payment_status'] === 'paid' ? 'badge-paid' : ($order['payment_status'] === 'failed' ? 'badge-failedpay' : 'badge-pendingpay');
                                                    $payText = strtoupper($order['payment_method']) . ' · ' . strtoupper($order['payment_status']);
                                                    if (!empty($order['card_last4'])) {
                                                        $payText .= ' • • • ' . $order['card_last4'];
                                                    }
                                                ?>
                                                <tr>
                                                    <td><?= $idx + 1; ?></td>
                                                    <td>
                                                        <strong>N$<?= number_format($order['total_current'], 2); ?></strong><br>
                                                        <span class="text-muted">was N$<?= number_format($order['total_old'], 2); ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="items-toggle" data-target="order-<?= $order['id']; ?>">View</a>
                                                    </td>
                                                    <td><span class="badge <?= $payBadgeClass; ?>"><?= $payText; ?></span></td>
                                                    <td><span class="badge <?= $orderBadge; ?>"><?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?></span></td>
                                                    <td><span class="badge <?= $deliveryBadge; ?>"><?php echo $order['delivery_status'] ? ucfirst(str_replace('_', ' ', $order['delivery_status'])) : 'Pending'; ?></span></td>
                                                    <td>
                                                        <?php if ($order['driver_id']): ?>
                                                            Driver #<?= (int) $order['driver_id']; ?><br>
                                                            <span class="text-muted">Assigned: <?= $order['assigned_at'] ?: '—'; ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">Not assigned yet</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= date('Y-m-d H:i', strtotime($order['created_at'])); ?><br>
                                                        <span class="text-muted">Paid: <?= $order['paid_at'] ?: '—'; ?></span>
                                                    </td>
                                                </tr>
                                                <tr id="order-<?= $order['id']; ?>" class="items-row" style="display:none;">
                                                    <td colspan="8">
                                                        <?php $items = $itemsByOrder[$order['id']] ?? []; ?>
                                                        <?php if (empty($items)): ?>
                                                            <span class="text-muted">No items found for this order.</span>
                                                        <?php else: ?>
                                                            <ul class="items-list">
                                                                <?php foreach ($items as $item): ?>
                                                                    <li>
                                                                        <strong><?= htmlspecialchars($item['name']); ?></strong>
                                                                        — Qty: <?= (int) $item['quantity']; ?>,
                                                                        <span class="text-muted">Old: N$<?= number_format($item['price_old'], 2); ?> | Current: N$<?= number_format($item['price_current'], 2); ?></span>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include './includes/footer.php'; ?>

    <script src="./src/js/jquery-3.3.1.min.js"></script>
    <script src="./src/js/bootstrap.min.js"></script>
    <script src="./src/js/jquery.nice-select.min.js"></script>
    <script src="./src/js/jquery-ui.min.js"></script>
    <script src="./src/js/jquery.slicknav.js"></script>
    <script src="./src/js/mixitup.min.js"></script>
    <script src="./src/js/owl.carousel.min.js"></script>
    <script src="./src/js/main.js"></script>

    <script>
        document.querySelectorAll('.items-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-target');
                const row = document.getElementById(targetId);
                if (!row) return;
                row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
                this.textContent = row.style.display === 'none' ? 'View' : 'Hide';
            });
        });
    </script>
</body>
</html>
