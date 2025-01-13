<?php

// Include database connection
include('../includes/connect.php');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);

    // Validate inputs (server-side validation for security)
    if (empty($username) || empty($name) || empty($surname) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    // Escaping data to prevent SQL injection
    $user_id = mysqli_real_escape_string($con, $user_id);
    $username = mysqli_real_escape_string($con, $username);
    $name = mysqli_real_escape_string($con, $name);
    $surname = mysqli_real_escape_string($con, $surname);
    $email = mysqli_real_escape_string($con, $email);

    // Update user data in the database
    $query = "UPDATE users SET 
                username = '$username', 
                name = '$name', 
                surname = '$surname', 
                email = '$email' 
              WHERE user_id = '$user_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user.']);
    }

    // Close database connection
    mysqli_close($con);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

mysqli_close($con);
?>