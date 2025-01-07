<?php
// Include database connection
include('./includes/connect.php');

// Check if order ID is passed through URL
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "<p>Invalid request. No order ID provided.</p>";
    exit;
}

// Optionally, you can fetch the order details here if needed
// $order_query = "SELECT * FROM orders WHERE order_id = $order_id";
// $order_result = mysqli_query($con, $order_query);
// $order = mysqli_fetch_assoc($order_result);

// For now, assuming order has been placed successfully if we have the order_id
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
            padding-top: 100px;
        }
        .success-container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 70%;
            max-width: 500px;
            margin: 0 auto;
        }
        h1 {
            color: #2ecc71;
        }
        p {
            font-size: 18px;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #3498db;
        }
    </style>
</head>
<body>

    <div class="success-container">
        <h1>Order Placed Successfully!</h1>
        <p>Your order has been successfully placed. Thank you for shopping with us!</p>
        <p>Order ID: <?= htmlspecialchars($order_id) ?></p>
        <a href="index.php" class="btn">Return to Home</a>
    </div>

</body>
</html>
