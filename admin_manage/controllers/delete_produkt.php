<?php
require_once('../../includes/connect.php');

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    
    $check_order_items_query = "SELECT COUNT(*) as count FROM `order_items` WHERE `produkt_id` = $product_id";
    $result_check = mysqli_query($con, $check_order_items_query);
    
    if ($result_check) {
        $row = mysqli_fetch_assoc($result_check);
        $count = $row['count'];

        if ($count > 0) {
            
            echo json_encode(['success' => false, 'message' => 'Data integrity at risk! This product is referenced in existing orders.']);
        } else {
            
            $delete_product = "DELETE FROM `produkt` WHERE `produkt_id` = $product_id";
            $result_product = mysqli_query($con, $delete_product);

            if ($result_product) {
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully!']);
            } else {
                
                $error_message = mysqli_error($con);

                
                if (strpos($error_message, 'foreign key constraint') !== false) {
                    echo json_encode(['success' => false, 'message' => 'Data integrity at risk! This product is referenced in existing orders.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete product. Please try again.']);
                }
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to check for product references. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

mysqli_close($con);
?>
