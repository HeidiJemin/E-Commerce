<?php
session_start();
include_once('includes/connect.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request (if needed)
    if (!isset($_SESSION['email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Email not found in session.']);
        exit;
    }


}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
      $(document).ready(function () {
        $("#resetPassword").click(function (event) {
          event.preventDefault();

          var email = $("#typeEmail").val().trim();
          var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

          if (!emailRegex.test(email)) {
            toastr.error("Please enter a valid email address.");
            return; // Prevent further processing
          }

          $.ajax({
    type: "POST",
    url: "ajaxpass.php",
    data: { action: "resetPassword", email: email },
    success: function (response) {
        try {
            // Check if response is valid JSON
            if (!response) {
                throw new Error("Empty response from server.");
            }

            response = JSON.parse(response); // Parse the JSON

            if (response.status === "success") {
                toastr.success(response.message);
                setTimeout(() => {
                    window.location.href = "verifyPassword.php";
                }, 2000);
            } else {
                toastr.error(response.message);
            }
        } catch (e) {
            console.error("Error parsing response:", e);
            toastr.error("An unexpected error occurred.");
        }
    },
    error: function (xhr, status, error) {
        console.error("AJAX error:", error);
        toastr.error("An error occurred while processing your request. Please try again later.");
    },
});

        });
      });

      // Automatically show the modal on page load
      document.addEventListener("DOMContentLoaded", function () {
        var modal = new mdb.Modal(document.getElementById("exampleModal"));
        modal.show();
      });
    </script>
  </head>
  <body>
    <!-- Modal for Email Input -->
    <div class="modal top fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true" data-mdb-backdrop="static" data-mdb-keyboard="false">
      <div class="modal-dialog" style="width: 300px;">
        <div class="modal-content text-center">
          <div class="modal-header h5 text-white bg-primary justify-content-center">
            Password Reset
          </div>
          <div class="modal-body px-5">
            <p class="py-2">
              Enter your email address and we'll send you an email with instructions to reset your password.
            </p>
            <div data-mdb-input-init class="form-outline">
              <input type="email" id="typeEmail" class="form-control my-3" />
              <label class="form-label" for="typeEmail">Email input</label>
            </div>
            <a href="#" id="resetPassword" data-mdb-ripple-init class="btn btn-primary w-100">Reset password</a>
            <div class="d-flex justify-content-between mt-4">
              <a class="" href="login.php">Login</a>
              <a class="" href="register.php">Register</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toastr Configuration -->
    <script>
      toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: true,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
      };
    </script>
  </body>
</html>

