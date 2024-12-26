<?php
include('../includes/connect.php');

function deleteUser($user_id) {
    global $con; // Use the global database connection

    $user_id = intval($user_id); // Sanitize input

    // Ensure the connection is valid
    if (!$con) {
        http_response_code(500);
        return "Database connection failed.";
    }

    $query = "DELETE FROM users WHERE user_id = $user_id";

    if (mysqli_query($con, $query)) {
        return "User deleted successfully.";
    } else {
        http_response_code(500);
        return "Error deleting user.";
    }
}

// Check if the request method and data are correct
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Return the result of the delete function
    echo deleteUser($user_id);
} else {
    http_response_code(400); // Bad request
    echo "Invalid request.";
}

mysqli_close($con);
?>
