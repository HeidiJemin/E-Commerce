<?php
session_start();
require_once('../includes/connect.php');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$adminPath = '../admin_manage/index.php';


if (isset($_SESSION['id'])) {
    if ($_SESSION['role_id'] != 1) {
        header("Location: $adminPath");
    } else {
        header("Location: index.php");
    }
    exit();
}


if (isset($_COOKIE['remember_token'])) {
    $rememberToken = mysqli_real_escape_string($con, $_COOKIE['remember_token']);

    
    $query = "SELECT user_id, email, remember_token, verified, username, role_id 
              FROM users WHERE remember_token = '$rememberToken'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        
        die("Database query failed: " . mysqli_error($con));
    }

    if (mysqli_num_rows($result) === 1) {
        
        $user = mysqli_fetch_assoc($result);

        $_SESSION['id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['date_time'] = time();
        $_SESSION['name'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['verified'] = $user['verified'];
        $_SESSION['username'] = $user['username'];

        
        if ($_SESSION['role_id'] != 1) {
            header("Location: $adminPath");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <link rel="stylesheet" href="./css/style_reg.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  <link rel="stylesheet" href="style_reg.css">

  
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
    function register_user(event) {
        event.preventDefault(); 

    var name = $("#name").val();
    var surname = $("#surname").val();
    var username = $("#username").val();
    var email = $("#email").val();
    var password = $("#password").val();
    var confirmPassword = $("#conf_password").val();
    var termsAccepted = $("#terms").is(":checked");

    var nameRegex = /^[A-Z][a-zA-Z ]{2,19}$/; 
    var usernameRegex = /^[a-zA-Z0-9-_]{3,20}$/; 
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; 
    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/; 

    var error = 0;

    
    if (!nameRegex.test(name)) {
        $("#name").addClass("error");
        $("#nameError").text("Emri duhet të fillojë me shkronjë të madhe, minimumi 3 karaktere.No numbers!");
        error++;
    } else {
        $("#name").removeClass("error");
        $("#nameError").text("");
    }

    
    if (!nameRegex.test(surname)) {
        $("#surname").addClass("error");
        $("#surnameError").text("Mbiemri duhet të fillojë me shkronjë të madhe, minimumi 3 karaktere.No numbers!");
        error++;
    } else {
        $("#surname").removeClass("error");
        $("#surnameError").text("");
    }

    
    if (!usernameRegex.test(username)) {
        $("#username").addClass("error");
        $("#usernameError").text("Username duhet të jetë 3-20 karaktere dhe mund të përmbajë vetëm shkronja, numra, '-' ose '_'.");
        error++;
    } else {
        $("#username").removeClass("error");
        $("#usernameError").text("");
    }

    
    if (!emailRegex.test(email)) {
        $("#email").addClass("error");
        $("#emailError").text("Ju lutem vendosni një email të vlefshëm.");
        error++;
    } else {
        $("#email").removeClass("error");
        $("#emailError").text("");
    }

    
    if (!passwordRegex.test(password)) {
        $("#password").addClass("error");
        $("#passwordError").text("Password duhet të ketë min 8 karaktere, një shkronjë të madhe, një të vogël, një numër dhe një simbol.");
        error++;
    } else if (password !== confirmPassword) {
        $("#password").addClass("error");
        $("#conf_password").addClass("error");
        $("#passwordError").text("Passwordet nuk përputhen.");
        $("#confirmPasswordError").text("Passwordet nuk përputhen.");
        error++;
    } else {
        $("#password").removeClass("error");
        $("#conf_password").removeClass("error");
        $("#passwordError").text("");
        $("#confirmPasswordError").text("");
    }

    
    if (!termsAccepted) {
        $("#termsError").text("Duhet të pranoni termat dhe kushtet.");
        error++;
    } else {
        $("#termsError").text("");
    }
    
    if (error == 0) {
        var data = new FormData();
        data.append("action", "register");
        data.append("name", name);
        data.append("surname", surname);
        data.append("username", username);
        data.append("email", email);
        data.append("password", password);
        data.append("conf_password", confirmPassword);

        
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
                    setTimeout(function () {
                        window.location.href = "./verify.php"; 
                    }, 2500);
                } else {
                    $("#" + response.tagError).text(response.message); 
                    $("#" + response.tagElement).addClass('error'); 
                }
            },
            error: function () {
                showMessage("An error occurred. Please try again.", "error");
            }
        });
        }
    }
   

    function isEmpty(value) {
    
    return (value === undefined || value === null || value.trim() === "");
}


    
    function showMessage(message, type) {
        const messageElement = $("<div>").addClass("message " + type).text(message);
        $("body").append(messageElement);
        setTimeout(() => messageElement.fadeOut(() => messageElement.remove()), 3000);
    }
</script>

</head>
<body>
  <div class="wrapper">
    <h2>Registration</h2>
    <form id="registrationForm" onsubmit="register_user(event);" novalidate>
      <div class="input-box">
        <input type="text" id="name" placeholder="Enter your first name" name="name">
        <span id="nameError" class="error-message"></span>
      </div>
      <div class="input-box">
        <input type="text" id="surname" placeholder="Enter your surname" name="surname">
        <span id="surnameError" class="error-message"></span>
      </div>
      <div class="input-box">
        <input type="text" id="username" placeholder="Enter your username" name="username">
        <span id="usernameError" class="error-message"></span>
      </div>
      <div class="input-box">
        <input type="text" id="email" name="email" placeholder="Enter your email">
        <span id="emailError" class="error-message"></span>
      </div>
      <div class="input-box">
        <input type="password" id="password" name="password" placeholder="Create password">
        <span id="passwordError" class="error-message"></span>
      </div>
      <div class="input-box">
        <input type="password" id="conf_password" name="conf_password" placeholder="Confirm password">
        <span id="confirmPasswordError" class="error-message"></span>
      </div>
      <div class="policy">
  <input type="checkbox" id="terms">
  <h3>I accept all terms & conditions</h3>
</div>
<div id="termsError" class="error-message"></div>
<div class="input-box button">
  <input type="submit" value="Register Now">
</div>
<div class="text">
  <h3>Already have an account? <a href="login.php">Login here</a></h3>
</div>

    </form>
  </div>
</body>
<script>
     document.querySelectorAll("#name, #surname").forEach((input) => {
  input.addEventListener("input", (event) => {
    const value = event.target.value;
    event.target.value = value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
  });
});
</script>
</html>
