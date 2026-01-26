<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// DB connection
$conn = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Fetch all orders
$order_sql = "SELECT * FROM orders ORDER BY order_date DESC";
$order_result = $conn->query($order_sql);

$orders = [];

while ($order = $order_result->fetch_assoc()) {
    $order_id = $order['order_id'];

    // Get related items with product names
    $items_sql = "
        SELECT oi.product_id, p.name AS product_name, oi.quantity, oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ";
    $stmt = $conn->prepare($items_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();

    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }

    $orders[] = [
        'order_id'     => $order['order_id'],
        'user_id'      => $order['user_id'],
        'total_amount' => $order['total_amount'],
        'order_date'   => $order['order_date'],
        'expiry_time'  => $order['expiry_time'],
        'status'       => $order['status'], // Include the status column
        'items'        => $items
    ];
}

echo json_encode($orders);
$conn->close();