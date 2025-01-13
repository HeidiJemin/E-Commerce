<?php
if (isset($_SESSION['id'])) {
    // Check if the user has role_id = 1 (customer)
    if ($_SESSION['role_id'] != 1) {
        // If not role_id 1, redirect to admin page
        header("Location: admin_manage/index.php");
        exit();
    }

    // If role_id is 1, redirect to index.php
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form | CodingLab</title> 
    <link rel="stylesheet" href="login_style.css">
    <!-- Include Toastr CSS for notifications -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  </head>
  <body>
    <div class="wrapper">
      <h2>Login</h2>
      <form id="loginForm" onsubmit="loginUser(event)" novalidate>
        <div class="input-box">
          <input type="text" id="email" name="email" autocomplete="off" placeholder="Enter your email" required>
          <span id="emailError" class="error-message"></span>
        </div>
        <div class="input-box">
          <input type="password" id="password" name="password" autocomplete="off" placeholder="Enter your password" required>
          <span id="passwordError" class="error-message"></span>
        </div>
        
        <div class="options" style="display: flex; justify-content: space-between; align-items: center; margin-top: 0; margin-bottom: 10px;">
          <label class="remember-me" style="margin: 0;">
            <input type="checkbox" id="rememberMe" name="rememberMe"> Remember me
          </label>
          <a href="forgot_password.php" class="forgot-password" style="margin: 0;">Forgot Password?</a>
        </div>

        <div class="input-box button">
          <input type="Submit" value="Login Now">
        </div>
        <div class="text">
          <h3>Don't have an account? <a href="register.php">Register now</a></h3>
        </div>
      </form>
    </div>

    <script>
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      };

      function loginUser(event) {
    event.preventDefault(); // Prevent form submission

    var email = $("#email").val();
    var password = $("#password").val();
    var rememberMe = $("#rememberMe").is(":checked"); // Check Remember Me option


    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; // Simple email regex
    var error = 0;

    // Validate email
    if (isEmpty(email)) {
        $("#email").addClass("error");
        $("#emailError").text("Email cannot be empty");
        error++;
    } else if (!emailRegex.test(email)) {
        $("#email").addClass("error");
        $("#emailError").text("Invalid email format");
        error++;
    } else {
        $("#email").removeClass("error");
        $("#emailError").text(""); // Clear the error message
    }

    // Validate password
    if (isEmpty(password)) {
        $("#password").addClass("error");
        $("#passwordError").text("Password cannot be empty");
        error++;
    } else {
        $("#password").removeClass("error");
        $("#passwordError").text(""); // Clear the error message
    }

    // If no errors, proceed with AJAX request
    if (error === 0) {
        var data = new FormData();
        data.append("action", "login");
        data.append("email", email);
        data.append("password", password);
        data.append("rememberMe", rememberMe); // Pass Remember Me status


        // AJAX call to the backend
        $.ajax({
            type: "POST",
            url: "ajax.php",
            async: false,
            cache: false,
            processData: false,
            data: data,
            contentType: false,
            success: function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        window.location.href = response.verified ? response.location : "./verify.php";
                    }, 2500);
                } else {
                    toastr.error(response.message); // Display error message from server
                }
            },
            error: function() {
                toastr.error("An error occurred. Please try again.");
            }
        });
    }
}

function isEmpty(value) {
    return (value === undefined || value === null || value.trim() === "");
}

    </script>
  </body>
</html>
