<?php
session_start();
require("../../vendor/autoload.php");
require_once('../../includes/connect.php');
require '../../libraries/phpmailer/Exception.php';
require '../../libraries/phpmailer/PHPMailer.php';
require '../../libraries/phpmailer/SMTP.php';
require('../../libraries/fpdf/fpdf.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$GMAIL_ADDRESS = 'trendytrend2803@gmail.com';
$GMAIL_ADDRESS_PASSWORD = 'iwsc jlrv aoyc dbpa';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mysqli_close($con);
    http_response_code(405);
    die("Method not allowed.");
}


$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

if (!$data) {
    mysqli_close($con);
    http_response_code(400);
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
$payment_id = htmlspecialchars($data['payment_id'] ?? '');
$payment_status = htmlspecialchars($data['payment_status'] ?? '');
$payment_method = htmlspecialchars($data['payment_method'] ?? '');



try {
    mysqli_begin_transaction($con);

    
    savePayment($con, $payment_id, $payment_status, $user_id, $total_price, $payment_method);

    
    $order_id = saveOrder($con, $user_id, $firstname, $lastname, $country, $phone, $email, $city, $address, $zipcode, $total_price, $payment_id);

    
    foreach ($cart_items as $item) {
        saveOrderItem($con, $order_id, $item['produkt_id'], $item['item_name'], $item['size_id'], $item['quantity'], $item['price']);
        updateSizeAvailability($con, $item['produkt_id'], $item['size_id'], $item['quantity']);
    }

    
    clearCart($con, $user_id);

    mysqli_commit($con);


$shipping_fee = $total_price > 50 ? 0 : 10;

// Generate PDF invoice
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 20);


$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 10, 'Jersey Store - Invoice', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 10, 'Thank you for shopping with us!', 0, 1, 'C');
$pdf->Ln(10);


$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(50, 10, "Order ID:", 0, 0);
$pdf->Cell(0, 10, $order_id, 0, 1);
$pdf->Cell(50, 10, "Payment ID:", 0, 0);
$pdf->Cell(0, 10, $payment_id, 0, 1);
$pdf->Cell(50, 10, "Customer Name:", 0, 0);
$pdf->Cell(0, 10, "$firstname $lastname", 0, 1);
$pdf->Cell(50, 10, "Email:", 0, 0);
$pdf->Cell(0, 10, $email, 0, 1);
$pdf->Ln(10);


$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 10, 'Item Name', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Price', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Total', 1, 1, 'C', true);


$pdf->SetFont('Arial', '', 12);
foreach ($cart_items as $item) {
    $pdf->Cell(70, 10, $item['item_name'], 1);
    $pdf->Cell(30, 10, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, '$' . number_format($item['price'], 2), 1, 0, 'C');
    $pdf->Cell(30, 10, '$' . number_format($item['price'] * $item['quantity'], 2), 1, 1, 'C');
}


$pdf->SetFont('Arial', '', 12);

$pdf->Cell(130, 10, 'Shipping Fee', 1, 0, 'R');
$pdf->Cell(30, 10, '$' . number_format($shipping_fee, 2), 1, 1, 'C');



$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130, 10, 'Grand Total', 1, 0, 'R');
$pdf->Cell(30, 10, '$' . number_format($total_price, 2), 1, 1, 'C');


$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 10, 'For any queries, contact us at support@jerseystore.com.', 0, 1, 'C');


$filename = "invoice_$order_id.pdf";
$pdf->Output('F', $filename);



    
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth   = true;
    $mail->Username = $GMAIL_ADDRESS ; // Your email address
                $mail->Password = $GMAIL_ADDRESS_PASSWORD; // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('your-email@example.com', 'Jersey Store');
    $mail->addAddress($email, "$firstname $lastname");

    $mail->isHTML(true);
    $mail->Subject = 'Your Order Invoice';
    $mail->Body    = 'Thank you for your order! Please find your invoice attached.';
    $mail->addAttachment($filename);

    $mail->send();

    // Clean up generated PDF
    unlink($filename);

    
    echo json_encode(["status" => "success", "message" => "Order saved successfully."]);
    mysqli_close($con);
} catch (Exception $e) {
    mysqli_rollback($con);
    mysqli_close($con);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}


// save payment
function savePayment($con, $payment_id, $payment_status, $user_id, $total_price, $payment_method)
{
    

    $query = "
        INSERT INTO payments (payment_id, payment_status, user_id, total_price, payment_method, created_at) 
        VALUES ('$payment_id', '$payment_status', $user_id, $total_price, '$payment_method', NOW())";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Payment insertion failed: " . mysqli_error($con));
    }
}


// save order
function saveOrder($con, $user_id, $firstname, $lastname, $country, $phone, $email, $city, $address, $zipcode, $total_price, $payment_id)
{
    $query = "
        INSERT INTO orders (user_id, firstname, lastname, country, phone, email, city, address, zipcode, total_price, payment_id, created_at, status) 
        VALUES ($user_id, '$firstname', '$lastname', '$country', '$phone', '$email', '$city', '$address', '$zipcode', $total_price, '$payment_id', NOW(), 'PENDING')";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Order insertion failed: " . mysqli_error($con));
    }

    return mysqli_insert_id($con); // Return the new order ID
}

// Function to save order items
function saveOrderItem($con, $order_id, $produkt_id, $item_name, $size_id, $quantity, $price)
{
    $query = "
        INSERT INTO order_items (order_id, produkt_id, item_name, size_id, quantity, price) 
        VALUES ($order_id, $produkt_id, '$item_name', '$size_id', $quantity, $price)";

    if (!mysqli_query($con, $query)) {
        throw new Exception("Order item insertion failed: " . mysqli_error($con));
    }
}

function updateSizeAvailability($con, $produkt_id, $size_id, $quantity)
{
    
    $produkt_id = (int)$produkt_id;
    $quantity = (int)$quantity;
    $size = (int)$size_id;

    
    $query = "
        UPDATE sizes 
        SET stock = stock - $quantity 
        WHERE produkt_id = $produkt_id AND size_id = '$size_id'";

    
    error_log("Executing query: $query");

    
    if (!mysqli_query($con, $query)) {
        mysqli_close($con);
        error_log("MySQL error: " . mysqli_error($con)); // Log MySQL error
        throw new Exception("Size availability update failed: " . mysqli_error($con));
    }

    
    if (mysqli_affected_rows($con) == 0) {
        mysqli_close($con);
        throw new Exception("No rows updated. Verify that produkt_id: $produkt_id and size: '$size_id' exist in the database.");
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