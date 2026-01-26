<?php
session_start();

// Check if the user is already logged in
if (!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true)) {
    header('Location: shoplogin.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopkeeper Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .logout-btn {
            float: right;
            background-color: #e74c3c;
            color: #fff !important;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            margin-top: -8px;
            margin-right: 10px;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6fb;
            color: #333;
        }

        header {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        nav {
            background-color: #34495e;
            padding: 12px 0;
            text-align: center;
        }

        nav a {
            color: #ecf0f1;
            margin: 0 18px;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #1abc9c;
        }

        main {
            padding: 30px 20px;
            max-width: 1200px;
            margin: auto;
        }

        section {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s;
        }

        section:hover {
            transform: scale(1.01);
        }

        h2 {
            margin-top: 0;
            font-size: 22px;
            color: #2c3e50;
        }

        button {
            background-color: #1abc9c;
            color: #fff;
            border: none;
            padding: 10px 18px;
            margin: 10px 10px 10px 0;
            cursor: pointer;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #16a085;
        }

        .close-btn {
            float: right;
            background-color: #e74c3c;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            display: none;
        }

        .close-btn:hover {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        table th {
            background-color: #ecf0f1;
            color: #2c3e50;
        }

        .no-products,
        .no-orders {
            text-align: center;
            color: #7f8c8d;
            margin-top: 20px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
        }
        
    </style>
</head>

<body>
    <header>Shopkeeper Dashboard</header>
    <a href="logout.php" class="logout-btn">Logout</a>
    <main>
        <section id="manage-products">
            <h2>Manage Products</h2>
            <button onclick="addProduct()">Add Product</button>
            <button class="close-btn" onclick="closeProductForm()" style="display: none;"><i
                    class="fa-solid fa-xmark"></i> Close</button>
            <div id="product-form-container"></div>
        </section>

        <section id="view-orders">
            <h2>View Orders</h2>
            <button onclick="viewOrders()">View Orders</button>
            <button class="close-btn" onclick="closeOrders()"><i class="fa-solid fa-xmark"></i> Close</button>
            <div id="orders-container"></div>
        </section>


        <section id="items-freezed">
            <h2>Items Freezed</h2>
            <button onclick="viewFreezedItems()">View Freezed Items</button>
            <button class="close-btn" onclick="closeFreezedItems()" style="display: none;"><i
                    class="fa-solid fa-xmark"></i> Close</button>
            <div id="freezed-items-container"></div>
        </section>
        <section id="view-stock">
            <h2>View and Update Stock</h2>
            <button onclick="viewStock()">View Stock</button>
            <button class="close-btn" onclick="closeStock()"><i class="fa-solid fa-xmark"></i> Close</button>
            <button class="refresh-btn" onclick="refreshStock()">Refresh</button>
            <div id="stock-container"></div>
        </section>

    </main>

    <script>
        function refreshStock() {
            if (!confirm('Are you sure you want to refresh the stock? This will update quantities for expired orders.')) {
                return;
            }

            fetch('refresh_stock.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        viewStock(); // Refresh the stock table
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing stock:', error);
                    alert('Failed to refresh stock. Please try again.');
                });
        }
        function viewFreezedItems() {
            fetch('freezed_items.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('freezed-items-container');
                    const closeBtn = document.querySelector('#items-freezed .close-btn');
                    container.innerHTML = '';
                    closeBtn.style.display = 'inline-block';

                    if (!data.success || data.data.length === 0) {
                        container.innerHTML = '<p class="no-products">No freezed items available.</p>';
                        return;
                    }

                    let table = `
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Freezed Quantity (g)</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                    data.data.forEach(item => {
                        table += `
                    <tr>
                        <td>${item.product_id}</td>
                        <td>${item.product_name}</td>
                        <td>${item.freezed_quantity}</td>
                    </tr>
                `;
                    });

                    table += '</tbody></table>';
                    container.innerHTML = table;
                })
                .catch(error => {
                    console.error('Error fetching freezed items:', error);
                    alert('Failed to fetch freezed items.');
                });
        }

        function closeFreezedItems() {
            document.getElementById('freezed-items-container').innerHTML = '';
            document.querySelector('#items-freezed .close-btn').style.display = 'none';
        }
        function addProduct() {
            const container = document.getElementById('product-form-container');
            const closeBtn = document.querySelector('#manage-products .close-btn');

            // Prevent adding multiple forms
            if (document.getElementById('productForm')) return;

            container.innerHTML = `
        <form id="productForm" style="margin-top: 20px;" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="nuts">Nuts</option>
                <option value="raisins">Raisins</option>
                <option value="dates">Dates</option>
                <option value="seeds">Seeds</option>
            </select>
            <input type="number" name="price" placeholder="Price (₹)" required>
            <input type="number" name="stock" placeholder="Stock (grams)" required>
            <label>Upload Product Image:</label>
            <input type="file" name="image" accept="image/*" required>
            <textarea name="description" placeholder="Product Description"></textarea>
            <button type="submit">Submit Product</button>
        </form>
    `;

            closeBtn.style.display = 'inline-block'; // Show the close button

            const form = document.getElementById('productForm');
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);

                fetch('add_product.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Product added successfully!');
                            form.remove();
                            closeBtn.style.display = 'none'; // Hide the close button
                        } else {
                            alert('Failed to add product: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error adding product:', error);
                        alert('Error adding product.');
                    });
            });
        }

        function closeProductForm() {
            const container = document.getElementById('product-form-container');
            const closeBtn = document.querySelector('#manage-products .close-btn');
            container.innerHTML = ''; // Clear the form content
            closeBtn.style.display = 'none'; // Hide the close button
        }

        function viewOrders() {
            fetch('view_orders.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('orders-container');
                    const closeBtn = document.querySelector('#view-orders .close-btn');
                    container.innerHTML = '';
                    closeBtn.style.display = 'inline-block';

                    if (data.length === 0) {
                        container.innerHTML = '<p class="no-orders">No active orders available.</p>';
                        return;
                    }

                    let table = `
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>User ID</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Expiry</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.forEach(order => {
                        let items = order.items.map(item => `
                            <div><strong>${item.product_name}</strong> - ${item.quantity}g - ₹${((item.price / 1000) * item.quantity).toFixed(2)}</div>
                        `).join('');

                        table += `
                            <tr>
                                <td>${order.order_id}</td>
                                <td>${order.user_id}</td>
                                <td>₹${order.total_amount}</td>
                                <td>${order.order_date}</td>
                                <td>${order.expiry_time}</td>
                                <td>${items}</td>
                                <td>
                                    <select id="status-${order.order_id}">
                                        <option value="Pending" ${order.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                        <option value="Completed" ${order.status === 'Completed' ? 'selected' : ''}>Completed</option>
                                        <option value="Cancelled" ${order.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                                    </select>
                                    <button onclick="updateOrderStatus(${order.order_id})">Save</button>
                                </td>
                            </tr>
                        `;
                    });

                    table += '</tbody></table>';
                    container.innerHTML = table;
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
                    alert('Failed to fetch orders.');
                });
        }

        function updateOrderStatus(orderId) {
            const newStatus = document.getElementById(`status-${orderId}`).value;

            fetch('update_order_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order status updated!');
                        viewOrders();
                    } else {
                        alert('Failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Could not update order status.');
                });
        }

        function closeOrders() {
            document.getElementById('orders-container').innerHTML = '';
            document.querySelector('#view-orders .close-btn').style.display = 'none';
        }

        function viewStock() {
            fetch('view_stock.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('stock-container');
                    const closeBtn = document.querySelector('#view-stock .close-btn');
                    container.innerHTML = '';
                    closeBtn.style.display = 'inline-block';

                    if (data.length === 0) {
                        container.innerHTML = '<p class="no-products">No products available.</p>';
                        return;
                    }

                    let table = `
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Stock (g)</th>
                            <th>Price (₹)</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                    data.forEach(product => {
                        table += `
                    <tr>
                        <td>${product.product_id}</td>
                        <td>${product.name}</td>
                        <td>
                            <input type="number" id="stock-${product.product_id}" value="${product.stock}" min="0">
                        </td>
                        <td>
                            <input type="number" id="price-${product.product_id}" value="${product.price}" min="0" step="0.01">
                        </td>
                        <td>
                            <button onclick="updateStock(${product.product_id})">Update</button>
                        </td>
                    </tr>
                `;
                    });

                    table += '</tbody></table>';
                    container.innerHTML = table;
                })
                .catch(error => {
                    console.error('Error fetching stock:', error);
                    alert('Failed to fetch stock.');
                });
        }

        function updateStock(productId) {
            const newStock = document.getElementById(`stock-${productId}`).value;
            const newPrice = document.getElementById(`price-${productId}`).value;

            fetch('update_stock.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, stock: newStock, price: newPrice })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Stock and price updated!');
                        viewStock();
                    } else {
                        alert('Update failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating stock and price:', error);
                    alert('Failed to update stock and price.');
                });
        }

        function closeStock() {
            document.getElementById('stock-container').innerHTML = '';
            document.querySelector('#view-stock .close-btn').style.display = 'none';
        }
    </script>
</body>
</html>