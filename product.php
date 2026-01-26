<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Dry Fruit Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="css/style1.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fffdd0; /* Light cream background */
            color: #333;
        }

        header {
            background-color: #d2691e; /* Warm brownish-orange */
            color: #fff;
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 28px;
        }

        nav {
            margin-top: 10px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 16px;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        main {
            padding: 20px;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Product Grid */
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            width: 220px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .product-card h3 {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }

        .product-card p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }

        .product-card button {
            padding: 10px 15px;
            background-color: #d2691e; /* Warm brownish-orange */
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        .product-card button:hover {
            background-color: #b35916; /* Slightly darker shade for hover */
        }

        footer {
            background-color: #d2691e; /* Warm brownish-orange */
            color: #fff;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .product-card {
                width: 180px;
            }
        }

        @media (max-width: 480px) {
            .product-card {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php
    session_start();
    $isLoggedIn = isset($_SESSION['username']); // Check if the user is logged in
    ?>
    <header>
        <h1>Sri Nidhi Dry Fruit Shop</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="product.php">Products</a>
            <a href="cart.php">Cart</a>
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" style="float: right;">
                    <i class="fa-solid fa-circle-user"></i> 
                    <?php echo htmlspecialchars($_SESSION['name']); ?> <!-- Display user's name -->
                </a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Our Products</h2>
        <div id="product-list" class="product-grid">
            <!-- Products will be dynamically added here -->
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Sri Nidhi Dry Fruit Shop. All rights reserved.</p>
    </footer>

    <script>
        let products = [];

        async function fetchProducts() {
            try {
                const response = await fetch('fetch_products.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                products = await response.json();
                if (products.error) {
                    console.error('Error fetching products:', products.error);
                    return;
                }
                renderProducts();
            } catch (error) {
                console.error('Error fetching products:', error);
            }
        }

        function renderProducts() {
            const productList = document.getElementById('product-list');
            productList.innerHTML = ''; // Clear existing products

            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.classList.add('product-card');
                productCard.setAttribute('data-id', product.id); // Ensure data-id is set

                const productImage = document.createElement('img');
                productImage.src = product.image;
                productImage.alt = product.name;

                const productName = document.createElement('h3');
                productName.textContent = product.name;

                const productDescription = document.createElement('p');
                productDescription.classList.add('product-description');
                productDescription.textContent = product.description; // Add product description

                const productPrice = document.createElement('p');
                productPrice.classList.add('product-price');
                if (product.price && !isNaN(product.price)) {
                    productPrice.innerHTML = `<strong>Price:</strong> ₹${product.price}`;
                } else {
                    console.error(`Invalid price for product: ${product.name}`);
                    productPrice.innerHTML = `<strong>Price:</strong> Not Available`;
                }

                const quantityLabel = document.createElement('label');
                quantityLabel.setAttribute('for', `quantity-${product.id}`);
                quantityLabel.textContent = 'Quantity:';

                const quantitySelect = document.createElement('select');
                quantitySelect.id = `quantity-${product.id}`;
                [200, 500, 1000].forEach(value => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = `${value}g`;
                    quantitySelect.appendChild(option);
                });

                const addToCartButton = document.createElement('button');
                addToCartButton.textContent = 'Add to Cart';
                addToCartButton.onclick = () => addToCart(product.id);

                productCard.appendChild(productImage);
                productCard.appendChild(productName);
                productCard.appendChild(productDescription); // Append description to the card
                productCard.appendChild(productPrice);
                productCard.appendChild(quantityLabel);
                productCard.appendChild(quantitySelect);
                productCard.appendChild(addToCartButton);

                productList.appendChild(productCard);
            });
        }

        function addToCart(productId) {
            const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
            if (!productCard) {
                console.error(`Product card with data-id="${productId}" not found.`);
                return;
            }

            const productName = productCard.querySelector('h3').textContent;
            const productPriceElement = productCard.querySelector('.product-price');
            if (!productPriceElement) {
                console.error(`Price element not found for product: ${productName}`);
                return;
            }

            const productPriceMatch = productPriceElement.textContent.match(/₹([\d,.]+)/);
            const productPrice = productPriceMatch ? parseFloat(productPriceMatch[1].replace(',', '')) : NaN;

            if (isNaN(productPrice)) {
                console.error(`Invalid price for product: ${productName}`);
                return;
            }

            const productImage = productCard.querySelector('img').src;
            const quantitySelect = document.getElementById(`quantity-${productId}`);
            if (!quantitySelect) {
                console.error(`Quantity select element not found for product: ${productName}`);
                return;
            }
            const quantity = parseInt(quantitySelect.value);

            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const existingProduct = cart.find(item => item.id === productId);

            if (existingProduct) {
                existingProduct.quantity += quantity;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    quantity: quantity
                });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            alert(`${productName} (${quantity}g) has been added to your cart.`);
        }

        fetchProducts();
    </script>
</body>
</html>