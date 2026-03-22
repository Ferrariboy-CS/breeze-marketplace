<?php
include 'check.php';

$sellerId = $_SESSION['id'];

// Fetch orders that include this seller's products
$sql = "SELECT o.id AS order_id, o.created_at, o.status AS order_status, o.total_current, o.total_old,
               d.status AS delivery_status, d.driver_id, a.name AS driver_name,
               a2.name AS buyer_name, o.address, o.area
        FROM orders o
        JOIN order_items oi ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN deliveries d ON d.order_id = o.id
        LEFT JOIN accounts a ON d.driver_id = a.id
        LEFT JOIN accounts a2 ON o.user_id = a2.id
        WHERE p.seller_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC";

$stmt = $query->conn->prepare($sql);
$stmt->bind_param('i', $sellerId);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch items per order
$itemsByOrder = [];
if (!empty($orders)) {
    $orderIds = array_column($orders, 'order_id');
    $idsList = implode(',', array_map('intval', $orderIds));
    $itemsResult = $query->executeQuery("SELECT oi.order_id, oi.quantity, oi.price_current, oi.price_old, p.name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id IN ($idsList)");
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
    <title>Seller Orders</title>
    <?php include './includes/css.php'; ?>
    <style>
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; color: #fff; }
        .badge-awaiting_driver { background: #ffc107; color: #000; }
        .badge-assigned { background: #17a2b8; }
        .badge-delivered { background: #28a745; }
        .badge-failed { background: #dc3545; }
        .badge-pending { background: #6c757d; }
        .items-toggle { cursor: pointer; color: #007bff; }
        .items-toggle:hover { text-decoration: underline; }
        .items-list { margin: 8px 0 0 0; padding-left: 18px; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <?php include './includes/navbar.php'; ?>
    <?php include './includes/aside.php'; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Orders for Your Products</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Orders</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <?php if (empty($orders)): ?>
                            <p class="p-3 mb-0">No orders yet.</p>
                        <?php else: ?>
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Total</th>
                                        <th>Buyer</th>
                                        <th>Address</th>
                                        <th>Order Status</th>
                                        <th>Delivery</th>
                                        <th>Driver</th>
                                        <th>Placed</th>
                                        <th>Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $idx => $order): ?>
                                        <?php
                                            $orderBadge = 'badge-' . str_replace(' ', '_', strtolower($order['order_status']));
                                            $deliveryBadge = 'badge-' . ($order['delivery_status'] ? str_replace(' ', '_', strtolower($order['delivery_status'])) : 'pending');
                                        ?>
                                        <tr>
                                            <td><?= $idx + 1; ?></td>
                                            <td>
                                                <strong>N$<?= number_format($order['total_current'], 2); ?></strong><br>
                                                <small class="text-muted">was N$<?= number_format($order['total_old'], 2); ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($order['buyer_name'] ?? ''); ?></td>
                                            <td><small><?= htmlspecialchars($order['address']); ?><br><?= htmlspecialchars($order['area']); ?></small></td>
                                            <td><span class="badge <?= $orderBadge; ?>"><?= ucfirst(str_replace('_',' ', $order['order_status'])); ?></span></td>
                                            <td><span class="badge <?= $deliveryBadge; ?>"><?= ucfirst(str_replace('_',' ', $order['delivery_status'] ?? 'pending')); ?></span></td>
                                            <td>
                                                <?php if ($order['driver_id']): ?>
                                                    <?= htmlspecialchars($order['driver_name'] ?: ('Driver #' . (int)$order['driver_id'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Not assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                                            <td><a class="items-toggle" data-target="order-<?= $order['order_id']; ?>">View</a></td>
                                        </tr>
                                        <tr id="order-<?= $order['order_id']; ?>" style="display:none;">
                                            <td colspan="9">
                                                <?php $items = $itemsByOrder[$order['order_id']] ?? []; ?>
                                                <?php if (empty($items)): ?>
                                                    <span class="text-muted">No items found.</span>
                                                <?php else: ?>
                                                    <ul class="items-list">
                                                        <?php foreach ($items as $item): ?>
                                                            <li>
                                                                <strong><?= htmlspecialchars($item['name']); ?></strong>
                                                                — Qty: <?= (int)$item['quantity']; ?>,
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

</div>

<script src="../src/js/jquery.min.js"></script>
<script src="../src/js/adminlte.js"></script>
<script src="../src/js/jquery-3.3.1.min.js"></script>
<script src="../src/js/bootstrap.min.js"></script>
<script src="../src/js/jquery.nice-select.min.js"></script>
<script src="../src/js/jquery-ui.min.js"></script>
<script src="../src/js/jquery.slicknav.js"></script>
<script src="../src/js/mixitup.min.js"></script>
<script src="../src/js/owl.carousel.min.js"></script>
<script src="../src/js/main.js"></script>

<script>
    document.querySelectorAll('.items-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.getElementById(this.dataset.target);
            if (!target) return;
            const visible = target.style.display === 'table-row';
            target.style.display = visible ? 'none' : 'table-row';
            this.textContent = visible ? 'View' : 'Hide';
        });
    });
</script>
</body>
</html>
