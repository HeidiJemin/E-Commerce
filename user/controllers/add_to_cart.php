<?php

session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');



require_once('../../includes/connect.php');


if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You need to log in to add products to the cart']);
    exit;
}
if (empty($_POST['produkt_id']) || empty($_POST['size_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data received']);
    exit;
}

$user_id = $_SESSION['id'];


if (isset($_POST['produkt_id']) && isset($_POST['size_id'])) {
    $produkt_id = mysqli_real_escape_string($con, $_POST['produkt_id']);
    
    $size_id = mysqli_real_escape_string($con, $_POST['size_id']);

    
    $check_query = "SELECT * FROM `cart` WHERE user_id = '$user_id' AND produkt_id = '$produkt_id' AND size_id = '$size_id'";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This product with the selected size is already in your cart']);
    } else {
        
        $insert_query = "INSERT INTO `cart` (user_id, produkt_id, size_id, quantity) VALUES ('$user_id', '$produkt_id', '$size_id', 1)";
        $insert_result = mysqli_query($con, $insert_query);

        if ($insert_result) {
            echo json_encode(['status' => 'success', 'message' => 'Product added to your cart successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add product to the cart']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
mysqli_close($con);
?>
