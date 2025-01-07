<?php
include('includes/connect.php');

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    // Sanitize and fetch the order_id
    $order_id = intval($_POST['order_id']);

    // Update the order status to COMPLETED using mysqli functions
    $query = "UPDATE orders SET status = 'COMPLETED' WHERE id = $order_id";

    if (mysqli_query($con, $query)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'invalid_request';
}
?>
