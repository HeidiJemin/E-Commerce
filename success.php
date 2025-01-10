<?php
session_start();
require __DIR__ . "/vendor/autoload.php";
include('./includes/connect.php'); // Database connection

\Stripe\Stripe::setApiKey("sk_test_51QfPWzD5IxD7HeGaK7wRCpyn40IfkKqtNfd0Cla2QtmkYq5zFuv7jox9deuGmaWcmOcNpV88mJiSKNXWsrWYEb8W00kFdRmDNK"); // Your Stripe Secret Key

// Get session_id from the URL
$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    http_response_code(400);
    die("Invalid session ID.");
}

try {
    // Retrieve Stripe checkout session details
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    $payment_intent_id = $session->payment_intent;

    // Retrieve payment intent details
    $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
    $payment_status = $payment_intent->status;
    $payment_id = $payment_intent->id;

    // Fetch user and order details from session
    $user_id = $_SESSION['id'] ?? null;
    $firstname = $_SESSION['firstname'] ?? null;
    $lastname = $_SESSION['lastname'] ?? null;
    $country = $_SESSION['country'] ?? null;
    $phone = $_SESSION['phone'] ?? null;
    $email = $session->customer_details->email ?? null;
    $city = $_SESSION['city'] ?? null;
    $address = $_SESSION['address'] ?? null;
    $zipcode = $_SESSION['zipcode'] ?? null;
    $total_price = $session->amount_total / 100; // Convert from cents
    $cart_items = $_SESSION['cart_items'] ?? null;

    // Validate required data
    if (!$user_id || !$firstname || !$lastname || !$email || !$total_price || !$cart_items) {
        http_response_code(400);
        die("Missing or invalid session data.");
    }

    // Start a database transaction
    mysqli_begin_transaction($con);

    try {
        // Save payment
        savePayment($con, $payment_id, $payment_status, $user_id, $total_price);

        // Save order and get the order ID
        $order_id = saveOrder($con, $user_id, $firstname, $lastname, $country, $phone, $email, $city, $address, $zipcode, $total_price, $payment_id);

        // Save order items and update inventory
        foreach ($cart_items as $item) {
            saveOrderItem($con, $order_id, $item['produkt_id'], $item['item_name'], $item['size'], $item['quantity'], $item['price']);
            updateSizeAvailability($con, $item['produkt_id'], $item['size'], $item['quantity']);
        }

        // Clear the user's cart
        clearCart($con, $user_id);

        mysqli_commit($con); // Commit transaction

        // Display success page
        displaySuccessPage($order_id, $total_price);
    } catch (Exception $e) {
        mysqli_rollback($con); // Rollback transaction if an error occurs
        http_response_code(500);
        die("Error processing your order: " . $e->getMessage());
    }
} catch (\Exception $e) {
    http_response_code(500);
    die("Error retrieving payment details: " . $e->getMessage());
}

// Function to save payment
function savePayment($con, $payment_id, $payment_status, $user_id, $total_price)
{
    $query = "
        INSERT INTO payments (payment_id, payment_status, user_id, total_price, created_at) 
        VALUES ('$payment_id', '$payment_status', $user_id, $total_price, NOW())";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Payment insertion failed: " . mysqli_error($con));
    }
}

// Function to save order
function saveOrder($con, $user_id, $firstname, $lastname, $country, $phone, $email, $city, $address, $zipcode, $total_price, $payment_id)
{
    $query = "
        INSERT INTO orders (user_id, firstname, lastname, country, phone, email, city, address, zipcode, total_price, payment_id, created_at,status) 
        VALUES ($user_id, '$firstname', '$lastname', '$country', '$phone', '$email', '$city', '$address', '$zipcode', $total_price, '$payment_id', NOW(), 'pending')";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Order insertion failed: " . mysqli_error($con));
    }

    return mysqli_insert_id($con); // Return the new order ID
}

// Function to save order items
function saveOrderItem($con, $order_id, $produkt_id, $item_name, $size, $quantity, $price)
{
    $query = "
        INSERT INTO order_items (order_id, produkt_id, item_name, size, quantity, price) 
        VALUES ($order_id, $produkt_id, '$item_name', '$size', $quantity, $price)";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Order item insertion failed: " . mysqli_error($con));
    }
}

// Function to update size availability
function updateSizeAvailability($con, $produkt_id, $size, $quantity)
{
    $query = "
        UPDATE sizes 
        SET stock = stock - $quantity 
        WHERE produkt_id = $produkt_id AND size = '$size'";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Size availability update failed: " . mysqli_error($con));
    }
}

// Function to clear the cart
function clearCart($con, $user_id)
{
    $query = "
        DELETE FROM cart WHERE user_id = $user_id";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Cart clearing failed: " . mysqli_error($con));
    }
}

// Function to display the success page
function displaySuccessPage($order_id, $total_price)
{
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Success - Order Placed</title>
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
                color: #28a745;
                margin-bottom: 20px;
            }
            .icon {
                font-size: 5rem;
                color: #28a745;
                margin-bottom: 20px;
            }
            .order-info {
                font-size: 1.2rem;
                color: #333;
                margin-bottom: 20px;
            }
            .order-info span {
                font-weight: bold;
            }
            .btn {
                padding: 12px 25px;
                background-color: #28a745;
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 1.2rem;
                cursor: pointer;
                text-decoration: none;
            }
            .btn:hover {
                background-color: #218838;
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
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="header">
            Payment Successful!
        </div>
        <div class="order-info">
            <p>Your payment has been processed successfully.</p>
            <p>Order ID: <span><?php echo htmlspecialchars($order_id); ?></span></p>
            <p>Total Amount: <span>$<?php echo htmlspecialchars($total_price); ?></span></p>
            <p>Thank you for your purchase!</p>
        </div>
        <a href="index.php" class="btn">Go to Homepage</a>
        <a href="order_history.php" class="btn" style="margin-left: 10px;">View Order History</a>
    </div>
    <div class="footer">
        <p>&copy; 2025 Your Website Name. All rights reserved.</p>
    </div>
    </body>
    </html>
    <?php
}
?>
