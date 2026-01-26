<?php
session_start();

// Database connection
$host = 'localhost'; // Database host
$db = 'dryfruits'; // Database name
$user = 'root'; // Database username (phpMyAdmin/XAMPP default)
$pass = ''; // Database password (XAMPP default is empty). Update if your phpMyAdmin uses a password.

$conn = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username and password are required.";
    } else {
        // Query to check user credentials and fetch the name and created_at
        $stmt = $conn->prepare("SELECT id, name, password, created_at FROM users WHERE email = ? OR mobile = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password (if stored as plain text, directly compare)
    if ($password === $user['password']) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['name'] = $user['name']; // Store the user's name in the session
        $_SESSION['created_at'] = $user['created_at']; // Store the account creation date in the session

        // Redirect to the home page
        $_SESSION['success_message'] = "User successfully logged in!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Invalid password.";
        header("Location: login.php"); // Redirect back to the login page
        exit;
    }
} else {
    $_SESSION['error'] = "User not found.";
    header("Location: login.php"); // Redirect back to the login page
    exit;
}

    $stmt->close();  

}
}
?>