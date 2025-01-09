<?php
session_start();
include('includes/connect.php');
include('functions/common_function.php');


if (isset($_SESSION['id'])) {
        
} else {
  // Check if the 'remember_token' cookie is set
  if (isset($_COOKIE['remember_token'])) {
      $rememberToken = $_COOKIE['remember_token'];

      // Query the database to find a matching token
      $query = "SELECT user_id, email, remember_token,verified,name, role_name,role_id FROM users WHERE remember_token = '$rememberToken'"; /// shto kete 
      $result = mysqli_query($con, $query);

      if ($result && mysqli_num_rows($result) == 1) {
          // Token matched, log the user in automatically
          $user = mysqli_fetch_assoc($result);

          // Set session variables
          $_SESSION['id'] = $user['user_id'];
          $_SESSION['email'] = $user['email'];
          $_SESSION['date_time'] = time();
          $_SESSION['name'] = $user['username'];
          $_SESSION['role_name'] = $user['role_name'];
          $_SESSION['role_id'] = $user['role_id'];
          $_SESSION['verified'] = $user['verified']; //// shto kete 

          // Redirect to the index page (or any other page you prefer)

          if($_SESSION['role_id']==="1"){
            header("Location: index.php"); // Or the current page
            exit;
          }
          else{
            header("Location: admin_manage/index.php"); // Or the current page
            exit;
          }
      }
  }
}


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
  <!-- css file -->
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="produkt_style.css">
  
 <style>
  .favourite-btn {
    margin-top: 15px;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    color: white;
    background-color: grey;
    transition: background-color 0.3s ease;
}

.favourite-btn.favourited {
    background-color: red;
}

 </style>

</head>

<body>
  <!-- navbar -->
  <div class="container-fluid p-0 ">
  <?php
      include("./includes/header.php")
    ?>
      

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
      <p class="text-center">Welcomes</p>
    </div>

    <div class="row px-1 mb-3">
      <div class="col-md-10">
        <div class="row">
         <?php
             getprodukt();
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
    






    <!-- footer -->
     <footer>
    <?php
      include("./includes/footer.php")
    ?>
    </footer>
    </div>
    

     





    <!-- bootstrap js link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"></script>

</body>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const favButtons = document.querySelectorAll(".favourite-btn");

        favButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const produktId = button.getAttribute("data-produkt-id");

                // Determine action: Add or Remove
                const action = button.classList.contains("favourited") ? "remove" : "add";

                // AJAX request
                fetch("favourites_handler.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        produkt_id: produktId,
                        action: action,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            // Toggle button class and text
                            button.classList.toggle("favourited");
                            if (button.classList.contains("favourited")) {
                                button.textContent = "Remove from Favourites";
                            } else {
                                button.textContent = "Add to Favourites";
                            }
                        } else {
                            alert(data.message || "An error occurred.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
            });
        });
    });
</script>

</html>