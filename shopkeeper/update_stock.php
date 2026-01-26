<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

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

$productId = $data['product_id'];
$newStock = $data['stock'];
$newPrice = $data['price'];

$stmt = $conn->prepare("UPDATE products SET stock = ?, price = ? WHERE product_id = ?");
$stmt->bind_param("idi", $newStock, $newPrice, $productId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Stock and price updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update stock and price']);
}

$stmt->close();
$conn->close();
?>