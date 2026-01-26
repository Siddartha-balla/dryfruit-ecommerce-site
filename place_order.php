<?php
header('Content-Type: application/json');
session_start();

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Validate data
if (
    !isset($data['user_id']) || 
    !isset($data['total_amount']) || 
    !isset($data['cart']) || 
    !is_array($data['cart']) || 
    count($data['cart']) === 0
) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}

$user_id = intval($data['user_id']);
$total_amount = intval($data['total_amount']);
$cart = $data['cart'];

// DB Connection
$mysqli = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);

// Check connection
if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Set timezone and calculate expiry time (3 hours from now)
date_default_timezone_set('Asia/Kolkata');
$order_date = date('Y-m-d H:i:s');
$expiry_time = date('Y-m-d H:i:s', strtotime('+3 hours'));

// Check stock for each product
foreach ($cart as $item) {
    $product_id = intval($item['id']);
    $quantity_grams = intval($item['quantity']);

    // Fetch stock from products table
    $stock_query = $mysqli->prepare("SELECT stock FROM products WHERE product_id = ?");
    $stock_query->bind_param("i", $product_id);
    $stock_query->execute();
    $stock_result = $stock_query->get_result();

    if ($stock_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    $stock = $stock_result->fetch_assoc()['stock'];

    if ($quantity_grams > $stock) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock for product ID ' . $product_id]);
        exit;
    }

    $stock_query->close();
}

// Insert into orders table
$order_stmt = $mysqli->prepare("INSERT INTO orders (user_id, total_amount, order_date, expiry_time) VALUES (?, ?, ?, ?)");
$order_stmt->bind_param("iiss", $user_id, $total_amount, $order_date, $expiry_time);

if (!$order_stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to place order']);
    exit;
}

$order_id = $order_stmt->insert_id;
$order_stmt->close();

// Insert each item into order_items table and update stock
$item_stmt = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
$update_stock_stmt = $mysqli->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");

foreach ($cart as $item) {
    $product_id = intval($item['id']);
    $quantity_grams = intval($item['quantity']);
    $price_per_kg = intval($item['price']);

    // Insert into order_items table
    $item_stmt->bind_param("iiii", $order_id, $product_id, $quantity_grams, $price_per_kg);
    $item_stmt->execute();

    // Update stock in products table
    $update_stock_stmt->bind_param("ii", $quantity_grams, $product_id);
    $update_stock_stmt->execute();
}

$item_stmt->close();
$update_stock_stmt->close();
$mysqli->close();

echo json_encode(['success' => true, 'message' => 'Order placed successfully']);