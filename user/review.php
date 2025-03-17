<?php
session_start();
require_once('../includes/connect.php');
require_once('functions/common_function.php');


if (!$con) {
    die("Error: " . mysqli_connect_error());
}

require_once('../includes/rememberme.php');

$name = ""; 
$email = ""; 

if (!isset($_GET['produkt_id']) || intval($_GET['produkt_id']) <= 0) {
    
    header("Location: index.php");
    exit();
}


$produkt_id = intval($_GET['produkt_id']);


if (isset($_SESSION["id"])) {
    $user_id = $_SESSION['id']; 
    $query = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error: " . mysqli_error($con));
    }

    if ($result->num_rows > 0) {
        $user = mysqli_fetch_assoc($result); 
        $name = htmlspecialchars($user['name']); 
        $email = htmlspecialchars($user['email']); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
<!-- Include jQuery (required for toastr and your script) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Include Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Include Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="./js/inactivity.js" defer></script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Testimonial</title>
    <link rel="stylesheet" href="./css/reviewStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <h2>Customer Testimonial</h2>
        <p>We would love to hear about your experience with our service!</p>
        <form id="testimonialForm">
        <input type="hidden" id="produkt_id" name="produkt_id" value="<?= $produkt_id; ?>"> <!-- Include produkt_id -->
        <script>
            console.log(<?= $produkt_id; ?>); 
        </script>
            <!-- Name Field -->
            <div class="input-box">
                <input type="text" id="name" name="name" placeholder="Your full name" value="<?= $name; ?>" required>
                <span class="error" id="nameError"></span>
            </div>

            <!-- Email Field -->
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="example@domain.com" value="<?= $email; ?>" required>
                <span class="error" id="emailError"></span>
            </div>

            <!-- Testimonial Field -->
            <div class="input-box">
                <textarea id="testimonial" name="testimonial" placeholder="Share your experience with us..." required></textarea>
                <span class="error" id="testimonialError"></span>
            </div>

            <!-- Overall Rating -->
            <p>Overall Rating</p>
            <div class="rating">
                <i class="fa fa-star" data-value="1"></i>
                <i class="fa fa-star" data-value="2"></i>
                <i class="fa fa-star" data-value="3"></i>
                <i class="fa fa-star" data-value="4"></i>
                <i class="fa fa-star" data-value="5"></i>
            </div>
            <input type="hidden" id="rating" name="rating" value="">

          

            <!-- Satisfaction Level -->
            <p>Satisfaction Level</p>
            <div class="satisfaction">
                <label>
                    <input type="radio" name="satisfaction" value="1" required>
                    <span>😠</span>
                </label>
                <label>
                    <input type="radio" name="satisfaction" value="2">
                    <span>🙁</span>
                </label>
                <label>
                    <input type="radio" name="satisfaction" value="3">
                    <span>😐</span>
                </label>
                <label>
                    <input type="radio" name="satisfaction" value="4">
                    <span>🙂</span>
                </label>
                <label>
                    <input type="radio" name="satisfaction" value="5">
                    <span>😁</span>
                </label>
            </div>

            <!-- Recommendation -->
            <p>Would you recommend us?</p>
            <div class="recommend">
                <label>
                    <input type="radio" name="recommend" value="yes" required> Yes
                </label>
                <label>
                    <input type="radio" name="recommend" value="no"> No
                </label>
            </div>

            <!-- Consent Checkbox -->
            <div class="consent">
                <label>
                    <input type="checkbox" name="consent" required>
                    I consent to the use of my testimonial as outlined in the terms and conditions.
                </label>
            </div>

            <!-- Submit Button -->
            <div class="input-box button">
                <input type="submit" value="Submit Testimonial">
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
  $(document).ready(function () {
    // Handle star rating
    $('.rating .fa-star').on('click', function () {
        let rating = $(this).data('value');
        $('#rating').val(rating);

        $('.rating .fa-star').removeClass('active');
        $(this).prevAll().addBack().addClass('active');
    });

    // Form submission
    $('#testimonialForm').on('submit', function (e) {
        e.preventDefault();

        let isValid = true;
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const testimonial = $('#testimonial').val().trim();

        // Name validation (must start with a capital letter)
        const nameRegex = /^[A-Z][a-zA-Z\s]*$/;
        if (name === '') {
            $('#nameError').text('Name cannot be empty.');
            isValid = false;
        } else if (!nameRegex.test(name)) {
            $('#nameError').text('Name must start with a capital letter.');
            isValid = false;
        } else {
            $('#nameError').text('');
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === '') {
            $('#emailError').text('Email cannot be empty.');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            $('#emailError').text('Enter a valid email address.');
            isValid = false;
        } else {
            $('#emailError').text('');
        }

        // Testimonial validation
        if (testimonial === '') {
            $('#testimonialError').text('Testimonial cannot be empty.');
            isValid = false;
        } else if (testimonial.length < 20) {
            $('#testimonialError').text('Testimonial must be at least 20 characters long.');
            isValid = false;
        } else {
            $('#testimonialError').text('');
        }

        if (!isValid) return; // Stop form submission if validation fails

        // Create FormData object to send data
        let data = new FormData(this);
        data.append("action", "submit_testimonial");
        data.append("name", name);
        data.append("email", email);
        data.append("testimonial", testimonial);
        data.append("rating", $('#rating').val());
       // data.append("experienceDate", $('#experienceDate').val());
        data.append("satisfaction", $('input[name="satisfaction"]:checked').val());
        data.append("recommend", $('input[name="recommend"]:checked').val());
        data.append("consent", $('input[name="consent"]:checked').val());
        data.append("produkt_id", parseInt($('#produkt_id').val()));

        // AJAX call to backend
        $.ajax({
            type: "POST",
            url: "./controllers/ajaxtestimonial.php",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (response) {
                try {
                    response = JSON.parse(response);

                    if (response.status === 'success') {
                        toastr.success(response.message, "Success");
                        if (response.redirect) {
                            setTimeout(() => {
                                window.location.href = response.redirect; 
                            }, 2000); 
                        }
                    } else if (response.status === 'exists') {
                        toastr.warning(response.message, "Cannot make");
                        setTimeout(() => {
                                window.location.href = "../user/index.php"; 
                            }, 2000); 
                    } else {
                        toastr.error(response.message || "An unknown error occurred.", "Error");
                    }
                } catch (e) {
                    console.error("Error parsing JSON response:", e);
                    toastr.error("Unexpected error occurred.", "Error");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                toastr.error("A network error occurred. Please try again.", "Network Error");
            }
        });
    });
});

</script>

</body>
</html>