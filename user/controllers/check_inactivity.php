<?php
session_start();


$timeout = 15 * 60 * 1000; // 15 min

if (isset($_SESSION['id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) >= $timeout) {
        echo 'inactive'; 
    } else {
        echo 'active'; 
    }
} else {
    echo 'not_logged_in'; 
}
?>
