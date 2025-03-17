<?php

session_start();
if(isset($_SESSION['id'])){
    echo "User ID: " . $_SESSION['id'];
    echo "  " . $_SESSION['role_id'];
    

}else {
    
    echo "No user is logged in.";
}
?>
