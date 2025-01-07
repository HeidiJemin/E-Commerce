<?php
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
  
    $order_id = (int)$_POST['order_id'];  

    
    mysqli_query($con, "START TRANSACTION");

    try {
       
        $delete_items_query = "DELETE FROM order_items WHERE order_id = $order_id";
        if (mysqli_query($con, $delete_items_query) === false) {
            throw new Exception("Failed to delete order items.");
        }

       
        $delete_order_query = "DELETE FROM orders WHERE id = $order_id";
        if (mysqli_query($con, $delete_order_query) === false) {
            throw new Exception("Failed to delete the order.");
        }

        
        mysqli_query($con, "COMMIT");

        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        
        mysqli_query($con, "ROLLBACK");

        
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}


mysqli_close($con);
?>
