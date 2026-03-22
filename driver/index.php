<?php
include './check.php';

$deliveries = $query->getDeliveriesByDriver($_SESSION['id']);
$nextSteps = [
    'assigned' => 'accepted',
    'accepted' => 'picked_up',
    'picked_up' => 'en_route',
    'en_route' => 'delivered'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Driver</a>
        <div class="ml-auto">
            <a class="btn btn-outline-light btn-sm" href="../logout/">Logout</a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">My Deliveries</h3>
        </div>

        <?php if (empty($deliveries)): ?>
            <div class="alert alert-info">No deliveries assigned yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Order</th>
                            <th>Address</th>
                            <th>Area</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deliveries as $row): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($row['order_id']); ?></td>
                                <td><?= htmlspecialchars($row['address']); ?></td>
                                <td><?= htmlspecialchars($row['area']); ?></td>
                                <td>N$<?= number_format($row['total_current'], 2); ?></td>
                                <td><span class="badge badge-info text-uppercase"><?= htmlspecialchars($row['status']); ?></span></td>
                                <td>
                                    <?php if (isset($nextSteps[$row['status']])): ?>
                                        <form method="post" action="update_delivery.php" class="d-inline">
                                            <input type="hidden" name="delivery_id" value="<?= $row['delivery_id']; ?>">
                                            <input type="hidden" name="status" value="<?= $nextSteps[$row['status']]; ?>">
                                            <button class="btn btn-sm btn-primary">Mark <?= ucfirst(str_replace('_', ' ', $nextSteps[$row['status']])); ?></button>
                                        </form>
                                    <?php elseif ($row['status'] === 'failed' || $row['status'] === 'delivered'): ?>
                                        <span class="text-muted">Completed</span>
                                    <?php else: ?>
                                        <form method="post" action="update_delivery.php" class="d-inline">
                                            <input type="hidden" name="delivery_id" value="<?= $row['delivery_id']; ?>">
                                            <input type="hidden" name="status" value="failed">
                                            <button class="btn btn-sm btn-danger">Mark Failed</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
