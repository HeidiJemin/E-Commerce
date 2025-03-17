<?php
session_start();
require_once('../includes/connect.php');

?>
<?php
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
    <link href="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/produkt_info.css">
    <script src="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.js"></script>
<script src="./js/inactivity.js" defer></script>


    <?php
    require_once('functions/common_function.php');
    ?>

<style>
   * {
      margin: 0;
      padding: 0;
      font-family: "Istok Web", sans-serif;
    }
    
</style>
  




</head>

<body>
  <!-- navbar -->
  <?php
      include("../includes/header.php")
    ?>

  
  
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

    <div class="row px-1">
      <div class="col-md-10">
        <div class="row">
            

        <?php
             view_more();
             getproduktbyliga();
             getproduktbyekip();
             
         ?>
         
        </div>
      </div>

      <div class="col-md-2  p-0 ">
  <ul class="navbar-nav me-auto text-center" style="list-style-type: none; padding: 0; margin: 0;">
    <li class="nav-item bg-info" style="height: 50px;">
      <a class="nav-link text-light" href="#" style="background-color: #ffce00; color: black; display: flex; justify-content: center; align-items: center; height: 100%; padding-left: 0; padding-right: 0;">
        <span style="font-size: 18px; font-weight: bold;color: black;">Ligat</span>
      </a>
    </li>
    <?php
        getliga();
    ?>
  </ul>
  <ul class="navbar-nav me-auto text-center" style="list-style-type: none; padding: 0; margin: 0;">
    <li class="nav-item bg-info" style="height: 50px;">
      <a class="nav-link text-light" href="#" style="background-color: #ffce00; color: black; display: flex; justify-content: center; align-items: center; height: 100%; padding-left: 0; padding-right: 0;">
        <span style="font-size: 18px; font-weight: bold;color: black;">Ekipet</span>
      </a>
    </li>
    <?php
      getekip();
    ?>
  </ul>
</div>
    </div>
    </div>




    <!-- footer -->
     <footer>
    <?php
      include("../includes/footer.php")
    ?>
    </footer>
    
    <!-- bootstrap js link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"></script>

</body>

</html>
<?php

mysqli_close($con);
?>