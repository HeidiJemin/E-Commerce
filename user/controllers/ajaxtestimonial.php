<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('../../includes/connect.php');
if ($_POST['action'] == 'submit_testimonial') {



$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$testimonial = isset($_POST['testimonial']) ? htmlspecialchars($_POST['testimonial']) : '';
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

$satisfaction = isset($_POST['satisfaction']) ? htmlspecialchars($_POST['satisfaction']) : '';
$recommend = isset($_POST['recommend']) ? htmlspecialchars($_POST['recommend']) : '';
$consent = isset($_POST['consent']) && $_POST['consent'] === 'on' ? 1 : 0;
$produkt_id = isset($_POST['produkt_id']) ? intval($_POST['produkt_id']) : 0;


error_log("Sanitized Inputs: " . print_r([
    'name' => $name,
    'email' => $email,
    'testimonial' => $testimonial,
    'rating' => $rating,
    'satisfaction' => $satisfaction,
    'recommend' => $recommend,
    'produkt_id' => $produkt_id,
], true));


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


$experienceDate = date('Y-m-d'); 


$testimonial = mysqli_real_escape_string($con, $testimonial);
$rating = mysqli_real_escape_string($con, $rating);
$experienceDate = mysqli_real_escape_string($con, $experienceDate);
$satisfaction = mysqli_real_escape_string($con, $satisfaction);
$recommend = mysqli_real_escape_string($con, $recommend);


$user_id = intval($_SESSION['id']);  
$produkt_id = intval($produkt_id); 

$checkQuery = "SELECT * FROM testimonials WHERE user_id = $user_id AND produkt_id = $produkt_id";
$checkOrderQuery = "SELECT oi.produkt_id 
                    FROM order_items oi 
                    JOIN orders o ON oi.order_id = o.id 
                    WHERE oi.produkt_id = $produkt_id 
                    AND o.user_id = $user_id";


$checkResult = mysqli_query($con, $checkQuery);

if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    http_response_code(200);
    echo json_encode([
        'status' => 'exists',
        'success' => false,
        'message' => 'You have already submitted a testimonial for this product.'
    ]);
    mysqli_close($con);
    exit;
}

$query_order_result = mysqli_query($con, $checkOrderQuery); 

if(mysqli_num_rows($query_order_result)==0){ 
    http_response_code(200);
    echo json_encode([
        'status' => 'exists',
        'success' => false,
        'message' => 'You cannot make a review. You did not buy yet !'
    ]);
    mysqli_close($con);
    exit;
}




$query = "INSERT INTO testimonials (user_id,  testimonial, rating, experience_date, satisfaction, recommend, produkt_id,consent)
          VALUES ($user_id, '$testimonial', $rating, '$experienceDate', '$satisfaction', '$recommend', $produkt_id,$consent)";

error_log("SQL Query: $query");

if (mysqli_query($con, $query)) {
    http_response_code(response_code: 200);
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
        'error' => mysqli_error($con), 
    ]);
}
}

       else {
            http_response_code(405); 
            echo json_encode(["message" => "Invalid request method."]);
        }

        mysqli_close($con);


?>
