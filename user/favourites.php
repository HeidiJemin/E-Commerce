<?php
session_start();
include_once('../includes/connect.php');
include_once('functions/common_function.php');

// Check if the user is logged in (either via session or remember token)
if (!isset($_SESSION['id'])) {
    // User is not logged in, check for remember token
    if (isset($_COOKIE['remember_token'])) {
        $rememberToken = $_COOKIE['remember_token'];
        // Query the database to find a matching token
        $query = "SELECT user_id, email, remember_token, verified, username, role_id FROM users WHERE remember_token = '$rememberToken'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            // Token matched, log the user in automatically
            $user = mysqli_fetch_assoc($result);
            // Set session variables
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['date_time'] = time();
            $_SESSION['name'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['verified'] = $user['verified'];

            // Check if the user has role_id = 1 (customer)
            if ($_SESSION['role_id'] != 1) {
                // Redirect to admin page if the role is not 1 (customer)
                header("Location: admin_manage/index.php");
                exit;
            }

        } else {
            // If token is not valid, redirect to login page
            header("Location: login.php");
            exit;
        }
    } else {
        // No session and no remember token, redirect to login page
        header("Location: login.php");
        exit;
    }
} else {
    // If the session is already set and role_id is not 1, redirect to admin page
    if ($_SESSION['role_id'] != 1) {
        header("Location: admin_manage/index.php");
        exit;
    }
}

// If the user has role_id = 1, proceed to show favourite products or any other functionality
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Favourite Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
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



<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="bg-light text-center py-3">
      <h3>Hidden Store</h3>
      <p>Welcomes</p>
    </div>

    <!-- Content -->
    <div class="container-fluid">
      <div class="row px-1 mb-3">
        <div class="col-md-10">
          <div class="row">
            <?php
            get_all_favourites();
            getproduktbyliga();
            getproduktbyekip();
            ?>
          </div>
        </div>

        <div class="col-md-2 p-2">
          <ul class="navbar-nav me-auto text-center" style="list-style-type: none; padding: 0; margin: 0;">
            <li class="nav-item bg-info" style="height: 50px;">
              <a class="nav-link text-light" href="" style="background-color: #ffce00; color: black; display: flex; justify-content: center; align-items: center; height: 100%;">
                <span style="font-size: 18px; font-weight: bold; color: black;">Ligat</span>
              </a>
            </li>
            <?php getliga(); ?>
          </ul>
          <ul class="navbar-nav me-auto text-center" style="list-style-type: none; padding: 0; margin: 0;">
            <li class="nav-item bg-info" style="height: 50px;">
              <a class="nav-link text-light" href="" style="background-color: #ffce00; color: black; display: flex; justify-content: center; align-items: center; height: 100%;">
                <span style="font-size: 18px; font-weight: bold; color: black;">Ekipet</span>
              </a>
            </li>
            <?php getekip(); ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <?php include("../includes/footer.php"); ?>
  </footer>

 


    <!-- bootstrap js link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"></script>

</body>

<script>
    document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("favourite-btn")) {
            const button = event.target;
            const produktId = button.getAttribute("data-produkt-id");

            fetch("favourites_handler.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    produkt_id: produktId,
                    action: "remove",
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the product card
                    const card = button.closest(".col-md-4");
                    card.remove();

                    // Check if there are remaining product cards in col-md-10
                    const productColumn = document.querySelector(".col-md-10 .row");
                    if (!productColumn.querySelector(".col-md-4")) {
                        productColumn.innerHTML = `
                            <div class='text-center' style='width: 100%; padding: 20px; min-height: 450px;'>
                                <h4>No favourite products yet.</h4>
                            </div>
                        `;
                    }
                } else {
                    alert(data.message || "An error occurred.");
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
});

</script>

</html>
<?php
// Close the database connection
mysqli_close($con);
?>