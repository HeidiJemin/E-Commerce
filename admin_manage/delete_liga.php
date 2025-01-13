<?php
include('../includes/connect.php'); // Include your database connection

if (isset($_POST['liga_id'])) {
    $liga_id = intval($_POST['liga_id']); // Sanitize the liga_id to prevent SQL injection

    // Delete query
    $delete_query = "DELETE FROM `liga` WHERE liga_id = $liga_id";
    $result = mysqli_query($con, $delete_query);

    // Return JSON response
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Liga deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete Liga. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
mysqli_close($con);
?>