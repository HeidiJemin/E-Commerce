<?php
session_start();
require_once('../includes/connect.php');
require_once('functions/common_function.php');

require_once('../includes/rememberme_verify.php');

$user_id = $_SESSION['id']; // Retrieve user ID from session

// Fetch cart details with size and stock
$cart_query = "
    SELECT c.cart_item_id, c.produkt_id, c.user_id, c.quantity, c.size_id, p.produkt_name, p.produkt_image1, p.produkt_price, s.stock, s.size
    FROM `cart` c
    JOIN `produkt` p ON c.produkt_id = p.produkt_id
    JOIN `sizes` s ON c.size_id = s.size_id
    WHERE c.user_id = '$user_id'
";

$cart_result = mysqli_query($con, $cart_query);

if (!$cart_result || mysqli_num_rows($cart_result) === 0) {
    $_SESSION['cart_error'] = "Your cart is empty.";
    header("Location: cart.php");
    exit;
}


while ($item = mysqli_fetch_assoc($cart_result)) {
    $produkt_id = (int) $item['produkt_id'];
    $size_id = (int) $item['size_id'];
    $size = htmlspecialchars($item['size']);
    $quantity_requested = max(1, (int) $item['quantity']);
    $available_stock = (int) $item['stock'];

    
    if ($quantity_requested > $available_stock) {
        
        header("Location: cart.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>E-Commerce Website - Checkout</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
    <script src="./inactivity.js" defer></script>
    <link href="./css/checkout.css" rel="stylesheet">
    <script src="./js/inactivity.js" defer></script>
  
  
</head>
<body>
<?php include("../includes/header.php"); ?>

<div class="card">
  <div class="card-body">
    <h1 class="text-center py-5">Checkout</h1>
    <div class="container">
      <?php if (!isset($_SESSION['id'])) { ?>
        <h1 class="text-center pt-5 pb-3">You are not logged in.</h1>
        <div class="d-flex justify-content-center">
          <a href="login.php" class="btn btn-primary">Click here to log in</a>
        </div>
      <?php } else {
        $user_id = $_SESSION['id'];
        $cart_query = "
          SELECT c.produkt_id, c.quantity, p.produkt_name, p.produkt_price, p.produkt_image1, s.size,c.size_id 
          FROM `cart` c
          JOIN `produkt` p ON c.produkt_id = p.produkt_id
          JOIN `sizes` s ON c.size_id = s.size_id
          WHERE c.user_id = '$user_id'
        ";
        $cart_items_result = mysqli_query($con, $cart_query);
        $subtotal = 0;
        $shipping_cost = 10.00; 

        if (mysqli_num_rows($cart_items_result) > 0) { ?>
          <form id="checkout_form" action="charge.php" method="POST" novalidate>
    <div class="row">
        <!-- Address Section -->
        <div class="col-md-7">
            <h3>Shipping Address and Details</h3>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="firstname" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name">
                    <span id="firstnameError" class="text-danger"></span>
                </div>
                <div class="col-md-6">
                    <label for="lastname" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name">
                    <span id="lastnameError" class="text-danger"></span>
                </div>
            </div>
            <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control" id="country" name="country" placeholder="Country">
                <span id="countryError" class="text-danger"></span>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Mobile Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                <span id="phoneError" class="text-danger"></span>
            </div>
            <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    
    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $_SESSION['email'] ?? ''; ?>" readonly>
    <span id="emailError" class="text-danger"></span>
</div>
            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="City">
                <span id="cityError" class="text-danger"></span>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" placeholder="Address"></textarea>
                <span id="addressError" class="text-danger"></span>
            </div>
            <div class="mb-3">
                <label for="zipcode" class="form-label">Zip Code</label>
                <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="Zip Code">
                <span id="zipcodeError" class="text-danger"></span>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="col-md-5 p-3" style="background: #f5f5f5;">
            <h3>Cart Summary</h3>
            <hr>
            <ul class="list-group list-group-flush">
                <?php
                $cart_items = [];
                while ($item = mysqli_fetch_assoc($cart_items_result)) {
                    $total_price = $item['produkt_price'] * $item['quantity'];
                    $subtotal += $total_price;
                    
                    // Save cart items 
                    $cart_items[] = [
                        'produkt_id' => $item['produkt_id'],
                        'item_name' => $item['produkt_name'],
                        'quantity' => $item['quantity'],
                        'size' => $item['size'],
                        'size_id' => $item['size_id'],
                        'price' => $item['produkt_price'],
                        'total_price' => $total_price
                    ];
                ?>
                    <li class="list-group-item d-flex align-items-center">
                        <img src="<?= '../admin_manage/produkt_image/' . htmlspecialchars($item['produkt_image1']); ?>"
                             alt="<?= htmlspecialchars($item['produkt_name']); ?>" width="60">
                        <div class="flex-grow-1 ms-3">
                            <p>
                                <?= htmlspecialchars($item['produkt_name']); ?> (<?= htmlspecialchars($item['size']); ?>)
                                <br>
                                $<?= number_format($item['produkt_price'], 2); ?> x <?= htmlspecialchars($item['quantity']); ?>
                            </p>
                        </div>
                        <strong>$<?= number_format($total_price, 2); ?></strong>
                    </li>
                <?php } ?>
            </ul>
            <hr>
            <div class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <span>$<?= number_format($subtotal, 2); ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Shipping:</span>
                <?php 
                    if($subtotal >= 50){
                        $shipping_cost = 0;
                    }else{
                        $shipping_cost = 10;
                    }
                    ?>
                <span>$<?= number_format($shipping_cost, 2); ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <span>Total Price:</span>
                <span class="fw-bold">$<?= number_format($subtotal + $shipping_cost, 2); ?></span>
            </div>
            <hr>

            
            <input type="hidden" name="user_id" value="<?= $user_id; ?>">
            <input type="hidden" name="total_price" value="<?= $subtotal + $shipping_cost; ?>">
            <input type="hidden" name="cart_items" value='<?= json_encode($cart_items); ?>'>

            <button type="submit" class="btn btn-success btn-lg w-100">Proceed to Payment</button>
        </div>
    </div>
</form>

                    <?php } else { ?>
                        <h3 class="text-center">Your cart is empty!</h3>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
    </div>


<?php include("../includes/footer.php"); ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ENjdO4Dr2bkBIFxQpeoFzQGRo4sV/18sb1sV5tQcKxXgF1Yh/tJw3oXZz5A7Bz4T" crossorigin="anonymous"></script>
<script>
    document.querySelectorAll("input[type='text'], textarea").forEach((input) => {
  input.addEventListener("input", (event) => {
    const value = event.target.value;
    if (value.length > 0) {
      event.target.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
  });
});
$('#checkout_form').on("submit", function (event) {
    let error = 0;
    const isEmpty = (value) => value.trim() === "";

    
    const firstname = $("#firstname").val();
    if (isEmpty(firstname)) {
        $("#firstname").addClass("error");
        $("#firstnameError").text("First Name cannot be empty");
        error++;
    } else {
        $("#firstname").removeClass("error");
        $("#firstnameError").text("");
    }

    
    const lastname = $("#lastname").val();
    if (isEmpty(lastname)) {
        $("#lastname").addClass("error");
        $("#lastnameError").text("Last Name cannot be empty");
        error++;
    } else {
        $("#lastname").removeClass("error");
        $("#lastnameError").text("");
    }

    
    const country = $("#country").val();
    if (isEmpty(country)) {
        $("#country").addClass("error");
        $("#countryError").text("Country cannot be empty");
        error++;
    } else {
        $("#country").removeClass("error");
        $("#countryError").text("");
    }

    // Validate Phone (10-digit number)
    const phone = $("#phone").val();
    const phonePattern = /^\d{10}$/;
    if (isEmpty(phone)) {
        $("#phone").addClass("error");
        $("#phoneError").text("Phone cannot be empty");
        error++;
    } else if (!phonePattern.test(phone)) {
        $("#phone").addClass("error");
        $("#phoneError").text("Invalid phone format");
        error++;
    } else {
        $("#phone").removeClass("error");
        $("#phoneError").text("");
    }

    
    const email = $("#email").val();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (isEmpty(email)) {
        $("#email").addClass("error");
        $("#emailError").text("Email cannot be empty");
        error++;
    } else if (!emailPattern.test(email)) {
        $("#email").addClass("error");
        $("#emailError").text("Invalid email format");
        error++;
    } else {
        $("#email").removeClass("error");
        $("#emailError").text("");
    }

    
    const city = $("#city").val();
    if (isEmpty(city)) {
        $("#city").addClass("error");
        $("#cityError").text("City cannot be empty");
        error++;
    } else {
        $("#city").removeClass("error");
        $("#cityError").text("");
    }

    
    const address = $("#address").val();
    if (isEmpty(address)) {
        $("#address").addClass("error");
        $("#addressError").text("Address cannot be empty");
        error++;
    } else {
        $("#address").removeClass("error");
        $("#addressError").text("");
    }

    // Validate Zip Code (5-6 digit number)
    const zipcode = $("#zipcode").val();
    const zipcodePattern = /^\d{5,6}$/;
    if (isEmpty(zipcode)) {
        $("#zipcode").addClass("error");
        $("#zipcodeError").text("Zip Code cannot be empty");
        error++;
    } else if (!zipcodePattern.test(zipcode)) {
        $("#zipcode").addClass("error");
        $("#zipcodeError").text("Invalid zip code format");
        error++;
    } else {
        $("#zipcode").removeClass("error");
        $("#zipcodeError").text("");
    }

    // If errors, prevent form submission
    if (error > 0) {
        event.preventDefault();
    }
});
</script>
</body>
</html>
<?php


mysqli_close($con);
?>