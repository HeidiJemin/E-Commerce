<?php
include_once('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate incoming data
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    $total_price = filter_var($_POST['total_price'], FILTER_VALIDATE_FLOAT);
    $payment_id = mysqli_real_escape_string($con, $_POST['payment_id']);
    $payment_status = mysqli_real_escape_string($con, $_POST['payment_status']);
    $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
    $country = mysqli_real_escape_string($con, $_POST['country']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $zipcode = mysqli_real_escape_string($con, $_POST['zipcode']);
    $cart_items = json_decode($_POST['cart_items'], true);

    if (!$user_id || !$total_price || !$email || !$cart_items) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid data provided"]);
        exit;
    }

    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // Save Payment
        savePayment($con, $payment_id, $payment_status, $user_id, $total_price);

        // Save Order
        $order_id = saveOrder($con, $user_id, $firstname, $lastname, $country, $phone, $email, $city, $address, $zipcode, $total_price, $payment_id);

        // Save Order Items and Update Sizes
        foreach ($cart_items as $item) {
            saveOrderItem($con, $order_id, $item['produkt_id'], $item['item_name'], $item['size'], $item['quantity'], $item['price']);
            updateSizeAvailability($con, $item['produkt_id'], $item['size'], $item['quantity']);
        }

        // Clear the cart after the order is saved
        clearCart($con, $user_id);

        mysqli_commit($con); // Commit the transaction
        echo json_encode(["status" => "success", "order_id" => $order_id]);
    } catch (Exception $e) {
        mysqli_rollback($con); // Rollback transaction on failure
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
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
        INSERT INTO orders (user_id, firstname, lastname, country, phone, email, city, address, zipcode, total_price, payment_id, created_at) 
        VALUES ($user_id, '$firstname', '$lastname', '$country', '$phone', '$email', '$city', '$address', '$zipcode', $total_price, '$payment_id', NOW())";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Order insertion failed: " . mysqli_error($con));
    }

    return mysqli_insert_id($con); // Get and return the inserted order ID
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
?>
<?php
// Close the database connection
mysqli_close($con);
?>