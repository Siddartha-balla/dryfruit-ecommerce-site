<?php
// filepath: c:\Users\Admin\dryfruit-shop\get_products.php
header('Content-Type: application/json');

$conn = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Fetch products
$query = "SELECT id, name, price, stock, frozen_stock FROM products";
$result = $conn->query($query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch products: ' . $conn->error]);
    $conn->close();
    exit;
}

$products = [];
while ($row = $result->fetch_assoc()) {
    // Sanitize output
    $row['name'] = htmlspecialchars($row['name']);
    $products[] = $row;
}

if (empty($products)) {
    echo json_encode(['success' => true, 'message' => 'No products found', 'products' => []]);
} else {
    echo json_encode(['success' => true, 'products' => $products]);
}

$conn->close();
?>