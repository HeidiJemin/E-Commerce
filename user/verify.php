<?php
session_start();
require_once('../includes/connect.php');

if(isset($_SESSION['verified']) && $_SESSION['verified'] == 1){ 
    header("location:./profile.php");
}

//
if (!isset($_SESSION['email'])) {
    header("Location: ./login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['email'])) {
        echo "Email not found in session.";
        exit;
    }

    $email = mysqli_real_escape_string($con, trim($_SESSION['email']));
    $query_get_username = "SELECT username FROM users WHERE email = '$email'";
    $result_get_username = mysqli_query($con, $query_get_username);
    $username = mysqli_fetch_assoc($result_get_username)['username'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Verification</title>
        <link rel="stylesheet" href="./css/style_reg.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="./js/inactivity.js" defer></script>
        <script>
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            function verifyCode(event) {
                event.preventDefault(); 

                var verificationCode = $("#verificationCode").val();

                var data = new FormData();
                data.append("action", "verifyCode");
                data.append('verificationCode', verificationCode);

                
                $.ajax({
                    type: "POST",
                    url: "./controllers/ajax.php",
                    async: false,
                    cache: false,
                    processData: false,
                    data: data,
                    contentType: false,
                    success: function (response, status, call) {
                        response = JSON.parse(response);
                        console.log(response);

                        if (call.status == 200) {
                            toastr.success(response.message); 
                            window.location.href = "./profile.php"; 
                        } else {
                            toastr.error(response.message); 
                            $("#responseMessage").text(response.message); 
                        }
                    },
                    error: async function (e) {
                        toastr.error("Code is wrong/expired!");
                    }
                });
            }

            function resendVerificationEmail() {
                var data = new FormData();
                data.append("action", "resendVerification");

                
                $.ajax({
                    type: "POST",
                    url: "./controllers/ajax.php",
                    async: false,
                    cache: false,
                    processData: false,
                    data: data,
                    contentType: false,
                    success: function (response, status, call) {
                        response = JSON.parse(response);
                        console.log(response);

                        if (call.status == 200) {
                            toastr.success(response.message); 
                        } else {
                            toastr.error(response.message); 
                        }
                    },
                    error: function () {
                        toastr.error("An error occurred. Please try again.");
                    }
                });
            }
        </script>
        
    </head>
    <body>
        <div class="wrapper">
            <h2>Email Verification</h2>
            <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
            <form id="verificationForm" onsubmit="verifyCode(event);">
                <div class="input-box">
                    <input type="text" id="verificationCode" name="verificationCode" placeholder="Enter verification code" required>
                    <span id="verificationCodeError" class="error-message"></span>
                </div>
                <div class="input-box button">
    <input type="submit" value="Verify" 
           style="color: black; letter-spacing: 1px; border: none; background: #ffce00; cursor: pointer; text-align: center; display: inline-block; padding: 5px 10px; border-radius: 4px; margin: 5px 0;" 
           onmouseover="this.style.background='black'; this.style.color='white';" 
           onmouseout="this.style.background='#ffce00'; this.style.color='black';">
</div>
</form>
<div class="input-box button">
    <button class="input-box button" 
            style="padding: 5px 10px; border-radius: 4px; background: #ffce00; color: black; border: none; cursor: pointer; margin: 5px 0;" 
            id="resendEmail" 
            onclick="resendVerificationEmail();" 
            onmouseover="this.style.background='black'; this.style.color='white';" 
            onmouseout="this.style.background='#ffce00'; this.style.color='black';">
        Resend Verification Email
    </button>
</div>
<div class="input-box button">
    <a href="./logout.php" 
       style="display: inline-block; color: black; text-decoration: none; letter-spacing: 1px; cursor: pointer; margin: 5px 0;" 
       onmouseover="this.style.textDecoration='underline';" 
       onmouseout="this.style.textDecoration='none';">
        Log out
    </a>
</div>

            <p id="responseMessage"></p>
        </div>
    </body>
    </html>
    <?php
} else {
    http_response_code(405); 
    echo json_encode(["message" => "Invalid request method."]);
}
?>
