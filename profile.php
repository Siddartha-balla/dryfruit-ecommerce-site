<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];
$name = isset($_SESSION['name']) ? $_SESSION['name'] : "Guest"; // Fix: Ensure 'name' is set
$date = isset($_SESSION['created_at']) ? $_SESSION['created_at'] : "Unknown"; // Fix: Ensure 'created_at' is set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style1.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fffdd0;
            color: #333;
        }

        header {
            background-color: #d2691e;
            color: #fff;
            padding: 15px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 28px;
        }

        main {
            padding: 20px;
            text-align: center;
        }

        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .profile-container p {
            font-size: 16px;
            margin: 10px 0;
        }

        .logout-btn {
            padding: 10px 15px;
            background-color: #d2691e;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: #b35916;
        }
    </style>
</head>
<body>
<header>
    <h1>User Profile</h1>
</header>

<main>
    <div class="profile-container">
        <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
        <p>Email:<?php echo htmlspecialchars($username); ?></p>
        <p>Member since:<?php echo htmlspecialchars($date); ?></p>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</main>
</body>
</html>