<?php
session_start();
$host = 'localhost'; // Database host
$db = 'dryfruits'; // Database name
$user = 'root'; // Database username
$pass = 'siddu8374'; // Database password

// Connect to the database
$conn = new mysqli(
    "sql202.infinityfree.com",
    "if0_40879790",
    "Siddu8374",
    "if0_40879790_dryfruits"
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Validate name
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        echo "Invalid name. Name can only contain alphabets and spaces.";
        exit;
    }

    // Validate mobile number
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo "Invalid mobile number.";
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit;
    }

    // Validate password
    if ($password !== $confirmPassword) {
        echo "Passwords do not match.";
        exit;
    }

    // Save user to the database without hashing the password
    $stmt = $conn->prepare("INSERT INTO users (name, mobile, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $mobile, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User successfully registered! Now login with your credentials.";
        // alert("User successfully registered! Now login with your credentials.");
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>