<?php 
// Product Details 
// Minimum amount is $0.50 US 
// Test Stripe API configuration 

define('STRIPE_API_KEY', 'pk_test_51QfPWzD5IxD7HeGajBmJdJ03rOsok2gC5NWwcwRzJzEynALpu1FhwVhv3FbmAXH68WKq10M1rymh6jCVrPM2YR7300jpjUaFBr');  
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51QfPWzD5IxD7HeGajBmJdJ03rOsok2gC5NWwcwRzJzEynALpu1FhwVhv3FbmAXH68WKq10M1rymh6jCVrPM2YR7300jpjUaFBr'); 

define('STRIPE_SUCCESS_URL', 'http://localhost/Ecom/success.php'); 
define('STRIPE_CANCEL_URL', 'http://localhost/Ecom/cancel.php'); 

// Database configuration   
define('DB_HOST', 'localhost');  
define('DB_USERNAME', 'root');  
define('DB_PASSWORD', '');  
define('DB_NAME', 'jerseystore'); 
?>