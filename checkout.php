<?php
include 'check.php';

$user_id = $_SESSION['id'];

$user = $query->executeQuery("SELECT * FROM accounts WHERE id = $user_id")->fetch_assoc();
$cart = $query->executeQuery("SELECT * FROM cart WHERE user_id = $user_id");

$price_old_Sum = 0;
$price_current_Sum = 0;
$msg = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = isset($_POST['address']) ? $query->validate($_POST['address']) : '';
    $area = isset($_POST['area']) ? $query->validate($_POST['area']) : '';
    $card_name = isset($_POST['card_name']) ? $query->validate($_POST['card_name']) : '';
    $card_number_raw = isset($_POST['card_number']) ? preg_replace('/[^0-9]/', '', $_POST['card_number']) : '';
    $exp_month = isset($_POST['exp_month']) ? (int) $_POST['exp_month'] : 0;
    $exp_year = isset($_POST['exp_year']) ? (int) $_POST['exp_year'] : 0;
    $cvv = isset($_POST['cvv']) ? preg_replace('/[^0-9]/', '', $_POST['cvv']) : '';

    if (empty($address) || empty($area)) {
        $msg = [
            'title' => 'Address required',
            'text' => 'Please provide delivery address and area.',
            'icon' => 'error'
        ];
    } elseif (empty($card_name) || empty($card_number_raw) || empty($exp_month) || empty($exp_year) || empty($cvv)) {
        $msg = [
            'title' => 'Payment required',
            'text' => 'Please fill in all Visa card details.',
            'icon' => 'error'
        ];
    } elseif (strlen($card_number_raw) < 13 || strlen($card_number_raw) > 19) {
        $msg = [
            'title' => 'Card number invalid',
            'text' => 'Enter a valid card number (13-19 digits).',
            'icon' => 'error'
        ];
    } elseif ($exp_month < 1 || $exp_month > 12 || $exp_year < (int) date('Y') || ($exp_year == (int) date('Y') && $exp_month < (int) date('n'))) {
        $msg = [
            'title' => 'Card expired',
            'text' => 'Check the expiry month/year.',
            'icon' => 'error'
        ];
    } elseif (strlen($cvv) < 3 || strlen($cvv) > 4) {
        $msg = [
            'title' => 'CVV invalid',
            'text' => 'Enter the 3 or 4 digit CVV.',
            'icon' => 'error'
        ];
    } elseif ($cart->num_rows === 0) {
        $msg = [
            'title' => 'Cart empty',
            'text' => 'Add items before placing an order.',
            'icon' => 'error'
        ];
    } else {
        $items = [];
        while ($row = $cart->fetch_assoc()) {
            $product_id = $row['product_id'];
            $product = $query->executeQuery("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
            $items[] = [
                'product_id' => $product_id,
                'quantity' => (int) $row['number_of_products'],
                'price_old' => (float) $product['price_old'],
                'price_current' => (float) $product['price_current'],
                'name' => $product['name']
            ];
            $price_old_Sum += $product['price_old'] * $row['number_of_products'];
            $price_current_Sum += $product['price_current'] * $row['number_of_products'];
        }

        $payment_method = 'visa';
        $payment_status = 'paid';
        $card_last4 = substr($card_number_raw, -4);
        $paid_at = date('Y-m-d H:i:s');

        $orderId = $query->lastInsertId('orders', [
            'user_id' => $user_id,
            'address' => $address,
            'area' => $area,
            'total_old' => $price_old_Sum,
            'total_current' => $price_current_Sum,
            'payment_method' => $payment_method,
            'payment_status' => $payment_status,
            'card_last4' => $card_last4,
            'paid_at' => $paid_at,
            'status' => 'awaiting_driver'
        ]);

        if ($orderId) {
            foreach ($items as $item) {
                $query->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_old' => $item['price_old'],
                    'price_current' => $item['price_current']
                ]);
            }

            $driverId = $query->findDriverByArea($area);
            $deliveryStatus = $driverId ? 'assigned' : 'awaiting_driver';
            $query->createDelivery($orderId, $driverId, $deliveryStatus);
            $query->update('orders', ['status' => $deliveryStatus], "WHERE id = '$orderId'");

            $query->clearCart($user_id);

            $msg = [
                'title' => 'Order placed',
                'text' => ($driverId ? 'Driver assigned automatically. ' : 'No driver in your area yet. We will assign one soon. ') . 'Payment processed via Visa.',
                'icon' => 'success'
            ];
        } else {
            $msg = [
                'title' => 'Error',
                'text' => 'Failed to create order. Please try again.',
                'icon' => 'error'
            ];
        }

        // Refresh cart data after clearing/processing
        $cart = $query->executeQuery("SELECT * FROM cart WHERE user_id = $user_id");
        $price_old_Sum = 0;
        $price_current_Sum = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            width: 90%;
            overflow-x: auto;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 2.5em;
            font-weight: bold;
        }

        h3 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 15px;
            text-decoration: underline;
        }

        .user-information,
        .cart-summary {
            margin-bottom: 40px;
        }

        .user-information ul {
            list-style-type: none;
            padding: 0;
            font-size: 1.1em;
            color: #555;
        }

        .user-information li {
            margin-bottom: 12px;
        }

        .user-information li strong {
            color: #7fad39;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th,
        td {
            padding: 15px 20px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 1.1em;
        }

        th {
            background-color: #f1f1f1;
            color: #7fad39;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e0f7fa;
            transition: background-color 0.3s ease;
        }

        .total {
            font-size: 1.4em;
            font-weight: bold;
            color: #333;
            margin-top: 25px;
            text-align: right;
        }

        .total p {
            margin: 15px 0;
        }

        .total span {
            color: rgb(255, 51, 0);
        }

        .price del {
            color: rgb(255, 0, 0);
            font-size: 14px;
        }

        .price {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price span {
            color: #7fad39;
            font-weight: bold;
        }

        .cart-summary {
            border-top: 2px solid #f1f1f1;
            padding-top: 20px;
        }

        del {
            font-weight: bold;
        }


        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            h2 {
                font-size: 2em;
            }

            h3 {
                font-size: 1.2em;
            }

            table th,
            table td {
                font-size: 1em;
                padding: 10px;
            }

            .total p {
                font-size: 1.2em;
            }

            .user-information ul {
                font-size: 1em;
            }

            .price span {
                font-size: 1em;
            }

            .price del {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {

            .container {
                width: 100%;
                padding: 10px;
            }

            h2 {
                font-size: 1.8em;
            }

            h3 {
                font-size: 1.1em;
            }

            .user-information ul {
                font-size: 0.9em;
            }

            table th,
            table td {
                font-size: 0.9em;
                padding: 8px;
            }

            .price span {
                font-size: 0.9em;
            }

            .total p {
                font-size: 1em;
            }

            .cart-summary {
                padding-top: 15px;
            }
        }
    </style>
</head>

<body>

    <?php if (!empty($msg)): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                title: "<?php echo $msg['title']; ?>",
                text: "<?php echo $msg['text']; ?>",
                icon: "<?php echo $msg['icon']; ?>",
                confirmButtonText: "OK"
            }).then(() => {
                // After a successful checkout, send users back to the home page.
                if ("<?php echo $msg['icon']; ?>" === "success") {
                    window.location.href = "index.php";
                }
            });
        </script>
    <?php endif; ?>

    <div class="container">
        <h2>Checkout Summary</h2>

        <div class="user-information">
            <h3>User Information</h3>
            <ul>
                <li><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></li>
                <li><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></li>
                <li><strong>Phone Number:</strong> <?= htmlspecialchars($user['number']); ?></li>
            </ul>
        </div>

        <form method="post" action="">
            <div class="cart-summary">
                <h3>Cart Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($cart as $index => $item) {
                            $product_id = $item["product_id"];
                            $product = $query->executeQuery("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
                            $price_old = $product['price_old'];
                            $price_current = $product['price_current'];

                            $price_old_Sum += $price_old * $item['number_of_products'];
                            $price_current_Sum += $price_current * $item['number_of_products'];
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($product['name']); ?></td>

                                <td>
                                    <div class="price">
                                        <del>N$<?= number_format($price_old, 2); ?></del>
                                        <span>N$<?= number_format($price_current, 2); ?></span>
                                    </div>
                                </td>

                                <td><?= $item['number_of_products']; ?></td>

                                <td>
                                    <div class="price">
                                        <del>N$<?= number_format($price_old * $item['number_of_products'], 2); ?></del>
                                        <span>N$<?= number_format($price_current * $item['number_of_products'], 2); ?></span>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="total">
                <p>Total (Old Price): <span><del>N$<?= number_format($price_old_Sum, 2); ?></del></span></p>
                <p>Total (Current Price): <span style="color: #7fad39">N$<?= number_format($price_current_Sum, 2); ?></span>
                </p>
            </div>

            <div class="user-information">
                <h3>Delivery Details</h3>
                <ul>
                    <li><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></li>
                    <li><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></li>
                    <li><strong>Phone Number:</strong> <?= htmlspecialchars($user['number']); ?></li>
                </ul>

                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <input type="text" id="address" name="address" required placeholder="Street, house, apt" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                </div>

                <div class="form-group">
                    <label for="area">Area / Zone</label>
                    <input type="text" id="area" name="area" required placeholder="Example: Central City" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                </div>
            </div>

            <div class="user-information">
                <h3>Payment (Visa)</h3>
                <div class="form-group" style="margin-bottom:12px;">
                    <label for="card_name">Name on Card</label>
                    <input type="text" id="card_name" name="card_name" required placeholder="Cardholder name" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" required placeholder="0000 0000 0000 0000" inputmode="numeric" maxlength="23" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <div class="form-group" style="flex:1; min-width:140px;">
                        <label for="exp_month">Expiry Month</label>
                        <input type="number" id="exp_month" name="exp_month" required min="1" max="12" placeholder="MM" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                    </div>
                    <div class="form-group" style="flex:1; min-width:140px;">
                        <label for="exp_year">Expiry Year</label>
                        <input type="number" id="exp_year" name="exp_year" required min="<?= date('Y'); ?>" max="<?= date('Y') + 15; ?>" placeholder="YYYY" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                    </div>
                    <div class="form-group" style="flex:1; min-width:140px;">
                        <label for="cvv">CVV</label>
                        <input type="number" id="cvv" name="cvv" required min="0" max="9999" placeholder="3-4 digits" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;">
                    </div>
                </div>
                <p style="font-size:0.9em;color:#888;margin-top:8px;">We only store the last 4 digits for reference; no full card details are saved.</p>
            </div>

            <div style="text-align:right; margin-top:20px;">
                <button type="submit" style="background:#7fad39;color:#fff;border:none;padding:12px 24px;border-radius:6px;font-size:16px;cursor:pointer;">Place Order</button>
            </div>
        </form>
    </div>

</body>

</html>