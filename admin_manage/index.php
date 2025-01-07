<?php
// Start the session and include necessary files
session_start();
ob_start();
include('../includes/connect.php');
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- CSS and JS links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
 <!-- JS scripts -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
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
        <img src="../images/logo.png" class="logo" alt="Logo">
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
            <a href="#" class="custom-btn nav-link">
                PAGESAT
            </a>
            <a href="index.php?shiko_user" class="custom-btn nav-link">
                PERDORUESIT
            </a>
            <a href="../logout.php" class="custom-btn nav-link">
                LOGOUT
            </a>
        </div>
    </div>
</div>


        <div class="container my-5">
            <?php
            if (isset($_GET['shto_liga'])) {
                include('shto_liga.php');
            }
            if (isset($_GET['shto_ekipe'])) {
                include('shto_ekipe.php');
            }
            if (isset($_GET['view_products'])) {
                include('view_products.php');
            }
            if (isset($_GET['edit_produkt'])) {
                include('edit_produkt.php');
            }
            if (isset($_GET['delete_produkt'])) {
                include('delete_produkt.php');
            }
            if (isset($_GET['shiko_liga'])) {
                include('shiko_liga.php');
            }
            if (isset($_GET['shiko_ekip'])) {
                include('shiko_ekip.php');
            }
            if (isset($_GET['edit_liga'])) {
                include('edit_liga.php');
            }
            if (isset($_GET['edit_ekip'])) {
                include('edit_ekip.php');
            }
            if (isset($_GET['shiko_user'])) {
                include('shiko_user.php');
            }
            if (isset($_GET['delete_ekip'])) {
                include('delete_ekip.php');
            }
            if (isset($_GET['shiko_porosi'])) {
                include('shiko_porosi.php');
            }
            ?>
        </div>
    </div>

    

   

    <script>
        $(document).ready(function () {
            $('#productTable').DataTable();
        });

        function filterTeamsByLiga() {
            const ligaId = document.getElementById('produkt_liga').value;
            const data = new FormData();
            data.append("liga_id", ligaId);

            $.ajax({
                type: "POST",
                url: "fetch_teams.php",
                async: false,
                cache: false,
                processData: false,
                data: data,
                contentType: false,
                success: function (response) {
                    const ekipSelect = document.getElementById('produkt_ekip');
                    ekipSelect.innerHTML = `<option value="" disabled selected>Zgjidh nje ekip</option>` + response;
                },
                error: function () {
                    console.error("An error occurred while fetching teams.");
                }
            });
        }
    </script>
</body>
</html>
