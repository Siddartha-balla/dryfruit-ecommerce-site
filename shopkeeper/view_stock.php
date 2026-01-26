<?php
header('Content-Type: application/json');
$conn = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$result = $conn->query("SELECT product_id, name, stock,price FROM products");
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>