<?php

// Start the session and connect to the database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('../includes/connect.php');
global $con;

if (isset($_POST['size_id']) && isset($_POST['quantity'])) {
    $size_id = (int)$_POST['size_id'];
    $quantity = (int)$_POST['quantity'];

    // Ensure user is logged in
    if (isset($_SESSION['id'])) {
        $user_id = $_SESSION['id'];

        // Fetch product price and stock for the given size
        $query = "
            SELECT p.produkt_price, s.stock 
            FROM sizes s 
            JOIN produkt p ON s.produkt_id = p.produkt_id 
            WHERE s.size_id = '$size_id'
        ";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $product_price = $row['produkt_price'];
            $stock = $row['stock'];

            // Ensure the requested quantity does not exceed the available stock
            if ($quantity > $stock) {
                echo "error"; // Quantity exceeds stock
                exit();
            }

            // Update the cart with the new quantity
            $update_query = "
                UPDATE `cart` 
                SET quantity = '$quantity' 
                WHERE user_id = '$user_id' AND size_id = '$size_id'
            ";
            $update_result = mysqli_query($con, $update_query);

            if ($update_result) {
                // Calculate the new total price for the size
                $new_total_price = $product_price * $quantity;

                // Return the updated price
                echo number_format($new_total_price, 2);
            } else {
                echo "error"; // Failed to update cart
            }
        } else {
            echo "error"; // Invalid size_id
        }
    } else {
        echo "error"; // User not logged in
    }
} else {
    echo "error"; // Missing parameters
}

?>
<?php
// Close the database connection
mysqli_close($con);
?>