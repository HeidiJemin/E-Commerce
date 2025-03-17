<?php

require_once('../../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']); 
    $photo = $_FILES['foto'];

    
    if (empty($username) || empty($name) || empty($surname) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        mysqli_close($con);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        mysqli_close($con);
        exit;
    }

    $user_id = mysqli_real_escape_string($con, $user_id);
    $username = mysqli_real_escape_string($con, $username);
    $name = mysqli_real_escape_string($con, $name);
    $surname = mysqli_real_escape_string($con, $surname);
    $email = mysqli_real_escape_string($con, $email);

    
    $current_email_query = "SELECT email, foto FROM users WHERE user_id = '$user_id'";
    $current_email_result = mysqli_query($con, $current_email_query);

    if ($current_email_result && mysqli_num_rows($current_email_result) > 0) {
        $user_data = mysqli_fetch_assoc($current_email_result);
        $current_email = $user_data['email'];
        $existing_photo = $user_data['foto'];
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        mysqli_close($con);
        exit;
    }

    
    $check_email_query = "SELECT user_id FROM users WHERE email = '$email' AND user_id != '$user_id'";
    $check_email_result = mysqli_query($con, $check_email_query);

    if (mysqli_num_rows($check_email_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email is already in use by another user.']);
        mysqli_close($con);
        exit;
    }

    
    $photo_filename = $existing_photo; 

    if ($photo['error'] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        
        $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);

        
        if (in_array(strtolower($ext), $allowed_extensions)) {
            // unique filename
            $photo_filename = uniqid() . '.' . $ext;

            $target_dir = "../../uploads/";
            $target_file = $target_dir . $photo_filename;

            
            if (move_uploaded_file($photo['tmp_name'], $target_file)) {
                
                if ($existing_photo && $existing_photo !== $photo_filename) {
                    $existing_file_path = $target_dir . $existing_photo;
                    if (file_exists($existing_file_path)) {
                        unlink($existing_file_path);
                    }
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload photo.']);
                mysqli_close($con);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type for photo.']);
            mysqli_close($con);
            exit;
        }
    }

    // Update query
    $query = "UPDATE users SET 
                username = '$username', 
                name = '$name', 
                surname = '$surname', 
                email = '$email'";

    
    if ($email !== $current_email) {
        
        $query .= ", verified = 0";
    }

    
    if ($photo_filename !== $existing_photo) {
        $query .= ", foto = '$photo_filename'";
    }

    $query .= " WHERE user_id = '$user_id'";

    
    $result = mysqli_query($con, $query);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

mysqli_close($con);
?>

