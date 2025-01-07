<?php
include('includes/connect.php');
include('functions/common_function.php');

session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Redirect if user's email is not verified
if (isset($_SESSION['id']) && $_SESSION['verified'] != '1') {
    header("Location: verify.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Website - Cart</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom CSS -->
    
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        

        .container {
            flex: 1;
        }

        .btn-checkout {
            background-color: #ffce00;
            color: black;
            font-weight: bold;
            border: none;
        }

        .btn-checkout:hover {
            background-color: black;
            color: white;
        }

        footer {
            margin-top: auto;
        }

        .total-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .stock-info {
            font-size: 0.9em;
            color: #888;
            display: block;
        }

        .cart-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 10px 0;
            border-top: 2px solid #ccc;
        }

        .cart-summary h4 {
            margin: 5px 0;
        }

        .cart-summary .btn-checkout {
            background-color: #ffce00;
            color: black;
            font-weight: bold;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
        }

        .cart-summary .btn-checkout:hover {
            background-color: black;
            color: white;
        }
        .navbar.bg-black {
  background-color: black !important;
  border: none;
}
.logo {
  width: 7%;
  height: 7%;
}

/* Navbar link styling */
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

    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include("./includes/header.php"); ?>

    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffce00;">
  <ul class="navbar-nav me-auto">
    <?php
      if(!isset($_SESSION['id'])){
        echo '
          <li class="nav-item ms-3">
            <a class="nav-link" href="#" style="color: black !important;">Guest</a>
          </li>
          <li class="nav-item ms-3">
            <a class="nav-link" href="login.php" style="color: black !important;">Login</a>
          </li>
        ';
      }else{
        echo '
          <li class="nav-item ms-3">
            <a class="nav-link" href="logout.php" style="color: black !important;">Logout</a>
          </li>
          <li class="nav-item ms-3">
            <a class="nav-link" href="profile.php" style="color: black !important;">Profile</a>
          </li>
        ';
      }
    ?>
  </ul>
</nav>

    <div class="bg-light">
        <h3 class="text-center">Hidden Store</h3>
        <p class="text-center">Welcome to the world of football jerseys</p>
    </div>

    <!-- Cart Table -->
    <div class="container my-5">
        <div class="row">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_id = $_SESSION['id'];
                    $cart_total = 0;

                    // Fetch cart details with size stock
                    $cart_query = "
                        SELECT c.*, p.produkt_name, p.produkt_image1, p.produkt_price, s.stock, s.size 
                        FROM `cart` c 
                        JOIN `produkt` p ON c.produkt_id = p.produkt_id 
                        JOIN `sizes` s ON c.size_id = s.size_id 
                        WHERE c.user_id = '$user_id'
                    ";
                    $cart_result = mysqli_query($con, $cart_query);

                    if (mysqli_num_rows($cart_result) === 0) {
                        echo "<tr><td colspan='6'>Your cart is empty.</td></tr>";
                    } else {
                        while ($cart_row = mysqli_fetch_assoc($cart_result)) {
                            $size_id = $cart_row['size_id'];
                            $quantity = $cart_row['quantity'];
                            $produkt_name = $cart_row['produkt_name'];
                            $produkt_image = $cart_row['produkt_image1'];
                            $produkt_price = $cart_row['produkt_price'];
                            $stock = $cart_row['stock'];
                            $size = $cart_row['size'];
                            $total_price = $produkt_price * $quantity;
                            $cart_total += $total_price;
                            $cart_id = $cart_row['cart_item_id'];
                    ?>
                            <tr>
                                <td><img src="admin_manage/produkt_image/<?php echo htmlspecialchars($produkt_image); ?>" alt="<?php echo htmlspecialchars($produkt_name); ?>" width="100" height="100"></td>
                                <td><?php echo htmlspecialchars($produkt_name); ?></td>
                                <td><?php echo htmlspecialchars($size); ?></td>
                                <td>
                                    <div class="quantity-box">
                                        <button class="quantity-btn decrease" data-size-id="<?php echo $size_id; ?>">-</button>
                                        <input type="number" value="<?php echo $quantity; ?>" min="1" class="quantity-input" data-size-id="<?php echo $size_id; ?>" readonly>
                                        <button class="quantity-btn increase" data-size-id="<?php echo $size_id; ?>">+</button>
                                    </div>
                                    <span class="stock-info" data-size-id="<?php echo $size_id; ?>" data-stock="<?php echo $stock; ?>">(<?php echo $stock; ?> left)</span>
                                </td>
                                <td><span class="price" data-size-id="<?php echo $size_id; ?>">$<?php echo number_format($total_price, 2); ?></span></td>
                                <td>
                                    <a href="remove.php?cart_id=<?php echo urlencode($cart_id); ?>" class="remove-btn">
                                        <button class="btn btn-danger">Remove</button>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <!-- Total Price and Checkout -->
            <div class="cart-summary">
                <div>
                    <h4>Subtotal: $<span id="cart-subtotal"><?php echo number_format($cart_total, 2); ?></span></h4>
                    <h4>Shipping Fee: $<span id="shipping-fee">10.00</span></h4>
                    <h4>Total (with Shipping): $<span id="cart-total-with-shipping"><?php echo number_format($cart_total + 10, 2); ?></span></h4>
                </div>
                <a href="checkout.php" class="btn btn-checkout">Go to Checkout</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Hidden Store. All Rights Reserved.</p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize button states and cart price calculations on page load
        updateButtonStates();
        updateCartTotalPrice();

        // Add event listeners to quantity buttons
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function () {
                const sizeId = this.getAttribute('data-size-id');
                const action = this.classList.contains('increase') ? 'increase' : 'decrease';
                updateQuantity(sizeId, action);
            });
        });

        // Add event listener to the Checkout button
        document.querySelector('.btn-checkout').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent form submission
            checkStockBeforeCheckout();
        });
    });

    function updateQuantity(sizeId, action) {
        const inputField = document.querySelector(`.quantity-input[data-size-id='${sizeId}']`);
        const increaseButton = document.querySelector(`.quantity-btn.increase[data-size-id='${sizeId}']`);
        const decreaseButton = document.querySelector(`.quantity-btn.decrease[data-size-id='${sizeId}']`);
        const stockSpan = document.querySelector(`.stock-info[data-size-id='${sizeId}']`);
        const maxStock = parseInt(stockSpan.getAttribute('data-stock'));
        let quantity = parseInt(inputField.value);

        if (action === 'increase' && quantity < maxStock) {
            quantity++;
        } else if (action === 'decrease' && quantity > 1) {
            quantity--;
        }

        inputField.value = quantity;
        increaseButton.disabled = quantity >= maxStock;
        decreaseButton.disabled = quantity <= 1;

        // Update price and cart total
        updateCartQuantity(sizeId, quantity);
    }

    function updateCartQuantity(sizeId, quantity) {
        $.ajax({
            url: 'update_cart.php',
            type: 'POST',
            data: { size_id: sizeId, quantity: quantity },
            success: function (response) {
                const updatedPrice = parseFloat(response);
                if (!isNaN(updatedPrice)) {
                    document.querySelector(`.price[data-size-id='${sizeId}']`).textContent = "$" + updatedPrice.toFixed(2);
                    updateCartTotalPrice();
                } else {
                    alert('Error updating the cart. Please try again.');
                }
            },
            error: function () {
                alert('Failed to update the cart.');
            }
        });
    }

    function updateCartTotalPrice() {
        let subtotal = 0;
        document.querySelectorAll('.price').forEach(priceElement => {
            const price = parseFloat(priceElement.textContent.replace('$', ''));
            subtotal += price;
        });

        const shippingFee = 10.00; // Static shipping fee
        const total = subtotal + shippingFee;

        document.getElementById('cart-subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('cart-total-with-shipping').textContent = total.toFixed(2);
    }

    function updateButtonStates() {
        document.querySelectorAll('.quantity-input').forEach(inputField => {
            const sizeId = inputField.getAttribute('data-size-id');
            const increaseButton = document.querySelector(`.quantity-btn.increase[data-size-id='${sizeId}']`);
            const decreaseButton = document.querySelector(`.quantity-btn.decrease[data-size-id='${sizeId}']`);
            const stockSpan = document.querySelector(`.stock-info[data-size-id='${sizeId}']`);
            const maxStock = parseInt(stockSpan.getAttribute('data-stock'));
            const quantity = parseInt(inputField.value);

            increaseButton.disabled = quantity >= maxStock;
            decreaseButton.disabled = quantity <= 1;
        });
    }

    function checkStockBeforeCheckout() {
    const sizeIds = [];
    document.querySelectorAll('.quantity-input').forEach(inputField => {
        const sizeId = inputField.getAttribute('data-size-id');
        sizeIds.push(sizeId);
    });

    $.ajax({
        url: 'check_stock.php',
        type: 'POST',
        data: { size_ids: sizeIds },
        success: function (response) {
            const stockData = JSON.parse(response);
            let outOfStock = false;
            let stockReduced = false;

            stockData.forEach(item => {
                const sizeId = item.size_id;
                const stock = item.stock;

                const stockSpan = document.querySelector(`.stock-info[data-size-id='${sizeId}']`);
                const inputField = document.querySelector(`.quantity-input[data-size-id='${sizeId}']`);
                const increaseButton = document.querySelector(`.quantity-btn.increase[data-size-id='${sizeId}']`);
                const decreaseButton = document.querySelector(`.quantity-btn.decrease[data-size-id='${sizeId}']`);
                const quantity = parseInt(inputField.value);

                if (stock === 0) {
                    // Handle out-of-stock items
                    stockSpan.textContent = "No more stock left of this size";
                    stockSpan.style.color = "red";
                    inputField.value = 0;
                    inputField.disabled = true;
                    increaseButton.disabled = true;
                    decreaseButton.disabled = true;
                    updateCartQuantity(sizeId, 0); // Update the cart with quantity 0
                    outOfStock = true;
                } else if (quantity > stock) {
                    // Handle decreased stock
                    stockSpan.textContent = `This item's stock decreased to ${stock}`;
                    stockSpan.style.color = "red";
                    inputField.value = stock;
                    updateCartQuantity(sizeId, stock); // Update the cart with new stock value
                    stockReduced = true;
                } else {
                    // Stock is sufficient
                    stockSpan.textContent = `(${stock} left)`;
                    stockSpan.style.color = "#888";
                }

                // Update the data-stock attribute dynamically
                stockSpan.setAttribute('data-stock', stock);

                // Update button states based on the new stock and quantity
                increaseButton.disabled = parseInt(inputField.value) >= stock;
                decreaseButton.disabled = parseInt(inputField.value) <= 1;
            });

            if (outOfStock || stockReduced) {
                alert('Some items in your cart have limited stock. Please review your cart.');
            } else {
                window.location.href = 'checkout.php';
            }
        },
        error: function () {
            alert('Failed to check stock. Please try again.');
        }
    });
}





</script>


</body>

</html>
