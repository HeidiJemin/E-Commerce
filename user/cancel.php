<?php
session_start();
require __DIR__ . "../vendor/autoload.php";
include_once('../includes/connect.php');

// Stripe API initialization
$stripe_secret_key = "sk_test_51QfPWzD5IxD7HeGaK7wRCpyn40IfkKqtNfd0Cla2QtmkYq5zFuv7jox9deuGmaWcmOcNpV88mJiSKNXWsrWYEb8W00kFdRmDNK";
\Stripe\Stripe::setApiKey($stripe_secret_key);

// Check if session_id is provided
if (isset($_GET['session_id'])) {
    $session_id = htmlspecialchars($_GET['session_id']);

    try {
        // Retrieve the Stripe Checkout session
        $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);

        // Extract relevant details
        $user_id = $checkout_session->metadata->user_id ?? null; // Website user ID passed in metadata
        $stripe_customer_id = $checkout_session->customer ?? null; // Stripe customer ID
        $total_price = $checkout_session->metadata->total_price ?? 0; // Total price from metadata

        // Prepare JSON data for the `event_data` field
        $event_data = json_encode([
            'total_price' => $total_price,
            'status' => 'cancelled',
            'stripe_customer_id' => $stripe_customer_id,
        ], JSON_UNESCAPED_SLASHES);

        // Dynamic SQL query (less secure, avoid for production)
        $query = "
            INSERT INTO stripe_logs (session_id, user_id, event_type, event_data, created_at)
            VALUES ('$session_id', '$user_id', 'checkout.session.cancelled', '$event_data', NOW())
        ";

        // Execute the query
        if (mysqli_query($con, $query)) {
            // Log success
            error_log("Cancellation logged successfully for session ID: $session_id");
        } else {
            // Log database error
            error_log("Database error: " . mysqli_error($con));
        }
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle Stripe API errors
        error_log("Stripe API error: " . $e->getMessage());
    } catch (Exception $e) {
        // Handle general errors
        error_log("General error: " . $e->getMessage());
    }
} else {
    error_log("No session_id provided in the request.");
}

// Redirect or display a cancellation message
echo "Your payment has been cancelled. If this was an error, please try again.";
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Canceled</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For icons -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            font-size: 2.5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .order-info {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 20px;
        }
        .btn {
            padding: 12px 25px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #c82333;
        }
        .footer {
            font-size: 1rem;
            margin-top: 40px;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="icon">
        <i class="fas fa-times-circle"></i>
    </div>
    <div class="header">
        Payment Canceled!
    </div>
    <div class="order-info">
        <p>Your payment was not completed.</p>
        <p>If you have any questions, please contact support.</p>
        <p>We hope to see you again soon!</p>
    </div>
    <a href="index.php" class="btn">Go to Homepage</a>
    <a href="cart.php" class="btn" style="margin-left: 10px;">Return to Cart</a>
</div>

</body>
</html>
