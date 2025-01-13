<?php
include('../includes/connect.php'); // Include your database connection

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']); // Sanitize the product_id to prevent SQL injection

    // Delete query
    $delete_product = "DELETE FROM `produkt` WHERE produkt_id = $product_id";
    $result_product = mysqli_query($con, $delete_product);

    // Return JSON response
    if ($result_product) {
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
mysqli_close($con);
?>


