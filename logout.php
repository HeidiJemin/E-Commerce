<?php
session_start();
session_unset();
session_destroy();

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/'); // Set the cookie to expire in the past
}

// Redirect to login.php
header("Location: login.php");
exit();
?>
