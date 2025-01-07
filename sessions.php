<?php

session_start();
if(isset($_SESSION['id'])){
    echo "User ID: " . $_SESSION['id'];
    echo "User ID: " . $_SESSION['verified'];

}else {
    // If the session variable is not set, print a message
    echo "No user is logged in.";
}
?>