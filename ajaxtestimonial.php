<?php
// Start the session if it has not been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('includes/connect.php'); // ndryshim sepse nuk eshte eficente te besh vetem include
if ($_POST['action'] == 'submit_testimonial') {


// Extract and sanitize inputs
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$testimonial = isset($_POST['testimonial']) ? htmlspecialchars($_POST['testimonial']) : '';
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$experienceDate = isset($_POST['experienceDate']) ? htmlspecialchars($_POST['experienceDate']) : '';
$satisfaction = isset($_POST['satisfaction']) ? htmlspecialchars($_POST['satisfaction']) : '';
$recommend = isset($_POST['recommend']) ? htmlspecialchars($_POST['recommend']) : '';
$consent = isset($_POST['consent']) && $_POST['consent'] === 'on' ? 1 : 0;
$produkt_id = isset($_POST['produkt_id']) ? intval($_POST['produkt_id']) : 0;

// Debug: Log sanitized inputs
error_log("Sanitized Inputs: " . print_r([
    'name' => $name,
    'email' => $email,
    'testimonial' => $testimonial,
    'rating' => $rating,
    'experience_date' => $experienceDate,
    'satisfaction' => $satisfaction,
    'recommend' => $recommend,
    'produkt_id' => $produkt_id,
], true));

// Validate inputs
if (empty($name) || empty($email) || empty($testimonial)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all the required fields.']);
    exit;
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
} elseif (!is_numeric($rating) || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid rating between 1 and 5.']);
    exit;
}

// Sanitize inputs for SQL
$name = mysqli_real_escape_string($con, $name);
$email = mysqli_real_escape_string($con, $email);
$testimonial = mysqli_real_escape_string($con, $testimonial);
$rating = mysqli_real_escape_string($con, $rating);
$experienceDate = mysqli_real_escape_string($con, $experienceDate);
$satisfaction = mysqli_real_escape_string($con, $satisfaction);
$recommend = mysqli_real_escape_string($con, $recommend);


// Check if email already exists
$checkQuery = "SELECT id FROM testimonials WHERE email = '$email'";
$checkResult = mysqli_query($con, $checkQuery);

if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A testimonial with this email already exists.']);
    exit;
}

// Insert the testimonial
$query = "INSERT INTO testimonials (name, email, testimonial, rating, experience_date, satisfaction, recommend, produkt_id,consent)
          VALUES ('$name', '$email', '$testimonial', $rating, '$experienceDate', '$satisfaction', '$recommend', $produkt_id,$consent)";

// Debug: Log the query
error_log("SQL Query: $query");

if (mysqli_query($con, $query)) {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'success' => true,
        'message' => 'Thank you for your testimonial!',
        'redirect' => './thank_you.php',
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit your testimonial. Please try again.',
        'error' => mysqli_error($con), // Include this only during debugging
    ]);
}
}

       else {
            http_response_code(405); // Invalid method
            echo json_encode(["message" => "Invalid request method."]);
        }

        ?>