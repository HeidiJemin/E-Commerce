<nav class="navbar navbar-expand-lg bg-black custom-absolute-top">
  <div class="container-fluid">
    <img src="./images/logo.png" alt="" class="logo">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="display_all.php">Products</a>
        </li>
        <?php
        // Check if the user is logged in before displaying the cart link
        if (!isset($_SESSION['id'])) {
          echo '<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
        }
        ?>
        <li class="nav-item">
          <a class="nav-link" href="#">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./favourites.php">Favourites</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="my_orders.php">My Orders</a>
        </li>
        <li class="nav-item">
          <?php
          // Check if the user is logged in before displaying the cart link
          if (isset($_SESSION['id'])) {
              // User is logged in, allow access to cart.php
              echo '<a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping"><sup>' . getCartProductNumber() . '</sup></i></a>';
          } else {
              // User is not logged in, redirect to login.php
              echo '<a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping"><sup>' . getCartProductNumber() . '</sup></i></a>';
          }
          ?>
        </li>
      
        <?php
        if(isset($_SESSION['id'])){
          echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
        }
        ?>
      </ul>
      <form class="d-flex" role="search" action="search_produkt.php" method="get">
        <input class="form-control me-2" type="search" name="search_produkt" placeholder="Search" aria-label="Search">
        <input type="submit" value="Search" name="search_produkt_data" class="btn btn-outline-light">
      </form>
    </div>
  </div>
</nav>
