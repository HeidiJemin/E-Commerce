<?php
require_once('../../includes/connect.php');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); 

$response = [
    'status' => 'error',
    'message' => 'Invalid request'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $query = "UPDATE orders SET status = 'COMPLETED' WHERE id = $order_id";
    if (mysqli_query($con, $query)) {
        $response['status'] = 'success';
        $response['message'] = 'Order status updated successfully';
    } else {
        $response['message'] = 'Database update failed';
    }
} else {
    $response['message'] = 'Invalid or missing parameters';
}

mysqli_close($con);
echo json_encode($response); 

?>