<?php
session_start();
$isLoggedIn = isset($_SESSION['username']); // Check if the user is logged in
if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); // Clear the message after displaying it
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dry Fruit Shop - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fffdd0; /* Light cream background */
            color: #333;
        }

        header {
            margin-bottom: 20px;
        }

        .navbar-brand {
            font-weight: bold;
            color: #d2691e !important;
        }

        .navbar-nav .nav-link {
            color: #333 !important;
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
            color: #d2691e !important;
        }

        .featured-products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            width: 250px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        footer {
            background-color: #d2691e;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Sri Nidhi Dry Fruit Shop</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <!-- <a class="nav-link" href="index.php">Home</a> -->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="product.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">Cart</a>
                        </li>
                        <?php if ($isLoggedIn): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="myorders.php">My Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">
                                    <i class="fa-solid fa-circle-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <!-- <a class="nav-link" href="logout.php">Logout</a> -->
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <h2 class="text-center">Fresh and Healthy Dry Fruits </h2>
        <p class="text-center">Explore our wide range of premium quality dry fruits, nuts, and seeds. Perfect for your health and taste buds!</p>

        <h3 class="text-center">Featured Products</h3>
        <div class="featured-products" id="featured-products">
            <!-- Featured products will be dynamically added here -->
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Sri Nidhi Dry Fruit Shop. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const featuredProducts = [
            { id: 1, name: "Walnut", price: 100, image: "images/wall nut.webp", description: "Rich in Omega-3, helps in brain function and heart health." },
            { id: 2, name: "Almond", price: 200, image: "images/almond.webp", description: "Loaded with Vitamin E, great for skin, hair, and energy." },
            { id: 3, name: "Cashew (Kaju)", price: 220, image: "images/cashew.webp", description: "Creamy and delicious, a great source of protein and iron." },
            { id: 4, name: "Pista (Plain)", price: 210, image: "images/pista plain.webp", description: "Low in calories, high in antioxidants, good for heart health." }
        ];

        function renderFeaturedProducts() {
            const featuredProductsContainer = document.getElementById('featured-products');
            featuredProducts.forEach(product => {
                const productCard = document.createElement('div');
                productCard.classList.add('product-card');

                productCard.innerHTML = `
                    <img src="${product.image}" alt="${product.name}">
                    <h3>${product.name}</h3>
                    <p>₹${product.price.toFixed(2)}</p>
                    <button onclick="viewProduct(${product.id})" class="btn btn-primary">View Details</button>
                `;

                featuredProductsContainer.appendChild(productCard);
            });
        }

        function viewProduct(productId) {
            window.location.href = `product.php?productId=${productId}`;
        }

        renderFeaturedProducts();
    </script>
</body>
</html>