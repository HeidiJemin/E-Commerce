<?php
session_start();
include_once('includes/connect.php');

// Redirect if email is not set in session
if (!isset($_SESSION['email'])) {
    header("Location: ./forgot_password.php");
    exit;
}

if(isset($_SESSION['id'])){
    header("location:profile.php");
}


$email = mysqli_real_escape_string($con, trim($_SESSION['email']));
$query_get_verificationcode = "SELECT verification_code, username FROM users WHERE email = '$email'";
$result_get_verificationcode = mysqli_query($con, $query_get_verificationcode);

if (!$result_get_verificationcode) {
    die("Error executing query: " . mysqli_error($con));
}

$row = mysqli_fetch_assoc($result_get_verificationcode);
$username = isset($row['username']) ? $row['username'] : null;
$verification_code = isset($row['verification_code']) ? $row['verification_code'] : null;

// Redirect if no user data found
if (!$username || !$verification_code) {
    header("Location: ./forgot_password.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="style_reg.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
        <form id="verificationForm" method="POST" onsubmit="changePassword(event);">
            <div class="input-box">
                <input type="text" id="verificationCode" name="verificationCode" placeholder="Enter verification code" required>
                <span id="verificationCodeError" class="error-message"></span>
            </div>
            <div class="input-box">
                <input type="password" id="Password" name="Password" placeholder="Enter Password" required>
                <span id="PasswordError" class="error-message"></span>
            </div>
            <div class="input-box">
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                <span id="ConfirmPasswordError" class="error-message"></span>
            </div>
            <div class="input-box button">
                <input type="submit" value="Reset Password">
            </div>
        </form>
        <p id="responseMessage"></p>
    </div>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: "5000"
        };

        function changePassword(event) {
            event.preventDefault(); // Prevent form submission

            // Clear previous error messages
            $(".error-message").text("");

            const verificationCode = $("#verificationCode").val().trim();
            const password = $("#Password").val().trim();
            const confirmPassword = $("#confirmPassword").val().trim();
            let hasError = false;

            // Validate verification code
            if (verificationCode === "") {
                $("#verificationCodeError").text("Verification code is required.");
                hasError = true;
            }

            // Validate password
            if (password.length < 4) {
                $("#PasswordError").text("Password must be at least 4 characters.");
                hasError = true;
            }

            var passwordRegex =/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/ ;
          
            if (!passwordRegex.test(password)) {
            $("#PasswordError").text("Password duhet të ketë min 8 karaktere, një shkronjë të madhe, një të vogël, një numër dhe një simbol.");
            hasError = true ; 
            }  

          

            // Check if passwords match
            if (password !== confirmPassword) {
                $("#ConfirmPasswordError").text("Passwords do not match.");
                hasError = true;
            }

            if (!hasError) {
                // Make AJAX request
                $.ajax({
                    type: "POST",
                    url: "ajaxpass.php",
                    data: {
                        action: "changePassword",
                        verificationCode: verificationCode,
                        password: password,
                        confirmPassword: confirmPassword
                    },
                    success: function (response) {
                        try {
                            response = JSON.parse(response);
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => {
                                    window.location.href = "./login.php"; // Redirect to login
                                }, 2000);
                            } else {
                                toastr.error(response.message);
                            }
                        } catch (e) {
                            console.error("Error parsing response:", e);
                            toastr.error("An unexpected error occurred.");
                        }
                    },
                    error: function () {
                        toastr.error("An error occurred while processing your request.");
                    }
                });
            }
        }
    </script>
</body>
</html>