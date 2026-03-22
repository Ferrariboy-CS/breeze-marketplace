<?php include 'check.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['userId']) && isset($_GET['newStatus']) && isset($_GET['userrole'])) {
    $userId = $_GET['userId'];
    $newStatus = $_GET['newStatus'];
    $role = $_GET['userrole'];

    $query->update('accounts', ['status' => $newStatus], "where id = '$userId'");

    if ($role === 'driver') {
        $query->update('driver_profiles', ['status' => $newStatus], "where account_id = '$userId'");
        header("Location: ./drivers.php");
        exit;
    }

    if ($role == 'seller') {
        header("Location: ./");
        exit;
    } else {
        header("Location: ./users.php");
        exit;
    }
}

header("Location: ./users.php");
