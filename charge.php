<?php
session_start();
require __DIR__ . "/vendor/autoload.php";
include('./includes/connect.php'); // Database connection

$stripe_secret_key = "sk_test_51QfPWzD5IxD7HeGaK7wRCpyn40IfkKqtNfd0Cla2QtmkYq5zFuv7jox9deuGmaWcmOcNpV88mJiSKNXWsrWYEb8W00kFdRmDNK";

\Stripe\Stripe::setApiKey($stripe_secret_key);

// Fetch and validate form data
$required_fields = ['firstname', 'lastname', 'email', 'address', 'zipcode', 'country', 'city', 'user_id', 'total_price', 'cart_items', 'phone'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        die("Missing required field: $field.");
    }
}

// Sanitize input data
$firstname = htmlspecialchars($_POST['firstname']);
$lastname = htmlspecialchars($_POST['lastname']);
$email = htmlspecialchars($_POST['email']);
$address = htmlspecialchars($_POST['address']);
$zipcode = htmlspecialchars($_POST['zipcode']);
$country = htmlspecialchars($_POST['country']);
$city = htmlspecialchars($_POST['city']);
$phone = htmlspecialchars($_POST['phone']);
$user_id = htmlspecialchars($_POST['user_id']);
$total_price = (float) $_POST['total_price'];
$cart_items = json_decode($_POST['cart_items'], true);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die("Invalid email address.");
}

// Store session data
$_SESSION['firstname'] = $firstname;
$_SESSION['lastname'] = $lastname;
$_SESSION['email'] = $email;
$_SESSION['address'] = $address;
$_SESSION['zipcode'] = $zipcode;
$_SESSION['country'] = $country;
$_SESSION['city'] = $city;
$_SESSION['phone'] = $phone;
$_SESSION['total_price'] = $total_price;
$_SESSION['cart_items'] = $cart_items;

// Prepare line items for Stripe
$line_items = [];
foreach ($cart_items as $item) {
    $line_items[] = [
        "quantity" => max(1, (int) $item["quantity"]),
        "price_data" => [
            "currency" => "usd",
            "unit_amount" => (int) ($item["price"] * 100), // Convert price to cents
            "product_data" => [
                "name" => htmlspecialchars($item["item_name"]) . " (" . htmlspecialchars($item["size"]) . ")",
            ]
        ]
    ];
}

// Create Stripe Checkout session
try {
    $checkout_session = \Stripe\Checkout\Session::create([
        "mode" => "payment",
        "success_url" => "http://localhost/Ecom/success.php?session_id={CHECKOUT_SESSION_ID}",
        "cancel_url" => "http://localhost/cart.php",
        "locale" => "auto",
        "line_items" => $line_items,
        "payment_intent_data" => [
            "metadata" => [
                "user_id" => $user_id,
                "total_price" => $total_price
            ]
        ],
        "customer_email" => $email,
    ]);

    // Redirect to Stripe Checkout
    header("Location: " . $checkout_session->url);
    exit();
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log("Stripe API error: " . $e->getMessage());
    http_response_code(500);
    die("Error creating Stripe Checkout session.");
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    die("An error occurred while processing your request.");
}
?>
