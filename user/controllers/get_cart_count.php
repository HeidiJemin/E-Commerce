<?php
session_start();
require_once('../../includes/connect.php');


function getCartProductNumber2() {
    global $con;

    
    if (!isset($_SESSION['id']) || $_SESSION['role_id']!=1) {
        return 0; 
    }

    $user_id = $_SESSION['id'];
    $query = "SELECT * FROM cart WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $query);

    if ($result) {
        return mysqli_num_rows($result);  
    } else {
        return 0;  
    }
}

echo getCartProductNumber2(); 



mysqli_close($con);
?>