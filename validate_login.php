<?php
header('Content-Type: application/json');

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// Hardcoded credentials
$validUsername = 'Dryfruits_01';
$validPassword = '12#987654';

// Validate credentials
if ($username === $validUsername && $password === $validPassword) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>