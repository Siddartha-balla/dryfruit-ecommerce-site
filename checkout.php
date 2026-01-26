<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout</title>
  
  <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9; /* Light gray background */
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
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            color: #d2691e;
        }

        .checkout-summary {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .checkout-summary th, .checkout-summary td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .checkout-summary th {
            background-color: #f4f4f4;
            font-size: 16px;
            color: #555;
        }

        .checkout-summary td {
            font-size: 14px;
        }

        .checkout-total {
            text-align: right;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .checkout-actions {
            text-align: center;
            margin-top: 20px;
        }

        .checkout-actions button {
            padding: 12px 25px;
            background-color: #d2691e; /* Warm brownish-orange */
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .checkout-actions button:hover {
            background-color: #b35916; /* Slightly darker shade for hover */
        }

        .empty-checkout {
            text-align: center;
            margin: 20px 0;
            font-size: 18px;
            color: #555;
        }

        .order-expiry {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            color: #d2691e;
            font-weight: bold;
        }

        footer {
            background-color: #d2691e; /* Warm brownish-orange */
            color: #fff;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
            font-size: 14px;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            main {
                padding: 15px;
            }

            .checkout-summary th, .checkout-summary td {
                font-size: 12px;
                padding: 8px;
            }

            .checkout-actions button {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
  <header>
    <h1>Checkout</h1>
  </header>

  <main>
    <h2>Order Summary</h2>

    <table class="checkout-summary" id="checkout-summary">
      <thead>
        <tr>
          <th>Product</th>
          <th>Price (₹)</th>
          <th>Quantity (g)</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody id="checkout-items"></tbody>
    </table>

    <p class="checkout-total">Total: ₹<span id="total-amount">0.00</span></p>

    <div class="checkout-actions">
      <button id="confirm-order-btn" onclick="confirmOrder()">Confirm Order</button>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 Dry Fruit Shop. All rights reserved. | <a href="contact.html">Contact Us</a></p>
  </footer>

  <script>
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;

    function renderCheckout() {
    const table = document.getElementById('checkout-items');
    let total = 0;

    cart.forEach(item => {
        // Skip items with invalid price (NaN)
        if (isNaN(item.price)) {
            console.warn(`Skipping item with invalid price: ${item.name}`);
            return;
        }

        const itemTotal = (item.price * item.quantity) / 1000;
        total += itemTotal;

        table.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>₹${item.price}</td>
                <td>${item.quantity}g</td>
                <td>₹${itemTotal.toFixed(2)}</td>
            </tr>
        `;
    });

    document.getElementById('total-amount').textContent = total.toFixed(2);
}

function confirmOrder() {
    // Check if the user is logged in
    if (!userId) {
        alert("You must be logged in to place an order.");
        window.location.href = "login.php";
        return;
    }

    // Get the current time
    const currentTime = new Date();
    const currentHour = currentTime.getHours();
    if (currentHour >= 24 || currentHour < 6) {
        alert("Shop is closed now. Please place your order between 6 AM and 9 PM.");
        return;
    }
    const validCart = cart.filter(item => item.price && !isNaN(item.price) && item.quantity && !isNaN(item.quantity));

    if (validCart.length === 0) {
        alert("No valid items in the cart to place an order.");
        return;
    }
    const totalAmount = Math.round(validCart.reduce((sum, item) => sum + ((item.price * item.quantity) / 1000), 0));
    const orderData = {
        user_id: userId,
        total_amount: totalAmount,
        cart: validCart
    };
    fetch("place_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Order placed successfully!");
            localStorage.removeItem("cart");
            window.location.href = "order_success.php";
        } else {
            alert("Order failed. Please try again.Product out of stock");
        }
    })
    .catch(err => {
        console.error("Order Error:", err);
        alert("Error placing order.");
    });
}

    renderCheckout();
</script>
</body>
</html>