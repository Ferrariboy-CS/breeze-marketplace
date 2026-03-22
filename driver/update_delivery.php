<?php
include './check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./');
    exit;
}

$deliveryId = isset($_POST['delivery_id']) ? (int) $_POST['delivery_id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

$delivery = $query->getDeliveryForDriver($deliveryId, $_SESSION['id']);

if (!$delivery) {
    header('Location: ./');
    exit;
}

$allowed = ['accepted', 'picked_up', 'en_route', 'delivered', 'failed'];
if (!in_array($status, $allowed)) {
    header('Location: ./');
    exit;
}

$query->updateDeliveryStatus($deliveryId, $_SESSION['id'], $status);
header('Location: ./');
exit;
