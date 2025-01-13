<?php
// Start the session if it has not been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('../includes/connect.php'); 

require './libraries/phpmailer/Exception.php';
require './libraries/phpmailer/PHPMailer.php';
require './libraries/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$GMAIL_ADDRESS = 'trendytrend2803@gmail.com';
$GMAIL_ADDRESS_PASSWORD = 'iwsc jlrv aoyc dbpa';

if($_POST['action'] === 'resetPassword') {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(203);
        echo json_encode([
            "message" => "Invalid email format.",
            "tagError" => "emailError",
            "tagElement" => "email"
        ]);
        exit;
    }

    // Check if the email exists in the database
    $query = "SELECT * FROM users WHERE email ='$email'";
    $current_email_result = mysqli_query($con, $query);

    if (!$current_email_result) {
        http_response_code(500);
        echo json_encode([
            "message" => "Internal Server Error",
            "error" => mysqli_error($con)
        ]);
        exit;
    }

    if (mysqli_num_rows($current_email_result) == 0) {
        http_response_code(203);
        echo json_encode(["message" => "No user found with this email."]);
        exit;
    }

    // Generate verification code and expiration time
    $verificationCode = rand(100000, 999999);
    $expirationDate = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Update the database with the verification code and expiration time
    $query_update_code = "UPDATE users SET verification_code = '$verificationCode', code_expiration = '$expirationDate' WHERE email = '$email'";
    $result_update_code = mysqli_query($con, $query_update_code);

    if (!$result_update_code) {
        http_response_code(500);
        echo json_encode([
            "message" => "Internal Server Error: Unable to store verification code.",
            "error" => mysqli_error($con)
        ]);
        exit;
    }




    // Send the verification code via email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $GMAIL_ADDRESS; // Define your email address
        $mail->Password = $GMAIL_ADDRESS_PASSWORD; // Define your email password
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
        exit;
    }

    // Successfully sent the email
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        "message" => "Verification email sent successfully."
    ]);
    
    exit;
}elseif ($_POST["action"] === 'changePassword') { 

    if (!isset($_SESSION['email'])) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            'success' => false,
            'message' => 'Session expired. Try again.'
        ]);
        exit;
    }

    // Input sanitization
    $email = mysqli_real_escape_string($con, $_SESSION['email']); 
    $verificationCode = mysqli_real_escape_string($con, $_POST['verificationCode']);
    $newPassword = mysqli_real_escape_string($con, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($con, $_POST['confirmPassword']);

    // Define a password validation regex
    $passwordRegex = "/^[a-zA-Z0-9-_ ]{4,}$/"; // Password must be at least 4 characters, alphanumeric, with optional special chars

    // Validate the new password
    if (!preg_match($passwordRegex, $newPassword)) {
        http_response_code(203);
        echo json_encode([
            "message" => "Password must be at least 4 characters long.",
            "tagError" => "passwordError",
            "tagElement" => "password"
        ]);
        exit;
    }

    // Check if password and confirm password match
    if ($newPassword !== $confirmPassword) {
        http_response_code(203);
        echo json_encode([
            "message" => "Passwords do not match.",
            "tagError" => "confirmPasswordError",
            "tagElement" => "confirmPassword"
        ]);
        exit;
    }

    // Verify the verification code
    $query = "SELECT verification_code FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Failed to verify the verification code. Please try again.',
            'error' => mysqli_error($con) // For debugging, remove in production
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($result);
    if (!$row || $row['verification_code'] !== $verificationCode) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Invalid verification code. Please try again.'
        ]);
        exit;
    }

    // Hash the new password
    $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the password in the database
    $updateQuery = "UPDATE users SET password = '$hashedNewPassword' WHERE email = '$email'";
    if (mysqli_query($con, $updateQuery)) {
        // Destroy the current session after successful password reset
        session_unset(); // Unset session variables
        session_destroy(); // Destroy the session

        echo json_encode([
            'success' => true,
            'message' => 'Password updated successfully. You will be logged out now.'
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update password. Please try again.',
            'error' => mysqli_error($con) // For debugging, remove in production
        ]);
    }
    exit;
}
mysqli_close($con);
    ?>