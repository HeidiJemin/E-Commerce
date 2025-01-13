<?php
session_start();
include_once('../includes/connect.php');
include_once('functions/common_function.php');

// Ensure database connection is established
if (!$con) {
    die("Error: " . mysqli_connect_error());
}

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['id']; // Get the logged-in user's ID

// Fetch the user's data
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error: " . mysqli_error($con));
}

if ($result->num_rows === 0) {
    die("User not found!");
}

$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Update</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="profilestyle.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="profilestyle.css">

    
    <style>
       
.navbar .nav-link {
  color: white !important; /* White text for navbar links */
}
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Istok Web", sans-serif;
}
    </style>

</head>
<body>

    <?php include('../includes/header.php'); ?> <!-- Including the header -->

    <div class="container rounded bg-white mt-5 mb-5">
        <div class="row">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <img id="profileImage" class="rounded-circle mt-5" width="150px" 
                        src="<?= isset($row['foto']) && !empty($row['foto']) ? $row['foto'] : 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg'; ?>">
                    <span class="font-weight-bold"><?= htmlspecialchars($row['username']) ?></span>
                    <span class="text-black-50"><?= htmlspecialchars($row['email']) ?></span>
                    <input type="file" id="fileInput" class="form-control mt-3" accept="image/*" style="display: none;">
                    <button id="uploadButton" 
        style="background: #ffce00; color: black; box-shadow: none; border: none;" 
        class="btn mt-2" 
        onmouseover="this.style.background='black'; this.style.color='white';" 
        onmouseout="this.style.background='#ffce00'; this.style.color='black';" 
        onfocus="this.style.boxShadow='none';" 
        onblur="this.style.background='#ffce00'; this.style.color='black'; this.style.boxShadow='none';">
    Choose Photo
</button>


                </div>
            </div>

            <div class="col-md-5 border-right">
                <div class="p-3 py-5">
                    <h4 class="text-right">Profile Settings</h4>
                    <form id="profileForm">
                        <input type="hidden" id="id" name="id" value="<?php echo $user_id ?>">
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="labels">Name</label>
                                <input type="text" class="form-control" placeholder="First Name" value="<?= htmlspecialchars($row['name']) ?>" id="name">
                                <span id="nameError" class="text-danger"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="labels">Surname</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($row['surname']) ?>" placeholder="Surname" id="surname">
                                <span id="surnameError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Username</label>
                                <input type="text" class="form-control" placeholder="Enter Username" value="<?= htmlspecialchars($row['username']) ?>" id="username">
                                <span id="usernameError" class="text-danger"></span>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Email</label>
                                <input type="text" class="form-control" placeholder="Email Address" value="<?= htmlspecialchars($row['email']) ?>" id="email">
                                <span id="emailError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="button" onclick="updateUser()">Save Profile</button>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="labels">Current Password</label>
                            <input type="password" class="form-control" placeholder="Enter your current password" id="currentPassword">
                            <span id="currentPasswordError" class="text-danger"></span>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="labels">New Password</label>
                            <input type="password" class="form-control" placeholder="Enter your new password" id="newPassword">
                            <span id="newPasswordError" class="text-danger"></span>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="labels">Confirm Password</label>
                            <input type="password" class="form-control" placeholder="Confirm your new password" id="confirmPassword">
                            <span id="confirmPasswordError" class="text-danger"></span>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="button" onclick="updatePassword()">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
const fileInput = document.getElementById('fileInput');
const uploadButton = document.getElementById('uploadButton');
const profileImage = document.getElementById('profileImage');
let uploadedFile = null;

uploadButton.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => profileImage.src = e.target.result;
        reader.readAsDataURL(file);
        uploadedFile = file;
    }
});

function updateUser() {
    const name = $("#name").val();
    const surname = $("#surname").val();
    const username = $("#username").val();
    const email = $("#email").val();
    const id = $("#id").val();

    const nameRegex = /^[A-Z][a-zA-Z ]{2,19}$/; // Starts with a capital letter, 3-20 characters
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    let error = 0;

    // Validate name
    if (!nameRegex.test(name)) {
        $("#name").addClass('error');
        $("#nameError").text("Name must start with a capital letter and be 3-20 characters long.");
        error++;
    } else {
        $("#name").removeClass('error');
        $("#nameError").text("");
    }

    // Validate surname
    if (!nameRegex.test(surname)) {
        $("#surname").addClass('error');
        $("#surnameError").text("Surname must start with a capital letter and be 3-20 characters long.");
        error++;
    } else {
        $("#surname").removeClass('error');
        $("#surnameError").text("");
    }

    // Validate username
    if (!username || username.trim() === "") {
        $("#username").addClass('error');
        $("#usernameError").text("Username cannot be empty.");
        error++;
    } else {
        $("#username").removeClass('error');
        $("#usernameError").text("");
    }

    // Validate email
    if (!emailRegex.test(email)) {
        $("#email").addClass('error');
        $("#emailError").text("Enter a valid email address.");
        error++;
    } else {
        $("#email").removeClass('error');
        $("#emailError").text("");
    }

    if (error > 0) {
        return; // Stop if validation fails
    }

    // Prepare form data for AJAX
    const formData = new FormData();
    formData.append('action', 'updateUser'); // Add action parameter here
    formData.append('id', id);
    formData.append('name', name);
    formData.append('surname', surname);
    formData.append('username', username);
    formData.append('email', email);
    if (uploadedFile) formData.append('foto', uploadedFile);

    // AJAX request
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            const res = JSON.parse(response);

            if (res.status === 'error') {
                if (res.field === 'email') {
                    // Highlight email input and show the error message
                    $("#email").addClass('error');
                    $("#emailError").text(res.message);
                } else {
                    alert(res.message);
                }
            } else if (res.status === 'success') {
                // Differentiate between email update and general update
                if (res.redirect) {
                    alert(res.message + " Please verify your new email address.");
                    window.location.href = res.redirect;
                } else {
                    alert(res.message);
                    location.reload();
                }
            }
        },
        error: function () {
            alert("An error occurred while updating.");
        }
    });
}

function updatePassword() {
    var userId = $("#id").val(); // Ensure user ID is included
    var currentPassword = $("#currentPassword").val().trim();
    var newPassword = $("#newPassword").val().trim();
    var confirmPassword = $("#confirmPassword").val().trim();

    // Clear previous error messages
    $("#currentPasswordError").text("");
    $("#newPasswordError").text("");
    $("#confirmPasswordError").text("");

    var error = false;

    // Validate current password
    if (currentPassword === "") {
        $("#currentPasswordError").text("Current password is required.");
        error = true;
    }

    // Validate new password
    var passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
    if (!passwordRegex.test(newPassword)) {
        $("#newPasswordError").text("Password must be at least 8 characters long, include at least one letter and one number.");
        error = true;
    }

    // Validate confirm password
    if (newPassword !== confirmPassword) {
        $("#confirmPasswordError").text("Passwords do not match.");
        error = true;
    }

    if (error) return; // Stop if there are validation errors

    // Send the AJAX request to update the password
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {
            action: "updatePassword",
            id: userId, // Pass user ID
            currentPassword: currentPassword,
            newPassword: newPassword
        },
        success: function (response) {
            var res = JSON.parse(response);
            if (res.success) {
                // Show success message
                alert("Password updated successfully!");

                // Clear input fields
                $("#currentPassword").val("");
                $("#newPassword").val("");
                $("#confirmPassword").val("");
            } else {
                // Show the error message under the "Current Password" field
                $("#currentPasswordError").text(res.message);
            }
        },
        error: function () {
            alert("An error occurred while updating the password.");
        }
    });
}

    </script>
</body>
</html>