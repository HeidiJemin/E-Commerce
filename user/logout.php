<?php
session_start();
   
require_once('../includes/connect.php');

if (isset($_SESSION['id'])) {
    
    $userId = $_SESSION['id'];
    $email = $_SESSION['email'];
    $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';

 

    
    $log_query = "INSERT INTO user_logs (user_id, email, action, message, ip_address, timestamp) 
                  VALUES ($userId, '$email', 'Logout', 'User logged out successfully.', '$ipAddress', NOW())";
    mysqli_query($con, $log_query);

    
    mysqli_close($con);
}


session_unset();
session_destroy();


if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/'); 
}

// Redirect to login page
header("Location: login.php");
exit();
?>
