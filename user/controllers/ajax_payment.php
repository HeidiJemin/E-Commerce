<?php
session_start();
require("../../vendor/autoload.php");
include_once('../../includes/connect.php');

// Stripe secret key
$stripe_secret_key = "sk_test_51QfPWzD5IxD7HeGaK7wRCpyn40IfkKqtNfd0Cla2QtmkYq5zFuv7jox9deuGmaWcmOcNpV88mJiSKNXWsrWYEb8W00kFdRmDNK";
\Stripe\Stripe::setApiKey($stripe_secret_key);


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    mysqli_close($con);
    die("Method not allowed.");
}


$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

if (!$data) {
    http_response_code(400);
    mysqli_close($con);
    die("Invalid input data.");
}


$firstname = htmlspecialchars($data['firstname'] ?? '');
$lastname = htmlspecialchars($data['lastname'] ?? '');
$email = htmlspecialchars($data['email'] ?? '');
$address = htmlspecialchars($data['address'] ?? '');
$zipcode = htmlspecialchars($data['zipcode'] ?? '');
$country = htmlspecialchars($data['country'] ?? '');
$city = htmlspecialchars($data['city'] ?? '');
$phone = htmlspecialchars($data['phone'] ?? '');
$user_id = htmlspecialchars($data['user_id'] ?? '');
$total_price = isset($data['total_price']) ? (float)$data['total_price'] : 0;
$cart_items = isset($data['cart_items']) ? json_decode(json_encode($data['cart_items']), true) : [];


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    mysqli_close($con);
    die("Invalid email address.");
}


if (!$firstname || !$lastname || !$total_price || empty($cart_items)) {
    http_response_code(400);
    mysqli_close($con);
    die("Missing required fields.");
}

// STOCK CHECK
foreach ($cart_items as $item) {
    $size_id = htmlspecialchars($item["size_id"]);
    $produkt_id = (int)$item["produkt_id"];
    $quantity_requested = max(1, (int)$item["quantity"]);

    $query = "SELECT stock FROM sizes WHERE size_id = '$size_id' AND produkt_id = $produkt_id";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $available_stock = (int)$row['stock'];

        if ($quantity_requested > $available_stock) {
            $response['error'] = "Stock unavailable for product ID: $produkt_id, Size: $size_id. Available stock: $available_stock.";
            http_response_code(400);
            echo json_encode($response);
            mysqli_close($con);
            exit;
        }
    } else {
        $response['error'] = "Invalid product or size for product ID: $produkt_id, Size: $size_id.";
        http_response_code(400);
        echo json_encode($response);
        mysqli_close($con);
        exit;
    }
}

// Prepare payment intent
try {
    $payment_intent = \Stripe\PaymentIntent::create([
        "amount" => $total_price * 100, // Convert to cents
        "currency" => "usd",
        "payment_method_types" => ["card"],
        "metadata" => [
            "user_id" => $user_id,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "email" => $email,
        ]
    ]);
    mysqli_close($con);
    // Respond with the client secret
    echo json_encode([
        "clientSecret" => $payment_intent->client_secret
    ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log("Stripe API error: " . $e->getMessage());
    mysqli_close($con);
    http_response_code(500);
    die("Error creating payment intent.");
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    mysqli_close($con);
    http_response_code(500);
    die("An error occurred.");
}
?>
