<?php
header('Content-Type: application/json');
$conn = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Query to find expired orders with a Pending status
$query = "
    SELECT oi.product_id, SUM(oi.quantity) AS quantity_to_add
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.status = 'Pending' AND o.expiry_time < NOW()
    GROUP BY oi.product_id
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];
        $quantityToAdd = $row['quantity_to_add'];

        // Update the stock in the products table
        $updateQuery = "UPDATE products SET stock = stock + ? WHERE product_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ii", $quantityToAdd, $productId);
        $stmt->execute();
    }

    // Update the status of expired orders to "Cancelled"
    $updateOrderQuery = "UPDATE orders SET status = 'Cancelled' WHERE status = 'Pending' AND expiry_time < NOW()";
    $conn->query($updateOrderQuery);

    echo json_encode(['success' => true, 'message' => 'Stock refreshed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'No expired orders found']);
}

$conn->close();
?>