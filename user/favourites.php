<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../includes/connect.php');
require_once('functions/common_function.php');
require_once('../includes/rememberme.php');
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Commerce Website </title>
  <!-- bootstrap CSS link -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- font awesome link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">

  <!-- css file -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/produkt_style.css">
  <script src="./js/inactivity.js" defer></script>

</head>

<body>
  <div class="main">
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


    
<div class="bg-light text-center py-3 mb-3">
<h3 class="text-center">Jersey Store</h3>
<p class="text-center">Welcome to the world of football jerseys</p>
    </div>

    <div class="row px-1 mb-3 flex-grow-1"> 
      <div class="col-md-10">
        <div class="row">
          <?php get_all_favourites(); getproduktbyliga(); getproduktbyekip(); ?>
        </div>
      </div>

      <div class="col-md-2 p-0">
        <ul class="navbar-nav me-auto text-center">
          <li class="nav-item bg-info">
            <a class="nav-link text-light" href="#" style="background-color: #ffce00; color: black;">
              <span style="font-size: 18px; font-weight: bold; color: black;">Ligat</span>
            </a>
          </li>
          <?php getliga(); ?>
        </ul>

        <ul class="navbar-nav me-auto text-center">
          <li class="nav-item bg-info">
            <a class="nav-link text-light" href="#" style="background-color: #ffce00; color: black;">
              <span style="font-size: 18px; font-weight: bold; color: black;">Ekipet</span>
            </a>
          </li>
          <?php getekip(); ?>
        </ul>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <?php include("../includes/footer.php"); ?>
    </footer>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script src="./js/favourite.js"></script>

</html>
<?php
mysqli_close($con);
?>