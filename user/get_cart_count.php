<?php
session_start();
include_once('../includes/connect.php');
include_once('functions/common_function.php');

function getCartProductNumber2() {
    global $con;

    // Check if user is logged in
    if (!isset($_SESSION['id'])) {
        return 0; // Return 0 if not logged in
    }

    $user_id = $_SESSION['id'];
    $query = "SELECT * FROM cart WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $query);

    if ($result) {
        return mysqli_num_rows($result);  // Return the number of rows (cart items)
    } else {
        return 0;  // Return 0 if query fails
    }
}

echo getCartProductNumber2(); // Output the cart count


// Close the database connection
mysqli_close($con);
?>