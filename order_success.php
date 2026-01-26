<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Calculate expiry time (3 hours from now)
date_default_timezone_set('Asia/Kolkata');
$expiry_time = date('Y-m-d H:i:s', strtotime('+3 hours'));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
            background-color: #f0fff0;
        }
        .success-box {
            display: inline-block;
            padding: 30px;
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
            border-radius: 10px;
        }
        .success-box h2 {
            margin: 0;
        }
        a {
            display: block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="success-box">
    <h2>🎉 Order Placed Successfully!</h2>
    <p>Thank you for your purchase.</p>
    <p><strong>Note:</strong> Please collect your order before <strong><?php echo $expiry_time; ?></strong>.</p>
    <a href="index.php">← Back to Home</a>
</div>

</body>
</html>