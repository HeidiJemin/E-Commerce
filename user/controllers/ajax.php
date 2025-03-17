<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../includes/connect.php');

require '../../libraries/phpmailer/Exception.php';
require '../../libraries/phpmailer/PHPMailer.php';
require '../../libraries/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$GMAIL_ADDRESS = 'trendytrend2803@gmail.com';
$GMAIL_ADDRESS_PASSWORD = 'iwsc jlrv aoyc dbpa';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $con;

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'register') {
            
            $name = mysqli_real_escape_string($con, trim($_POST['name']));
            $surname = mysqli_real_escape_string($con, trim($_POST['surname']));
            $username = mysqli_real_escape_string($con, trim($_POST['username']));
            $email = mysqli_real_escape_string($con, trim($_POST['email']));
            $password = mysqli_real_escape_string($con, trim($_POST['password']));
            $confirmPassword = mysqli_real_escape_string($con, trim($_POST['conf_password']));
            $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

            
            $nameRegex = "/^[A-Z][a-zA-Z ]{2,19}$/";           
            $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/"; 
            $usernameRegex = "/^[a-zA-Z0-9-_]{3,20}$/";
            
            if (!preg_match($nameRegex, $name)) {
                http_response_code(203);
                echo json_encode([
                    "message" => "Emri fillon me shkronje te madhe, vetem karaktere, minimumi 3",
                    "tagError" => "nameError",
                    "tagElement" => "name"
                ]);
                mysqli_close($con);
                exit;
            }

            
            if (!preg_match($nameRegex, $surname)) {
                http_response_code(203);
                echo json_encode([
                    "message" => "Mbiemri fillon me shkronje te madhe, vetem karaktere, minimumi 3",
                    "tagError" => "surnameError",
                    "tagElement" => "surname"
                ]);
                mysqli_close($con);
                exit;
            }

            
            if (!preg_match($usernameRegex, $username)) {
                http_response_code(203);
                echo json_encode([
                    "message" => "Username vetem karaktere,numra,ose '-' dhe '_' , minimumi 3",
                    "tagError" => "usernameError",
                    "tagElement" => "username"
                ]);
                mysqli_close($con);
                exit;
            }

            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(203);
                echo json_encode([
                    "message" => "Email nuk eshte i sakte",
                    "tagError" => "emailError",
                    "tagElement" => "email"
                ]);
                mysqli_close($con);
                exit;
            }

            

            
            if (empty($password) || strlen($password) < 8) {
                http_response_code(203);
                echo json_encode([
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
                    "message" => "Internal Server Error: Unable to check for existing user.",
                    "error" => mysqli_error($con)
                ]);
                mysqli_close($con);
                exit;
            }

            if (mysqli_num_rows($result_check) > 0) {
                http_response_code(203);
                echo json_encode([
                    "message" => "Invalid Email.Try another Email",
                    "tagError" => "emailError",
                    "tagElement" => "email"
                ]);
                mysqli_close($con);
                exit;
            }
            
            
            $query_insert = "INSERT INTO users (name, surname,username, email, password, role_id, verified) VALUES ('$name', '$surname', '$username', '$email', '$passwordHashed', 1, 0)";
            $result_insert = mysqli_query($con, $query_insert);

            if (!$result_insert) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Internal Server Error: Unable to register user.",
                    "error" => mysqli_error($con)
                ]);
                mysqli_close($con);
                exit;
            }

            
            $user_id = mysqli_insert_id($con);
            $query_user = "SELECT * FROM users WHERE user_id = '$user_id'";
            $result_user = mysqli_query($con, $query_user);

            if (!$result_user) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Internal Server Error: Unable to fetch user.",
                    "error" => mysqli_error($con)
                ]);
                mysqli_close($con);
                exit;
            }

            $row = mysqli_fetch_assoc($result_user);

            
            $verificationCode = rand(100000, 999999);
            
            
            $expirationDate = date("Y-m-d H:i:s", strtotime("+24 hour"));

            
            $query_insert_code = "UPDATE users SET verification_code = '$verificationCode', code_expiration = '$expirationDate' WHERE email = '$email'";
            $result_insert_code = mysqli_query($con, $query_insert_code);

            if (!$result_insert_code) {
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
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $GMAIL_ADDRESS ; 
                $mail->Password = $GMAIL_ADDRESS_PASSWORD; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                
                $mail->setFrom('your-email@gmail.com', 'Mailer');
                $mail->addAddress($email, $name);

                
                $mail->isHTML(true);
                $mail->Subject = 'Your Verification Code';
                $mail->Body    = "Your 6-digit verification code is: <b>$verificationCode</b>";

                $mail->send();
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    "message" => "Could not send verification email. Please try again later.",
                    "error" => $mail->ErrorInfo
                ]);
                mysqli_close($con);
                exit;
            }

            
            $_SESSION['id'] = $row['user_id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['date_time'] = time();
            $_SESSION['name'] = $row['name'];
            $_SESSION['surname'] = $row['surname'];
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['verified'] = $row['verified'];
            $_SESSION['username'] = $row['username'];

            
            http_response_code(200);
            echo json_encode([
                "message" => "Useri u ruajt me sukses. Verifikimi i email-it eshte derguar.",
                "location" => "./verify.php"  
            ]);

            mysqli_close($con);
            exit;

            
        } elseif ($_POST['action'] == "verifyCode") {
            if (!isset($_SESSION['email'])) {
                http_response_code(400);
                echo json_encode([
                    "message" => "Email not found in session."
                ]);
                exit;
            }

            $email = mysqli_real_escape_string($con, trim($_SESSION['email']));
            $verificationCode = mysqli_real_escape_string($con, trim($_POST['verificationCode']));

            
            $query_check_code = "SELECT user_id FROM users WHERE email = '$email' AND verification_code = '$verificationCode' AND code_expiration > NOW()";
            $result_check_code = mysqli_query($con, $query_check_code);

            if (mysqli_num_rows($result_check_code) > 0) {
                
                $query_update_verified = "UPDATE users SET verified = 1 WHERE email = '$email'";
                mysqli_query($con, $query_update_verified);

                http_response_code(200);
            
                $_SESSION['verified'] = '1';

                echo json_encode([
                    "message" => "Email verified successfully."
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "message" => "Invalid or expired verification code."
                ]);
            }
            mysqli_close($con);
            exit;
        }
        elseif ($_POST['action'] == "login") {
            $email = mysqli_real_escape_string($con, trim($_POST['email']));
            $password = mysqli_real_escape_string($con, trim($_POST['password']));
            $rememberMe = isset($_POST['rememberMe']) && $_POST['rememberMe'] === "true";
            
            


            


    if (empty($email)) {
        http_response_code(203);
        echo json_encode([
            "message" => "Email cannot be empty.",
            "tagError" => "emailError",
            "tagElement" => "email"
        ]);
        mysqli_close($con);
        exit;
    }

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
             

        
            if (empty($password)) {
               
                http_response_code(203);
                echo json_encode([
                    "message" => "Password cannot be empty.",
                    "tagError" => "passwordError",
                    "tagElement" => "password"
                ]);
                mysqli_close($con);
                exit;
            }

           
        
            
            $query_check = "SELECT 
                                users.user_id, 
                                users.username, 
                                users.surname,
                                users.name,
                                users.email, 
                                users.password, 
                                users.role_id, 
                                users.verified,
                                users.verification_code,
                                users.failed_attempts,
                                users.lockout_time,
                                users.remember_token,
                                users.code_expiration,
                                roles.name AS role_name
                            FROM 
                                users 
                            LEFT JOIN 
                                roles ON users.role_id = roles.id
                            WHERE 
                                users.email = '$email';";
        
            $result_check = mysqli_query($con, $query_check);
        
            if (!$result_check) {
                $log_query = "INSERT INTO user_logs (user_id, email, action, message, ip_address, timestamp) 
                      VALUES (NULL, '$email', 'Login Attempt', 'Database error during login.', '{$_SERVER['REMOTE_ADDR']}', NOW())";
        mysqli_query($con, $log_query);
                http_response_code(500);
                echo json_encode([
                    "message" => "Internal Server Error",
                    "error" => mysqli_error($con)
                ]);
                mysqli_close($con);
                exit;
            }
        
            if (mysqli_num_rows($result_check) == 0) {

        
                http_response_code(203);
                echo json_encode(["message" => "Password/Email Incorrect"]);
                mysqli_close($con);
                exit;
            }
            
        
            $row = mysqli_fetch_assoc($result_check);
            $passwordHashed = $row['password'];

             
             if ($row['failed_attempts'] >= 7) {
                $lockout_time = strtotime($row['lockout_time']);
                $current_time = time();
                
                
                if ($current_time - $lockout_time > 1800) {
                    
                    $reset_query = "UPDATE users SET failed_attempts = 0, lockout_time = NULL WHERE user_id = " . $row['user_id'];
                    mysqli_query($con, $reset_query);
                } else {
                    $log_query = "INSERT INTO user_logs (user_id, email, action, message, ip_address, timestamp) 
                          VALUES ({$row['user_id']}, '$email', 'Account Locked', 'Account is locked due to too many failed attempts.', '{$_SERVER['REMOTE_ADDR']}', NOW())";
            mysqli_query($con, $log_query);
                    http_response_code(203);
                    echo json_encode(
                        array(
                            "message" => "Your account is locked. Please try again after 30 minutes."
                        ));
                        mysqli_close($con);
                        exit;
                }
            }

        
            // verifikimi i password
            
            if (!password_verify($password, $passwordHashed)) {
                
                $new_failed_attempts = ($row['failed_attempts'] >= 7) ? 1 : $row['failed_attempts'] + 1;
            
                
                if ($new_failed_attempts >= 7) {
                    $lockout_time = date("Y-m-d H:i:s");
                    $query_update_failed_attempts = "UPDATE users 
                                                     SET failed_attempts = $new_failed_attempts, lockout_time = '$lockout_time' 
                                                     WHERE user_id = " . $row['user_id'];
                    mysqli_query($con, $query_update_failed_attempts);
            
                    
                    $log_query = "INSERT INTO user_logs (user_id, email, action, message, ip_address, timestamp) 
                                  VALUES ({$row['user_id']}, '$email', 'Account Locked', 'Account locked after too many failed login attempts.', '{$_SERVER['REMOTE_ADDR']}', NOW())";
                    mysqli_query($con, $log_query);
            
                    
                    http_response_code(203);
                    echo json_encode(["message" => "Your account is locked due to too many failed attempts. Please try again after 30 minutes."]);
                    mysqli_close($con);
                exit;
                }
            
                
                $query_update_failed_attempts = "UPDATE users SET failed_attempts = $new_failed_attempts WHERE user_id = " . $row['user_id'];
                mysqli_query($con, $query_update_failed_attempts);
            
                
                $log_query = "INSERT INTO user_logs (user_id, email, action, message, ip_address, timestamp) 
                              VALUES ({$row['user_id']}, '$email', 'Failed Login', 'Incorrect password.', '{$_SERVER['REMOTE_ADDR']}', NOW())";
                mysqli_query($con, $log_query);
            
                
                http_response_code(203);
                echo json_encode(["message" => "Password/Email Incorrect."]);
                exit;
            }
            
        
        //nese login i sakte cojme ne 0 failed attempts
        $query_update_failed_attempts = "UPDATE users SET failed_attempts = 0 WHERE user_id = " . $row['user_id'];
        mysqli_query($con, $query_update_failed_attempts);
        $log_query = "INSERT INTO user_logs (user_id, email, action, message, ip_address, timestamp) 
        VALUES ({$row['user_id']}, '$email', 'Successful Login', 'User logged in successfully.', '{$_SERVER['REMOTE_ADDR']}', NOW())";
mysqli_query($con, $log_query);

            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        
            $_SESSION['id'] = $row['user_id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['date_time'] = time();
            $_SESSION['username'] = $row['username'];
            $_SESSION['surname'] = $row['surname'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['verified'] = $row['verified'];
            

            if ($rememberMe) {
               
                        
                $rememberToken = bin2hex(random_bytes(64));

                
                $updateTokenQuery = "UPDATE users SET remember_token = ? WHERE user_id = ?";
                $updateStmt = mysqli_prepare($con, $updateTokenQuery);
                mysqli_stmt_bind_param($updateStmt, 'si', $rememberToken, $row['user_id']);

                if (!mysqli_stmt_execute($updateStmt)) {
                    http_response_code(500);
                    echo json_encode(["success" => false, "message" => "Error updating remember token in the database."]);
                    exit;
                }

                
                setcookie('remember_token', $rememberToken, time() + 86400 * 30, '/', '', true, true); // Cookie valid for 30 days
             }
        
            
            if ($row['verified'] == 0) {
                
                $verificationCode = rand(100000, 999999);
                $expirationDate = date("Y-m-d H:i:s", strtotime("+24 hour"));
        
                
                $query_insert_code = "UPDATE users SET verification_code = '$verificationCode', code_expiration = '$expirationDate' WHERE email = '$email'";
                if (!mysqli_query($con, $query_insert_code)) {
                    http_response_code(500);
                    echo json_encode(["message" => "Error updating verification code in the database."]);
                    mysqli_close($con);
                    exit;
                }
        
                
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $GMAIL_ADDRESS; 
                    $mail->Password = $GMAIL_ADDRESS_PASSWORD; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
        
                    
                    $mail->setFrom('your-email@gmail.com', 'Mailer');
                    $mail->addAddress($email, $row['username']); 
        
                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Verification Code';
                    $mail->Body = "Your 6-digit verification code is: <b>$verificationCode</b><br><br>
                        Please verify your email by visiting the following link: 
                        <a href='http://yourwebsite.com/verify.php'>Verify Now</a>";
        
                    $mail->send();
        
                    
                    http_response_code(200);
                    echo json_encode([
                        "success" => true,
                        "message" => "Verification email sent. Please check your inbox.",
                        "verified" => false,
                        "location" => "./verify.php"
                    ]);
                 mysqli_close($con);
                exit;
        
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode([
                        "message" => "Could not send verification email. Please try again later.",
                        "error" => $mail->ErrorInfo
                    ]);
                    mysqli_close($con);
                    exit;
                }
            }
             // Update session email



            
            $location = "profile.php"; 
            if ($row['role_id'] == 0) { 
                $location = "../admin_manage/index.php?shiko_user"; // Admin 
            }
            if ($row['verified'] == 1 && $_SESSION['email'] != $row['email']) {
                $_SESSION['email'] = $row['email']; 
            }
        
            http_response_code(200);
           
            echo json_encode([
                "success" => true,
                "message" => "User logged in successfully.",
                "verified" => true,
                "location" => $location
            ]);
            mysqli_close($con);
            exit;
        }
        
        
        elseif ($_POST['action'] == "updateUser") {
            $id = intval($_POST['id']);
            $name = mysqli_real_escape_string($con, $_POST['name']);
            $surname = mysqli_real_escape_string($con, $_POST['surname']);
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            
            
            if (!preg_match("/^[A-Z][a-zA-Z ]{2,19}$/", $name)) {
                echo json_encode(['status' => 'error', 'field' => 'name', 'message' => 'Name must start with a capital letter and be 3-20 characters long.No-Numbers']);
                mysqli_close($con);
                exit;
            }
        
            if (!preg_match("/^[A-Z][a-zA-Z ]{2,19}$/", $surname)) {
                echo json_encode(['status' => 'error', 'field' => 'surname', 'message' => 'Surname must start with a capital letter and be 3-20 characters long.No-Numbers']);
                mysqli_close($con);
                exit;
            }
        
            if (empty($username)) {
                echo json_encode(['status' => 'error', 'field' => 'username', 'message' => 'Username cannot be empty.']);
                mysqli_close($con);
                exit;
            }

            if (!preg_match('/^[a-zA-Z0-9-_]{3,20}$/', $username)) {
                echo json_encode([
                    'status' => 'error',
                    'field' => 'username',
                    'message' => "Username must be 3-20 characters long and contain only letters,numbers, '-' or '_'."
                ]);
                mysqli_close($con);
                exit;
            }

        
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'error', 'field' => 'email', 'message' => 'Enter a valid email address.']);
                mysqli_close($con);
                exit;
            }
        
            
            $emailCheckQuery = "SELECT user_id FROM users WHERE email = '$email' AND user_id != $id";
            $emailCheckResult = mysqli_query($con, $emailCheckQuery);
        
            if (mysqli_num_rows($emailCheckResult) > 0) {
                echo json_encode(['status' => 'error', 'field' => 'email', 'message' => 'Try another email.']);
                mysqli_close($con);
                exit;
            }
        
            
            $currentEmailQuery = "SELECT email FROM users WHERE user_id = $id";
            $currentEmailResult = mysqli_query($con, $currentEmailQuery);
            if (!$currentEmailResult || mysqli_num_rows($currentEmailResult) == 0) {
                echo json_encode(['status' => 'error', 'message' => 'User not found.']);
                mysqli_close($con);
                exit;
            }
        
            $currentEmailRow = mysqli_fetch_assoc($currentEmailResult);
            $existingEmail = $currentEmailRow['email'];
        
            
            $isEmailChanged = ($email !== $existingEmail);
        
           
            
$fotoPath = null;


if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    
    $query = "SELECT foto FROM users WHERE user_id = $id";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $currentFoto = $row['foto'];

        
        if (!empty($currentFoto)) {
            $currentFilePath = "../../uploads/" . $currentFoto;
            if (file_exists($currentFilePath)) {
                unlink($currentFilePath);
            }
        }
    }

    
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

    
    $photo_filename = $id . '_' . uniqid() . '.' . $ext;

    
    $target_dir = "../../uploads/";
    $target_file = $target_dir . $photo_filename;

    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        $fotoPath = $photo_filename; 
    }
}


$updateQuery = "UPDATE users SET 
                name = '$name', 
                surname = '$surname', 
                username = '$username', 
                email = '$email'" . 
                ($isEmailChanged ? ", verified = 0" : "") . 
                ($fotoPath ? ", foto = '$fotoPath'" : "") . 
                " WHERE user_id = $id";

        
            if (mysqli_query($con, $updateQuery)) {
                if ($isEmailChanged) {
                    
                     $_SESSION['email'] = $email;  
                     $_SESSION['verified'] = '0';

                    $verificationCode = rand(100000, 999999);
                    $expirationDate = date("Y-m-d H:i:s", strtotime("+24 hour"));
                    $query_update_code = "UPDATE users SET verification_code = '$verificationCode', code_expiration = '$expirationDate' WHERE user_id = $id";
                    mysqli_query($con, $query_update_code);
        
                    
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = $GMAIL_ADDRESS;
                        $mail->Password = $GMAIL_ADDRESS_PASSWORD;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->setFrom('your-email@gmail.com', 'Mailer');
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = 'Your Verification Code';
                        $mail->Body = "Your 6-digit verification code is: <b>$verificationCode</b><br><br>
                                       Please verify your email by visiting the following link: 
                                       <a href='http://yourwebsite.com/verify.php'>Verify Now</a>";
                        $mail->send();
                    } catch (Exception $e) {
                        mysqli_close($con); 
                        echo json_encode(['status' => 'error', 'message' => 'Could not send verification email.']);
                        mysqli_close($con);
                        exit;
                    }
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Profile updated successfully. Verification email sent to the new address.',
                        'redirect' => './verify.php'
                    ]);
                } else {
                    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
                }
            
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . mysqli_error($con)]);
            }
            mysqli_close($con);

        }elseif (isset($_POST['action']) && $_POST['action'] == 'updatePassword') {
                
                $userId = intval($_POST['id']);
                $currentPassword = mysqli_real_escape_string($con, $_POST['currentPassword']);
                $newPassword = mysqli_real_escape_string($con, $_POST['newPassword']);

                
                $query = "SELECT password FROM users WHERE user_id = '$userId'";
                $result = mysqli_query($con, $query);

                if (!$result) {
                    echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . mysqli_error($con)]);
                    mysqli_close($con); 
                    exit;
                }

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $hashedPassword = $row['password'];

                    
                    if (password_verify($currentPassword, $hashedPassword)) {
                        
                        if (strlen($newPassword) < 8) {
                            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long.']);
                            mysqli_close($con); 
                            exit;
                        }
                        $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/"; 
                        if (!preg_match($passwordRegex, $newPassword)) {
                            echo json_encode([
                                'status' => 'error',
                                'message' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
                            ]);
                            mysqli_close($con); 
                            exit;
                        }

                        
                        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                        
                        $updateQuery = "UPDATE users SET password = '$hashedNewPassword' WHERE user_id = '$userId'";
                        if (mysqli_query($con, $updateQuery)) {
                            echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
                        }
                    } else {
                        
                        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
                    }
                } else {
                    
                    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
                }
                mysqli_close($con); 
            } elseif ($_POST['action'] === 'resendVerification') {
                if (!isset($_SESSION['email'])) {
                    http_response_code(400);
                    echo json_encode([
                        "message" => "Email not found in session."
                    ]);
                    mysqli_close($con); 
                            exit;
                }

                $email = mysqli_real_escape_string($con, trim($_SESSION['email']));

                
                $verificationCode = rand(100000, 999999);
                
                
                $expirationDate = date("Y-m-d H:i:s", strtotime("+24 hour"));
                
                
                $query_update_code = "UPDATE users SET verification_code = '$verificationCode', code_expiration = '$expirationDate' WHERE email = '$email'";
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
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $GMAIL_ADDRESS; 
                    $mail->Password = $GMAIL_ADDRESS_PASSWORD; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    
                    $mail->setFrom('your-email@gmail.com', 'Mailer');
                    $mail->addAddress($email);

                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Verification Code';
                    $mail->Body    = "Your new 6-digit verification code is: <b>$verificationCode</b>";

                    $mail->send();
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
                    "message" => "Verification email sent successfully."
                ]);
                mysqli_close($con); 
                exit;
            }



            elseif (isset($_POST['action']) && $_POST['action'] === 'deletePhoto') {
                if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid user ID.'
                    ]);
                    mysqli_close($con); 
                            exit;
                }
            
                $user_id = intval($_POST['user_id']);
                error_log("Received user_id: $user_id");
            
                
                $query = "SELECT foto FROM users WHERE user_id = $user_id";
                $result = mysqli_query($con, $query);
            
                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $photoPath = $row['foto'];
                    $fullPhotoPath = '../../uploads/' . $photoPath;
                    
                    $deleteQuery = "UPDATE users SET foto = NULL WHERE user_id = $user_id";
                    $deleteResult = mysqli_query($con, $deleteQuery);
            
                    if ($deleteResult) {
                        
                        if ($photoPath && file_exists($fullPhotoPath)) {
                            unlink($fullPhotoPath);
                        }
            
                        echo json_encode([
                            'success' => true,
                            'message' => 'Photo deleted successfully.',
                            'verified' => true,
                            'location' => './profile.php'
                        ]);
                    } else {
                        
                        error_log("SQL Error: " . mysqli_error($con));
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to update the database.'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'User not found.'
                    ]);
                }
            }


        } 
}
           else {
                http_response_code(405); 
                echo json_encode(["message" => "Invalid request method."]);
            }
            

?>
