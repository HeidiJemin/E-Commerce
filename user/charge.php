<?php
session_start();
require __DIR__ . "/../vendor/autoload.php";
require_once('../includes/connect.php');

// Redirect to cart.php if the request method is not POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}




$required_fields = ['firstname', 'lastname', 'email', 'address', 'zipcode', 'country', 'city', 'user_id', 'total_price', 'cart_items', 'phone'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        die("Missing required field: $field.");
    }
}


$firstname = htmlspecialchars($_POST['firstname']);
$lastname = htmlspecialchars($_POST['lastname']);
$email = htmlspecialchars($_POST['email']);
$address = htmlspecialchars($_POST['address']);
$zipcode = htmlspecialchars($_POST['zipcode']);
$country = htmlspecialchars($_POST['country']);
$city = htmlspecialchars($_POST['city']);
$phone = htmlspecialchars($_POST['phone']);
$user_id = htmlspecialchars($_POST['user_id']);
$total_price = (float) $_POST['total_price'];
$cart_items = json_decode($_POST['cart_items'], true);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die("Invalid email address.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Checkout</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="./js/inactivity.js" defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f9f9f9;
        }
        .checkout-container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        input {
            padding: 0.75rem;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        #card-element {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        button {
            padding: 0.75rem;
            font-size: 1rem;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            position: relative;
            overflow: hidden;
        }
        button:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }
        button:disabled::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 3px solid #fff;
            border-top-color: transparent;
            border-left-color: transparent;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        #payment-message {
            color: red;
            font-size: 0.875rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Payment Checkout</h1>
        <form id="payment-form">
            <input type="email" id="email" value="<?php echo $email; ?>" readonly>
            <div id="card-element"><!-- Stripe Card Element will be inserted here --></div>
            <input type="text" id="cardholder-name" placeholder="Cardholder's Name">
            <button id="submit-button">Pay</button>
            <p id="payment-message"></p>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            
            const postData = <?php echo json_encode([
                "firstname" => $firstname,
                "lastname" => $lastname,
                "email" => $email,
                "address" => $address,
                "zipcode" => $zipcode,
                "country" => $country,
                "city" => $city,
                "phone" => $phone,
                "user_id" => $user_id,
                "total_price" => $total_price,
                "cart_items" => $cart_items,
            ]); ?>;

            // Initialize Stripe
            const stripe = Stripe("pk_test_51QfPWzD5IxD7HeGajBmJdJ03rOsok2gC5NWwcwRzJzEynALpu1FhwVhv3FbmAXH68WKq10M1rymh6jCVrPM2YR7300jpjUaFBr"); // Replace with your Stripe public key
            const elements = stripe.elements();
            const card = elements.create("card");
            card.mount("#card-element");

            const paymentForm = document.getElementById("payment-form");
            const submitButton = document.getElementById("submit-button");
            const paymentMessage = document.getElementById("payment-message");

            paymentForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    submitButton.disabled = true;

    try {
        const cardholderName = document.getElementById("cardholder-name").value;

        if (!cardholderName.trim()) {
            paymentMessage.textContent = "Please enter the cardholder's name.";
            submitButton.disabled = false;
            return;
        }

        // Send AJAX request to create a PaymentIntent
        const response = await fetch("./controllers/ajax_payment.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(postData), 
        });

        const result = await response.json();

        if (!result.clientSecret) {
            paymentMessage.textContent = "Error creating payment intent.";
            window.location.href = "./cancel.php";  // Redirect to cancel.php
            return;
        }

        // Confirm payment using Stripe
        const { error, paymentIntent } = await stripe.confirmCardPayment(result.clientSecret, {
            payment_method: {
                card: card,
                billing_details: {
                    name: cardholderName,
                },
            },
        });

        if (error) {
            paymentMessage.textContent = `Payment failed: ${error.message}`;
            window.location.href = "./cancel.php";  
        } else if (paymentIntent.status === "succeeded") {
            
            const dbResponse = await fetch("./controllers/save_order.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    ...postData, 
                    payment_id: paymentIntent.id,
                    payment_status: paymentIntent.status,
                    
                    payment_method: 'card',
                }),
            });

            const dbResult = await dbResponse.json();

            if (dbResult.status === "success") {
                // Redirect to success page
                window.location.href = "./success.php";
            } else {
                paymentMessage.textContent = "Error saving order: " + dbResult.message;
            }
        }
    } catch (error) {
        paymentMessage.textContent = `An error occurred: ${error.message}`;
    } finally {
        submitButton.disabled = false;
    }
});


        });
    </script>
</body>
<?php
mysqli_close($con);
?>
</html>
