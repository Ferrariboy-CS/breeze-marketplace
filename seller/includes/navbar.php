<?php
// Build seller notifications: recent orders on this seller's products with driver status
$sellerId = $_SESSION['id'];
$notifQuery = $query->executeQuery("SELECT o.id AS order_id, o.created_at, o.status AS order_status, d.status AS delivery_status, a.name AS driver_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    LEFT JOIN deliveries d ON d.order_id = o.id
    LEFT JOIN accounts a ON d.driver_id = a.id
    WHERE p.seller_id = $sellerId
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5");

$notifItems = $notifQuery->fetch_all(MYSQLI_ASSOC);
$notifCount = count($notifItems);
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="./" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a onclick="logout()" class="nav-link">Logout</a>
        </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search" name="search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages shortcut (opens orders page) -->
        <li class="nav-item dropdown">
            <a class="nav-link" href="../orders.php">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge"><?= $notifCount; ?></span>
            </a>
        </li>
        
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge"><?= $notifCount; ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header"><?= $notifCount; ?> recent orders</span>
                <div class="dropdown-divider"></div>
                <?php if (empty($notifItems)): ?>
                    <span class="dropdown-item">No recent orders</span>
                <?php else: ?>
                    <?php foreach ($notifItems as $item): ?>
                        <a href="../orders.php" class="dropdown-item">
                            <i class="fas fa-truck mr-2"></i>
                            Order #<?= $item['order_id']; ?> - <?= ucfirst(str_replace('_',' ', $item['delivery_status'] ?? 'pending')); ?>
                            <span class="float-right text-muted text-sm"><?= date('m/d H:i', strtotime($item['created_at'])); ?></span>
                            <br><small class="text-muted">Driver: <?= $item['driver_name'] ?: 'TBD'; ?> | Order: <?= ucfirst(str_replace('_',' ', $item['order_status'])); ?></small>
                        </a>
                        <div class="dropdown-divider"></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="../orders.php" class="dropdown-item dropdown-footer">View all orders</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button"><i class="fas fa-th-large"></i></a>
        </li>
    </ul>
</nav>