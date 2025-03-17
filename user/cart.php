<?php
session_start();
require_once('../includes/connect.php');
require_once('functions/common_function.php');

require_once('../includes/rememberme_verify.php');

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
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="./css/cart.css">
    <script src="./js/inactivity.js" defer></script>

   
</head>

<body>
    
    <?php include("../includes/header.php"); ?>

    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffce00;">
    <ul class="navbar-nav me-auto">
        <?php
        
        if (!isset($_SESSION['id']) || (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 0)) {
            echo '
                <li class="nav-item ms-3">
                    <a class="nav-link" href="" style="color: black !important;">Guest</a>
                </li>
            ';
        }

        
        if (!isset($_SESSION['id'])) {
            echo '
                <li class="nav-item ms-3">
                    <a class="nav-link" href="login.php" style="color: black !important;">Login</a>
                </li>
            ';
        }

        
        if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
            echo '
                <li class="nav-item ms-3">
                    <a class="nav-link" href="profile.php" style="color: black !important;">Profile</a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link" href="logout.php" style="color: black !important;">Logout</a>
                </li>
            ';
        }
        ?>
    </ul>
</nav>


    <div class="bg-light">
        <h3 class="text-center">Jersey Store</h3>
        <p class="text-center">Welcome to the world of football jerseys</p>
    </div>

    <div style="margin: 14px 0; color: #ff5733; font-family: 'Arial Black', sans-serif; font-size: 1.5em;">
    <p class="text-center">BUY OVER $50, GET FREE SHIPPING</p>
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
                        echo "<tr><td colspan='6' class='text-center'>Your cart is empty.</td></tr>";
    echo "<script>document.querySelector('.cart-summary').classList.add('hidden');</script>";
                    } else {
                        while ($cart_row = mysqli_fetch_assoc($cart_result)) {
                            echo "<script>document.querySelector('.cart-summary').classList.remove('hidden');</script>";
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
                                <td><img src="../admin_manage/produkt_image/<?php echo htmlspecialchars($produkt_image); ?>" alt="<?php echo htmlspecialchars($produkt_name); ?>" width="100" height="100"></td>
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
                                    <a href="./controllers/remove.php?cart_id=<?php echo urlencode($cart_id); ?>" class="remove-btn">
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
                    <?php
        $shipping_fee = ($cart_total > 50) ? 0 : 10.00; // free shipping for totals over $50
        ?>
                    <h4>Shipping Fee: $<span id="shipping-fee"><?php echo number_format($shipping_fee, 2); ?></span></h4>
                    <h4>Total (with Shipping): $<span id="cart-total-with-shipping"><?php echo number_format($cart_total + 10, 2); ?></span></h4>
                </div>
                <a href="checkout.php" class="btn btn-checkout">Go to Checkout</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
    <?php
      include("../includes/footer.php")
    ?>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        
        updateButtonStates();
        updateCartTotalPrice();
        checkStockBeforeCheckout(false);

        //  event listeners to quantity buttons
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function () {
                const sizeId = this.getAttribute('data-size-id');
                const action = this.classList.contains('increase') ? 'increase' : 'decrease';
                updateQuantity(sizeId, action);
            });
        });

        //  event listener to the Checkout button
        document.querySelector('.btn-checkout').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent form submission
            checkStockBeforeCheckout(true);
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
            url: './controllers/update_cart.php',
            type: 'POST',
            data: { size_id: sizeId, quantity: quantity },
            success: function (response) {
                const updatedPrice = parseFloat(response);
                if (!isNaN(updatedPrice)) {
                    document.querySelector(`.price[data-size-id='${sizeId}']`).textContent = "$" + updatedPrice.toFixed(2);
                    updateCartTotalPrice();
                } else {
                    toastr.error('Error updating the cart. Please try again.');
                }
            },
            error: function () {
                toastr.error('Failed to update the cart.');
            }
        });
    }

    function updateCartTotalPrice() {
        let subtotal = 0;
        document.querySelectorAll('.price').forEach(priceElement => {
            const price = parseFloat(priceElement.textContent.replace('$', ''));
            subtotal += price;
        });

        const shippingFee = subtotal > 50 ? 0 : 10.00; 
        const total = subtotal + shippingFee;

        document.getElementById('cart-subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('cart-total-with-shipping').textContent = total.toFixed(2);
        document.getElementById('shipping-fee').textContent = shippingFee.toFixed(2);

        const cartSummary = document.querySelector('.cart-summary');
    if (subtotal === 0) {
        cartSummary.classList.add('hidden'); 
    } else {
        cartSummary.classList.remove('hidden'); 
    }
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


function checkStockBeforeCheckout(triggeredByUser = false) {
    const sizeIds = [];
    document.querySelectorAll('.quantity-input').forEach(inputField => {
        const sizeId = inputField.getAttribute('data-size-id');
        sizeIds.push(sizeId);
    });

    $.ajax({
        url: './controllers/check_stock.php',
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
                    
                    stockSpan.textContent = "No more stock left of this size";
                    stockSpan.style.color = "red";
                    inputField.disabled = true;
                    increaseButton.disabled = true;
                    decreaseButton.disabled = true;
                    outOfStock = true;
                } else if (quantity > stock) {
                    
                    stockSpan.textContent = `This item's stock decreased to ${stock}`;
                    stockSpan.style.color = "red";
                    inputField.value = stock; 
                    updateCartQuantity(sizeId, stock); 
                    stockReduced = true;
                } else {
                    
                    stockSpan.textContent = `(${stock} left)`;
                    stockSpan.style.color = "#888";
                }

                
                stockSpan.setAttribute('data-stock', stock);

                
                if (stock > 0) {
                    increaseButton.disabled = quantity >= stock;
                    decreaseButton.disabled = quantity <= 1;
                }
            });

            if (triggeredByUser) {
                if (outOfStock || stockReduced) {
                    toastr.error('Some items in your cart have limited stock. Please review your cart.');
                } else {
                    window.location.href = 'checkout.php';
                }
            }
        },
        error: function () {
            toastr.error('Failed to check stock. Please try again.');
        }
    });
}





</script>


</body>
<?php
mysqli_close($con);
?>
</html>
