<?php
include './database/dbconfig.php';

// Define the daily percentage (1.49%)
$dailyPercentage = 1.49 / 100; // 1.49%

// Fetch all user investments by matching the `id` field
$sql = "SELECT id, SUM(package_amount) AS total_investment 
        FROM investment 
        GROUP BY id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id'];
        $totalInvestment = $row['total_investment'];

        // Calculate 1.49% of the total investment for the user
        $dailyProfit = $totalInvestment * $dailyPercentage;

        // Update the user's balance and profit in the users table
        $updateSql = "UPDATE users 
                      SET balance = balance + ?, profit = profit + ? 
                      WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ddi", $dailyProfit, $dailyProfit, $userId);

        if ($stmt->execute()) {
            echo "Updated user ID $userId: +$dailyProfit added to balance and profit.\n";
        } else {
            echo "Error updating user ID $userId: " . $stmt->error . "\n";
        }

        $stmt->close();
    }
} else {
    echo "No active investments found.\n";
}

$conn->close();
?>
