<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('../../includes/connect.php'); 

require '../../libraries/phpmailer/Exception.php';
require '../../libraries/phpmailer/PHPMailer.php';
require '../../libraries/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$GMAIL_ADDRESS = 'trendytrend2803@gmail.com';
$GMAIL_ADDRESS_PASSWORD = 'iwsc jlrv aoyc dbpa';

if($_POST['action'] === 'resetPassword') {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(203);
        echo json_encode([
            "message" => "Invalid email format.",
            "tagError" => "emailError",
            "tagElement" => "email"
        ]);
        mysqli_close($con); 
        exit;
    }

    
    $query = "SELECT * FROM users WHERE email ='$email'";
    $current_email_result = mysqli_query($con, $query);

    if (!$current_email_result) {
        http_response_code(500);
        echo json_encode([
            "message" => "Internal Server Error",
            "error" => mysqli_error($con)
        ]);
        mysqli_close($con); 
        exit;
    }

    if (mysqli_num_rows($current_email_result) == 0) {
        http_response_code(203);
        echo json_encode(["message" => "Invalid email."]);
        mysqli_close($con); 
        exit;
    }

    
    $verificationCode = rand(100000, 999999);
    $expirationDate = date("Y-m-d H:i:s", strtotime("+1 hour"));

    
    $query_update_code = "UPDATE users SET verification_code_pass = '$verificationCode', code_expiration = '$expirationDate' WHERE email = '$email'";
    $result_update_code = mysqli_query($con, $query_update_code);

    if (!$result_update_code) {
        http_response_code(500);
        echo json_encode([
            "message" => "Internal Server Error: Unable to store verification code.",
            "error" => mysqli_error($con)
        ]);
        mysqli_close($con); 
        exit;
    }




    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $GMAIL_ADDRESS; 
        $mail->Password = $GMAIL_ADDRESS_PASSWORD; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($GMAIL_ADDRESS, 'Password Reset');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = "Your 6-digit verification code is: <b>$verificationCode</b>";

        $mail->send();
        $_SESSION['email'] = $email;
            
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "message" => "Could not send verification email. Please try again later.",
            "error" => $mail->ErrorInfo
        ]);
        mysqli_close($con); 
        exit;
    }

    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        "message" => "Verification email sent successfully."
    ]);
    mysqli_close($con); 
    exit;
}elseif ($_POST["action"] === 'changePassword') { 

    if (!isset($_SESSION['email'])) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            'success' => false,
            'message' => 'Session expired. Try again.'
        ]);
        mysqli_close($con); 
        exit;
    }

    
    $email = mysqli_real_escape_string($con, $_SESSION['email']); 
    $verificationCode = mysqli_real_escape_string($con, $_POST['verificationCode']);
    $newPassword = mysqli_real_escape_string($con, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($con, $_POST['confirmPassword']);

    //password regex
    $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/"; 


    if (!preg_match($passwordRegex, $newPassword)) {
        http_response_code(203);
        echo json_encode([
            'message' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            "tagError" => "passwordError",
            "tagElement" => "password"
        ]);
        mysqli_close($con);
        exit;
    }

    
    if ($newPassword !== $confirmPassword) {
        http_response_code(203);
        echo json_encode([
            "message" => "Passwords do not match.",
            "tagError" => "confirmPasswordError",
            "tagElement" => "confirmPassword"
        ]);
        mysqli_close($con); 
        exit;
    }

    
    $query = "SELECT verification_code_pass FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        http_response_code(500); 
        echo json_encode([
            'success' => false,
            'message' => 'Failed to verify the verification code. Please try again.',
            'error' => mysqli_error($con) 
        ]);
        mysqli_close($con);
        exit;
    }

    $row = mysqli_fetch_assoc($result);
    if (!$row || $row['verification_code_pass'] !== $verificationCode) {
        http_response_code(203); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Invalid verification code. Please try again.'
        ]);
        mysqli_close($con);
        exit;
    }

    // Hash 
    $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    
    $updateQuery = "UPDATE users SET password = '$hashedNewPassword' WHERE email = '$email'";
    if (mysqli_query($con, $updateQuery)) {
        
        session_unset(); 
        session_destroy(); 

        echo json_encode([
            'success' => true,
            'message' => 'Password updated successfully. You will be logged out now.'
        ]);
    } else {
        http_response_code(500); 
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update password. Please try again.',
            'error' => mysqli_error($con) 
        ]);
    }
    mysqli_close($con);
    exit;
}
mysqli_close($con);
    ?>