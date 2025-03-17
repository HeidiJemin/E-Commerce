<?php
require_once('../../includes/connect.php');


if (isset($_POST['payment_id']) && !empty($_POST['payment_id'])) {
    $payment_id = intval($_POST['payment_id']); 

    // Delete query
    $delete_query = "DELETE FROM `payments` WHERE payment_id = $payment_id";

    
    $result = mysqli_query($con, $delete_query);

    
    if ($result) {
        
        echo json_encode(['success' => true, 'message' => 'Payment deleted successfully!']);
    } else {
        
        echo json_encode(['success' => false, 'message' => 'Failed to delete the payment. Please try again.']);
    }
} else {
    
    echo json_encode(['success' => false, 'message' => 'Invalid request. Payment ID is required.']);
}


mysqli_close($con);
?>
