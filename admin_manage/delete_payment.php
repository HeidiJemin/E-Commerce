<?php
include('../includes/connect.php'); // Include your database connection

// Check if payment_id is set and not empty
if (isset($_POST['payment_id']) && !empty($_POST['payment_id'])) {
    $payment_id = intval($_POST['payment_id']); // Sanitize the payment_id to prevent SQL injection

    // Delete query
    $delete_query = "DELETE FROM `payments` WHERE payment_id = $payment_id";

    // Execute the query
    $result = mysqli_query($con, $delete_query);

    // Check if the query was successful
    if ($result) {
        // Return a JSON response indicating success
        echo json_encode(['success' => true, 'message' => 'Payment deleted successfully!']);
    } else {
        // Return a JSON response indicating failure
        echo json_encode(['success' => false, 'message' => 'Failed to delete the payment. Please try again.']);
    }
} else {
    // Return a JSON response indicating invalid or missing payment_id
    echo json_encode(['success' => false, 'message' => 'Invalid request. Payment ID is required.']);
}

// Close the MySQL connection
mysqli_close($con);
?>
