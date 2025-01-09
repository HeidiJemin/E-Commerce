<?php

session_start();
if(isset($_SESSION['id'])){
    echo "User ID: " . $_SESSION['id'];
    echo "" . $_SESSION['firstname'];
    echo "" . $_SESSION['email'];
    echo "" . $_SESSION['address'];
    echo "" . $_SESSION['zipcode'];
    echo "" . $_SESSION['country'];
    echo "" . $_SESSION['city'];
    echo "" . $_SESSION['firstname'];

}else {
    // If the session variable is not set, print a message
    echo "No user is logged in.";
}
?>