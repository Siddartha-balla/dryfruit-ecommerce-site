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

// Query to calculate freezed items
$query = "
    SELECT 
        oi.product_id, 
        p.name AS product_name, 
        SUM(oi.quantity) AS freezed_quantity
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.status = 'Pending' AND o.expiry_time > NOW()
    GROUP BY oi.product_id
";

$result = $conn->query($query);

$freezedItems = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $freezedItems[] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'freezed_quantity' => $row['freezed_quantity']
        ];
    }
}

echo json_encode(['success' => true, 'data' => $freezedItems]);
$conn->close();
?>