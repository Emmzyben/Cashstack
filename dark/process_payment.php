<?php
session_start();
include '../database/dbconfig.php'; 
// Check if the user is logged in
if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
} else {
    header('Location: ../index.php');
    exit(); 
}

// Get payment data from the frontend
$data = json_decode(file_get_contents('php://input'), true);
$tx_ref = $data['tx_ref'];
$amount = $data['amount'];
$email = $data['customer_email'];
$phone = $data['customer_phone'];

// Fetch the API key from the database securely
$sql = "SELECT apiKey FROM admin";  
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$apiDetails = $result->fetch_assoc();

if ($apiDetails) {
    // Return API key securely (but not to be exposed directly in the frontend)
    echo json_encode([
        'status' => 'success',
        'apiKey' => $apiDetails['apiKey'],
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'API key not found']);
}

$stmt->close();
$conn->close();
?>
