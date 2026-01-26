<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$conn = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$sql = "SELECT o.order_id, o.total_amount, o.order_date, o.expiry_time, o.status,
               oi.product_id, p.name AS product_name, p.image AS product_image, 
               oi.quantity, oi.price
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['order_details'] = [
        'total_amount' => $row['total_amount'],
        'order_date' => $row['order_date'],
        'expiry_time' => $row['expiry_time'],
        'status' => $row['status']
    ];
    $orders[$row['order_id']]['items'][] = [
        'product_name' => $row['product_name'],
        'product_image' => $row['product_image'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #d2691e;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        .order-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .order-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .order-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
        }
        .order-total {
            font-weight: bold;
            margin-top: 10px;
        }
        .order-status {
            font-weight: bold;
            color: #007bff; /* Default color for status */
        }
        .order-status.Pending {
            color: #ffc107; /* Yellow for Pending */
        }
        .order-status.Completed {
            color: #28a745; /* Green for Completed */
        }
        .order-status.Cancelled {
            color: #dc3545; /* Red for Cancelled */
        }
    </style>
</head>
<body>
    <header>
        <h1>My Orders</h1>
    </header>
    <main>
        <div class="container">
            <?php if (empty($orders)): ?>
                <p class="text-center">You have no orders yet.</p>
            <?php else: ?>
                <?php foreach ($orders as $orderId => $order): ?>
                    <div class="order-container">
                        <div class="order-header">
                            <p>Order Date: <?php echo $order['order_details']['order_date']; ?></p>
                            <p>Expiry Time: <?php echo $order['order_details']['expiry_time']; ?></p>
                            <p>Status: <span class="order-status <?php echo htmlspecialchars($order['order_details']['status']); ?>">
                                <?php echo htmlspecialchars($order['order_details']['status']); ?>
                            </span></p>
                            <?php if ($order['order_details']['status'] === 'Pending'): ?>
                                <button class="btn btn-danger btn-sm" onclick="cancelOrder(<?php echo $orderId; ?>)">Cancel Order</button>
                            <?php endif; ?>
                        </div>
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item">
                                    <img src="<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    <div>
                                        <p>Product: <?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p>Quantity: <?php echo $item['quantity']; ?>g</p>
                                        <p>Price: ₹<?php echo $item['price']; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="order-total">
                            <p>Total Amount: ₹<?php echo $order['order_details']['total_amount']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <script>
        function cancelOrder(orderId) {
            if (!confirm('Are you sure you want to cancel this order?')) {
                return;
            }

            fetch('cancel_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error cancelling order:', error);
                alert('Failed to cancel order. Please try again.');
            });
        }
    </script>
</body>
</html>