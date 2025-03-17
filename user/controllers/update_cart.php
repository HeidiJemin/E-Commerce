<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../includes/connect.php');
global $con;

if (isset($_POST['size_id']) && isset($_POST['quantity'])) {
    $size_id = (int)$_POST['size_id'];
    $quantity = (int)$_POST['quantity'];

    
    if (isset($_SESSION['id'])) {
        $user_id = $_SESSION['id'];

        
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

            
            if ($quantity > $stock) {
                echo "error"; 
                mysqli_close($con);
                exit();
            }

            
            $update_query = "
                UPDATE `cart` 
                SET quantity = '$quantity' 
                WHERE user_id = '$user_id' AND size_id = '$size_id'
            ";
            $update_result = mysqli_query($con, $update_query);

            if ($update_result) {
                
                $new_total_price = $product_price * $quantity;

                
                echo number_format($new_total_price, 2);
                mysqli_close($con);
            } else {
                mysqli_close($con);
                echo "error"; 
            }
        } else {
            mysqli_close($con);
            echo "error"; 
        }
    } else {
        mysqli_close($con);
        echo "error"; 
    }
} else {
    mysqli_close($con);
    echo "error"; 
}

?>
