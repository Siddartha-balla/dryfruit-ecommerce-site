<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "siddu8374"; // Replace with your MySQL password
$dbname = "dryfruits";

$conn = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch products
$sql = "SELECT product_id, name, description, price, stock, image FROM products";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $price = isset($row['price']) ? (int)$row['price'] : 0;

        // Skip if price is invalid (null, zero, negative, or non-numeric)
        if ($price <= 0) {
            error_log("Invalid price for product: " . $row['name']);
            continue;
        }

        $products[] = [
            "id" => (int)$row['product_id'],
            "name" => $row['name'],
            "description" => $row['description'],
            "price" => $price,
            "stock" => (int)$row['stock'],
            "image" => $row['image']
        ];
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "No products found"]);
    exit;
}

$conn->close();

// Return products as JSON
header('Content-Type: application/json');
echo json_encode($products);
?>
