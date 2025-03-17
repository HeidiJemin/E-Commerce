<?php
session_start();

if (isset($_SESSION['id'])) {
    $_SESSION['last_activity'] = time(); // Update the last activity
    echo 'Activity updated';
} else {
    echo 'User not logged in';
}
?>
