<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../../includes/connect.php');

if (!isset($_SESSION['id'])) {
    header("Location: login.php"); 
    exit;
}


if (isset($_GET['cart_id'])) {
    

    $cart_id = $_GET['cart_id'];  
    $user_id = $_SESSION['id'];   

    
    $delete_query = "DELETE FROM `cart` WHERE user_id = '$user_id' AND cart_item_id = '$cart_id'";
    $delete_result = mysqli_query($con, $delete_query);

    if ($delete_result) {
    
        header("Location: ../cart.php");
        exit;
    } else {
        exit;
    }
} else {
   
    header("Location: ../cart.php");
    exit;
}
?>
<?php

mysqli_close($con);
?>