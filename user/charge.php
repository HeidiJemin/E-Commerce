<?php
session_start();
require __DIR__ . "/../vendor/autoload.php";

include_once('../includes/connect.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirect to cart.php if the request method is not POST
    header("Location: cart.php");
    exit; // Stop further execution
}
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

// Check stock availability
foreach ($cart_items as $item) {
    $size = htmlspecialchars($item["size"]);
    $produkt_id = (int) $item["produkt_id"];
    $quantity_requested = max(1, (int) $item["quantity"]);

    // Query to check stock in the database
    $query = "SELECT stock FROM sizes WHERE size = '$size' AND produkt_id = $produkt_id";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $available_stock = (int) $row['stock'];

        // Check if requested quantity exceeds available stock
        if ($quantity_requested > $available_stock) {
            // Set a session variable to indicate the stock issue
            $_SESSION['stock_error'] = "Stock unavailable for product ID: $produkt_id, Size: $size. Available stock: $available_stock.";
            
            // Redirect the user back to cart.php
            header("Location: cart.php");
            exit();
        }
    } else {
        // Set a session variable to indicate an invalid product/size error
        $_SESSION['stock_error'] = "Invalid product or size for product ID: $produkt_id, Size: $size.";

        // Redirect the user back to cart.php
        header("Location: cart.php");
        exit();
    }
}

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
$line_items[] = [
    "quantity" => 1,
    "price_data" => [
        "currency" => "usd",
        "unit_amount" => 1000, // $10 in cents
        "product_data" => [
            "name" => "Shipping Fee",
        ]
    ]
];

// Optional: Store session data if needed on another page
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

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        "mode" => "payment",
        "success_url" => "http://localhost/Ecom/user/success.php?session_id={CHECKOUT_SESSION_ID}",
        "cancel_url" => "http://localhost/Ecom/user/cancel.php",
        "locale" => "auto",
        "line_items" => $line_items, // Includes the shipping fee
        "payment_intent_data" => [
            "metadata" => [
                "user_id" => $user_id,
                "total_price" => $total_price + 10 // Include the shipping fee in metadata
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


mysqli_close($con);
?>
