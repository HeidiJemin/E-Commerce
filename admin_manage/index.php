<?php
session_start();
require_once('../includes/connect.php');


if (isset($_SESSION['id'])) {
   
    if ((int)$_SESSION['role_id'] === 0) {
        
        
    } else {
      
        header("Location: ./unauthorized.php");
        exit;
    }
} else {
    
    if (isset($_COOKIE['remember_token'])) {
        $rememberToken = $_COOKIE['remember_token'];

        $query = "SELECT user_id, email, remember_token, verified, username, role_id FROM users WHERE remember_token = '$rememberToken'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) == 1) {
           
            $user = mysqli_fetch_assoc($result);

            
            if ((int)$user['role_id'] === 0) {
                
                $_SESSION['id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['date_time'] = time();
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['verified'] = $user['verified'];

                
            } else {
                
                header("Location: ./unauthorized.php");
                exit;
            }
        } else {
            
            header("Location: ../user/login.php");
            exit;
        }
    } else {
        
        header("Location: ../user/login.php");
        exit;
    }
}
$username = $_SESSION['username'];

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- CSS and JS links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

 <!-- JS scripts -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <style>
        * {
  margin: 0;
  padding: 0;
  font-family: "Istok Web", sans-serif;
}

html,
body {
  
  width: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
  overflow-x: hidden;
}
        .container-fluid {
            flex: 1;
        }
        
        
        .produkt_img {
            width: 90px;
            object-fit: contain;
        }
        #productTable {
            border-collapse: collapse;
            width: 100%;
        }
        #productTable th, #productTable td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        #productTable thead {
            background-color: #17a2b8;
            color: white;
        }
        #productTable tbody tr:hover {
            background-color: #f1f1f1;
        }
        .custom-btn {
            background-color: #ffce00;
            color: black;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            display: inline-block;
        }
        .custom-btn:hover {
            background-color: black;
            color: white;
        }
        .custom-btn a {
            color: inherit;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
    <nav class="navbar navbar-expand-lg" style="background-color: #000; color: #fff;">
    <div class="container-fluid">
        <img src="./produkt_image/logo.png" class="logo" alt="Logo">
        <nav class="navbar navbar-expand-lg">
            <ul class="navbar-nav">
                <!-- Home Button -->
                <li class="nav-item">
                    <a href="./index.php" class="nav-link" style="color: #fff;">
                        Home
                    </a>
                </li>
                <!-- Welcome Message -->
                <li class="nav-item">
                    <a href="" class="nav-link" style="color: #fff;">
                        Welcome <?php echo htmlspecialchars($username); ?>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</nav>


        <div class="bg-light">
            <h3 class="text-center">Manage Everything</h3>
        </div>

        <div class="row">
    <div class="col-md-12 p-1 d-flex justify-content-center">
        <div class="button text-center">
            <a href="shto_produkt.php" class="custom-btn nav-link">
                SHTO PRODUKT
            </a>
            <a href="index.php?view_products" class="custom-btn nav-link">
                SHIKO PRODUKTET
            </a>
            <a href="index.php?shto_liga" class="custom-btn nav-link">
                SHTO LIGA
            </a>
            <a href="index.php?shiko_liga" class="custom-btn nav-link">
                SHIKO LIGA
            </a>
            <a href="index.php?shto_ekipe" class="custom-btn nav-link">
                SHTO EKIPE
            </a>
            <a href="index.php?shiko_ekip" class="custom-btn nav-link">
                SHIKO EKIPE
            </a>
            <a href="index.php?shiko_porosi" class="custom-btn nav-link">
                POROSITE
            </a>
            <a href="index.php?shiko_pagesa" class="custom-btn nav-link">
                PAGESAT
            </a>
            <a href="index.php?shiko_user" class="custom-btn nav-link">
                PERDORUESIT
            </a>
            <a href="../user/logout.php" class="custom-btn nav-link">
                LOGOUT
            </a>
        </div>
    </div>
</div>


        <div class="container my-5">
            <?php
            if (isset($_GET['shto_liga'])) {
                include_once('shto_liga.php');
            }
            if (isset($_GET['shto_ekipe'])) {
                include_once('shto_ekipe.php');
            }
            if (isset($_GET['view_products'])) {
                include_once('view_products.php');
            }
            if (isset($_GET['edit_produkt'])) {
                include_once('edit_produkt.php');
            }
            if (isset($_GET['shiko_liga'])) {
                include_once('shiko_liga.php');
            }
            if (isset($_GET['shiko_ekip'])) {
                include_once('shiko_ekip.php');
            }
            if (isset($_GET['edit_liga'])) {
                include_once('edit_liga.php');
            }
            if (isset($_GET['edit_ekip'])) {
                include_once('edit_ekip.php');
            }
            if (isset($_GET['shiko_user'])) {
                include_once('shiko_user.php');
            }
            if (isset($_GET['delete_ekip'])) {
                include_once('delete_ekip.php');
            }
            if (isset($_GET['shiko_porosi'])) {
                include_once('shiko_porosi.php');
            }
            if (isset($_GET['shiko_pagesa'])) {
                include_once('shiko_pagesa.php');
            }
            ?>
        </div>
    </div>

</body>

    <script>
        
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    function previewImage(imageNumber) {
    var fileInput = document.getElementById('produkt_image' + imageNumber);
    var file = fileInput.files[0];
    var preview = document.getElementById('preview_image' + imageNumber);

    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result; 
        };
        reader.readAsDataURL(file);
    } else {
        
        preview.src = './produkt_image/<?php echo $product_image1; ?>';
    }
}
</script>

    
</html>
