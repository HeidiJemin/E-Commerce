<?php
require_once('../../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $con;

    
    $name = mysqli_real_escape_string($con, trim($_POST['name']));
    $surname = mysqli_real_escape_string($con, trim($_POST['surname']));
    $username = mysqli_real_escape_string($con, trim($_POST['username']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $password = mysqli_real_escape_string($con, trim($_POST['password']));
    $confirmPassword = mysqli_real_escape_string($con, trim($_POST['conf_password']));
    $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

    
    $nameRegex = "/^[a-zA-Z ]{3,20}$/"; 
    $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/"; 
    $usernameRegex = "/^[a-zA-Z0-9-_]{3,20}$/";

    // Validate first name
    if (!preg_match($nameRegex, $name)) {
        http_response_code(203);
        echo json_encode([
            "success" => false,
            "message" => "Emri vetem karaktere, minimumi 3",
            "tagError" => "nameError",
            "tagElement" => "name"
        ]);
        mysqli_close($con);
        exit;
    }

    // Validate surname
    if (!preg_match($nameRegex, $surname)) {
        http_response_code(203);
        echo json_encode([
            "success" => false,
            "message" => "Mbiemri vetem karaktere, minimumi 3",
            "tagError" => "surnameError",
            "tagElement" => "surname"
        ]);
        mysqli_close($con);
        exit;
    }

    // Validate username
    if (!preg_match($usernameRegex, $username)) {
        http_response_code(203);
        echo json_encode([
            "success" => false,
            "message" => "Username duhet te kete gjatesi 3-20 ,te permbaje vetem karaktere,numra,'-' ose '_'/",
            "tagError" => "usernameError",
            "tagElement" => "username"
        ]);
        mysqli_close($con);
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(203);
        echo json_encode([
            "success" => false,
            "message" => "Email nuk eshte i sakte",
            "tagError" => "emailError",
            "tagElement" => "email"
        ]);
        mysqli_close($con);
        exit;
    }

    // Validate password
    if (empty($password) || strlen($password) < 8) {
        http_response_code(203);
        echo json_encode([
            "success" => false,
            "message" => "Passwordi minimumi 8 karaktere.",
            "tagError" => "passwordError",
            "tagElement" => "password"
        ]);
        mysqli_close($con);
        exit;
    }

    if (!preg_match($passwordRegex, $password)) {
        
        http_response_code(203);
        echo json_encode([
            "success" => false,
            "message" => "Passwordi duhet të përmbajë të paktën një shkronjë të madhe, një të vogël, një numër dhe një karakter special.",
            "tagError" => "passwordError",
            "tagElement" => "password"
        ]);
        mysqli_close($con);
        exit;
    }

    
    if ($password !== $confirmPassword) {
        http_response_code(203);
        echo json_encode([
            "success" => false,
            "message" => "Passwordi dhe confirm password te barabarta.",
            "tagError" => "passwordError",
            "tagElement" => "password"
        ]);
        mysqli_close($con);
        exit;
    }

    
    $query_check = "SELECT user_id FROM users WHERE email = '$email'";
    $result_check = mysqli_query($con, $query_check);

    if (!$result_check) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Internal Server Error: Unable to check for existing user.",
            "error" => mysqli_error($con)
        ]);
        mysqli_close($con);
        exit;
    }

    if (mysqli_num_rows($result_check) > 0) {
        http_response_code(203);
        echo json_encode([
            "success" => false,
            "message" => "Invalid Email. Try another Email",
            "tagError" => "emailError",
            "tagElement" => "email"
        ]);
        mysqli_close($con);
        exit;
    }

    
    $query_insert = "INSERT INTO users (name, surname, username, email, password, role_id, verified) VALUES ('$name', '$surname', '$username', '$email', '$passwordHashed', 1, 0)";
    $result_insert = mysqli_query($con, $query_insert);

    if (!$result_insert) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Internal Server Error: Unable to register user.",
            "error" => mysqli_error($con)
        ]);
        mysqli_close($con);
        exit;
    }

    // Success 
    mysqli_close($con);
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "User successfully registered."
    ]);
    exit;
} else {
    
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Only POST requests are accepted.",
    ]);
    exit;
}
