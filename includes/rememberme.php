<?php

$adminPath = '../admin_manage/index.php';

// if user is logged in
if (isset($_SESSION['id'])) {
    // not user
    if ($_SESSION['role_id'] != 1) {
        // admin page if role_id is not 1
        header("Location: $adminPath");
        exit();
    }

} else {
    // user not logged in, do "remember me" 
    if (isset($_COOKIE['remember_token'])) {
        $rememberToken = $_COOKIE['remember_token'];

        // Query the database to find the user with the matching remember_token
        $query = "SELECT user_id, email, remember_token,name, username,verified, role_id FROM users WHERE remember_token = '$rememberToken'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            // Token matched, log the user in
            $user = mysqli_fetch_assoc($result);

            $_SESSION['id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['date_time'] = time();
            $_SESSION['name'] = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['verified'] = $user['verified'];

            if ($user['role_id'] == 1) {
                 
                
                exit();
            } else {
                
                header("Location: $adminPath");
                exit();
            }
        } else {
            
            header("Location: login.php");
            exit();
        }
    } else {
        
        header("Location: login.php");
        exit();
    }
}
?>
