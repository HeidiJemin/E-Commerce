<nav class="navbar navbar-expand-lg bg-black custom-absolute-top">
  <div class="container-fluid">
    <img src="../admin_manage/produkt_image/logo.png" alt="" class="logo">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'display_all.php') ? 'active' : ''; ?>" href="display_all.php">Products</a>
        </li>
        <?php
        if (!isset($_SESSION['id'])) {
          echo '<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
        }
        ?>
        
        <li class="nav-item">
          <?php
          if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
              echo '<a class="nav-link ' . (basename($_SERVER['PHP_SELF']) == 'favourites.php' ? 'active' : '') . '" href="./favourites.php">Favourites</a>';
          }
          ?>
        </li>
        <li class="nav-item">
          <?php
          if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
              echo '<a class="nav-link ' . (basename($_SERVER['PHP_SELF']) == 'my_orders.php' ? 'active' : '') . '" href="my_orders.php">My Orders</a>';
          }
          ?>
        </li>
        <li class="nav-item">
          <?php
          if (isset($_SESSION['id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
              echo '<a class="nav-link" href="cart.php"><i class="fa-solid fa-cart-shopping"><sup id="cart-count">' . getCartProductNumber() . '</sup></i></a>';
          } 
          ?>
        </li>
      </ul>
      <?php
      $search_produkt_val = isset($_GET['search_produkt']) ? htmlspecialchars($_GET['search_produkt']) : '';
      ?>
      <form class="d-flex" role="search" action="search_produkt.php" method="get">
        <input class="form-control me-2" type="search" name="search_produkt" placeholder="Search" value="<?php echo $search_produkt_val; ?>" aria-label="Search">
        <input type="submit" value="Search" name="search_produkt_data" class="btn btn-outline-light">
      </form>
    </div>
  </div>
</nav>
