<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('../includes/connect.php');
// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Check if the cart ID is set
if (isset($_GET['cart_id'])) {
    

    $cart_id = $_GET['cart_id'];  // Get cart ID from the URL
    $user_id = $_SESSION['id'];   // Get user ID from session

    // Remove the item from the cart
    $delete_query = "DELETE FROM `cart` WHERE user_id = '$user_id' AND cart_item_id = '$cart_id'";
    $delete_result = mysqli_query($con, $delete_query);

    if ($delete_result) {
        // Redirect to the cart page after successful removal
        header("Location: cart.php");
        exit;
    } else {
        // Handle error (optional)
        header("Location: cart.php?error=1"); // Redirect with an error flag
        exit;
    }
} else {
    // Redirect to the cart page if no cart ID is provided
    header("Location: cart.php");
    exit;
}
?>
<?php
// Close the database connection
mysqli_close($con);
?>