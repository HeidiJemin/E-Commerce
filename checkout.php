<?php
    session_start(); // Ensure session is started
    include('./includes/connect.php');
    include('functions/common_function.php');

    // Check connection
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
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
  
  
  <style>
    /* Optional: Style for error highlighting */
    .error {
      border-color: #dc3545;
    }
    /* Ensure that the body takes the full height */
html, body {
  height: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
}

/* Main content section */
.content {
  flex-grow: 1; /* Take the remaining space between the header and footer */
  padding-bottom: 60px; /* Adjust the bottom padding to avoid content hidden behind the footer */
}
.logo {
  width: 7%;
  height: 7%;
}
.navbar .nav-link {
  color: white !important; /* White text for navbar links */
}

.navbar .nav-link:hover {
  color: #ffce00 !important; /* Hover color for navbar links */
}

.navbar .nav-link.active {
  color: #ffce00 !important; /* Active link color */
}

/* Toggler icon color */
.navbar-toggler-icon {
  background-color: white; /* White icon color */
}
/* Footer Styling */
footer {
  background-color: black;
  color: white;
  font-family: 'Roboto', sans-serif;
  text-align: center;
  padding: 15px;
}

  </style>
</head>
<body>
<?php include("./includes/header.php"); ?>
<div class="content">
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
          SELECT c.cart_item_id, c.quantity, p.produkt_name, p.produkt_price, p.produkt_image1, s.size 
          FROM `cart` c
          JOIN `produkt` p ON c.produkt_id = p.produkt_id
          JOIN `sizes` s ON c.size_id = s.size_id
          WHERE c.user_id = '$user_id'
        ";
        $cart_items_result = mysqli_query($con, $cart_query);
        $subtotal = 0;
        $shipping_cost = 10.00; // Flat shipping rate

        if (mysqli_num_rows($cart_items_result) > 0) { ?>
          <form id="checkout_form" method="POST" novalidate>
            <div class="row">
              <!-- Address Section -->
              <div class="col-md-7">
                <h3>Shipping Address</h3>
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
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email">
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
        <?php while ($item = mysqli_fetch_assoc($cart_items_result)) {
            $total_price = $item['produkt_price'] * $item['quantity'];
            $subtotal += $total_price; ?>
            <li class="list-group-item d-flex align-items-center cart-item" data-produkt-id="<?= $item['cart_item_id']; ?>"
                data-produkt-name="<?= htmlspecialchars($item['produkt_name']); ?>"
                data-size="<?= htmlspecialchars($item['size']); ?>"
                data-quantity="<?= $item['quantity']; ?>"
                data-price="<?= $item['produkt_price']; ?>"
                data-total-price="<?= $total_price; ?>"
            >
                <img src="<?= 'admin_manage/produkt_image/' . htmlspecialchars($item['produkt_image1']); ?>"
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
        <span>$<?= number_format($shipping_cost, 2); ?></span>
    </div>
    <hr>
    <div class="d-flex justify-content-between">
        <span>Total Price:</span>
        <span class="fw-bold">$<?= number_format($subtotal + $shipping_cost, 2); ?></span>
    </div>
    <hr>
    <button type="submit" class="btn btn-success btn-lg w-100">Submit and Pay with PayPal</button>
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


<?php include("./includes/footer.php"); ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ENjdO4Dr2bkBIFxQpeoFzQGRo4sV/18sb1sV5tQcKxXgF1Yh/tJw3oXZz5A7Bz4T" crossorigin="anonymous"></script>
<script>
    document.querySelectorAll("input[type='text'], textarea").forEach((input) => {
  input.addEventListener("input", (event) => {
    const words = event.target.value.split(" ");
    event.target.value = words
      .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(" ");
  });
});
    $('#checkout_form').on("submit", function (event) {
        event.preventDefault();

        let error = 0;

        const isEmpty = (value) => value.trim() === "";

        // Validate First Name
        const firstname = $("#firstname").val();
        if (isEmpty(firstname)) {
            $("#firstname").addClass("error");
            $("#firstnameError").text("First Name cannot be empty");
            error++;
        } else {
            $("#firstname").removeClass("error");
            $("#firstnameError").text("");
        }

        // Validate Last Name
        const lastname = $("#lastname").val();
        if (isEmpty(lastname)) {
            $("#lastname").addClass("error");
            $("#lastnameError").text("Last Name cannot be empty");
            error++;
        } else {
            $("#lastname").removeClass("error");
            $("#lastnameError").text("");
        }

        // Validate Country
        const country = $("#country").val();
        if (isEmpty(country)) {
            $("#country").addClass("error");
            $("#countryError").text("Country cannot be empty");
            error++;
        } else {
            $("#country").removeClass("error");
            $("#countryError").text("");
        }

        // Validate Phone
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

        // Validate Email
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

        // Validate City
        const city = $("#city").val();
        if (isEmpty(city)) {
            $("#city").addClass("error");
            $("#cityError").text("City cannot be empty");
            error++;
        } else {
            $("#city").removeClass("error");
            $("#cityError").text("");
        }

        // Validate Address
        const address = $("#address").val();
        if (isEmpty(address)) {
            $("#address").addClass("error");
            $("#addressError").text("Address cannot be empty");
            error++;
        } else {
            $("#address").removeClass("error");
            $("#addressError").text("");
        }

        // Validate Zip Code
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

        if (error === 0) {
                // Collect form data directly from form fields
                const cartItems = [];
$(".cart-item").each(function () {
    const produkt_id = $(this).data('produkt-id');
    const item_name = $(this).data('produkt-name');
    const size = $(this).data('size');
    const quantity = $(this).data('quantity');
    const price = parseFloat($(this).data('price'));
    const total_price = parseFloat($(this).data('total-price'));
    
    cartItems.push({
        produkt_id: produkt_id,
        item_name: item_name,
        quantity: quantity,
        size: size,
        price: price,
        total_price: total_price
    });
});

                // Send AJAX request
                $.ajax({
    url: "save_order_payment.php",  // URL to backend PHP file
    type: "POST",  // HTTP method
    data: {
        user_id: <?= json_encode($user_id); ?>,  // PHP variable for user_id
        total_price: <?= json_encode($subtotal + $shipping_cost); ?>,  // Calculated total price
        payment_id: "TEST1237",  // Mock payment details (replace with actual data)
        payment_status: "COMPLETED",  // Mock payment status (replace with actual data)
        firstname: $("#firstname").val(),  // Shipping details from form fields
        lastname: $("#lastname").val(),
        country: $("#country").val(),
        phone: $("#phone").val(),
        email: $("#email").val(),
        city: $("#city").val(),
        address: $("#address").val(),
        zipcode: $("#zipcode").val(),
        cart_items: JSON.stringify(cartItems)  // Convert cart items array to JSON string
    },
    success: function (response) {
        // Success handling: Show confirmation and redirect
        alert("Order and payment successfully saved in the database!");
        window.location.href = "order_success.php?order_id=" + response.order_id;
    },
    error: function (xhr, status, error) {
        // Error handling: Log the error and show an alert
        console.error("Error: ", error);
        alert("An error occurred while saving your order. Please check the console for details.");
    }
});
            }
        });
</script>
</body>
</html>
