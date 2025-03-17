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
$userId = $_SESSION['id'];
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shto Produkte - Admin</title>
    <link rel="stylesheet" href="./css/admin_style.css">
    <!-- bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- font awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous">
    <!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

<style>
    * {
  margin: 0;
  padding: 0;
  font-family: "Istok Web", sans-serif;
}
    html,
body {
  min-height: 100%;
  width: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
  overflow-x: hidden;
}
.container-fluid {
    margin-top: 0;
    padding-top: 0;
}

</style>


</head>

<body class="bg-light m-0 p-0">
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

        <div class="container mt-4">
            <h1 class="text-center mb-4">Shto Produkt</h1>
            <form id="produkt_form" action="" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- Left column -->
        <div class="col-md-6">
            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_name">Emri i Produktit</label>
                <input type="text" required class="form-control" name="produkt_name" id="produkt_name" placeholder="Shkruaj emrin e produktit">
            </div>

            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_description">Pershkrimi i Produktit</label>
                <textarea required class="form-control" name="produkt_description" id="produkt_description" rows="3" placeholder="Shkruaj pershkrimin"></textarea>
            </div>

            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_keywords">Keywords per Produktin</label>
                <input type="text" required class="form-control" name="produkt_keywords" id="produkt_keywords" placeholder="Shkruaj keywords">
            </div>

            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_price">Cmimi i Produktit ($)</label>
                <input type="number" step="0.01" required class="form-control" name="produkt_price" id="produkt_price" placeholder="Shkruaj cmimin">
            </div>

            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_liga">Liga</label>
                <select class="form-select" name="produkt_liga" id="produkt_liga">
                    <option value="">Zgjidh nje lige</option>
                    <?php
                    $select_query = "SELECT * FROM `liga`";
                    $result_query = mysqli_query($con, $select_query);
                    while ($row = mysqli_fetch_assoc($result_query)) {
                        echo "<option value='{$row['liga_id']}'>{$row['liga_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Right column -->
        <div class="col-md-6">
            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_ekip">Ekip</label>
                <select class="form-select" name="produkt_ekip" id="produkt_ekip">
                    <option value="">Zgjidh nje ekip</option>
                </select>
            </div>

            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_image1">Foto e Produktit 1</label>
                <input type="file" required class="form-control" name="produkt_image1" id="produkt_image1">
            </div>

            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_image2">Foto e Produktit 2</label>
                <input type="file" required class="form-control" name="produkt_image2" id="produkt_image2">
            </div>

            <div class="form-outline mb-3">
                <label class="form-label" for="produkt_image3">Foto e Produktit 3</label>
                <input type="file" required class="form-control" name="produkt_image3" id="produkt_image3">
            </div>

            <h5 class="mt-4">Stoku per Masat</h5>
            <div class="row">
                <?php
                $sizes = ['Small', 'Medium', 'Large', 'XL', 'XXL'];
                foreach ($sizes as $size) {
                    echo "
                    <div class='col-md-4'>
                        <label class='form-label'>$size</label>
                        <input type='number' class='form-control stock-input' name='stock_" . strtolower($size) . "' placeholder='Sasia' required min='0'>
                    </div>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="text-center mt-4">
        <button type="submit" 
            class="btn" 
            style="background-color: black; color: white; border: 1px solid black;margin: 10px;" 
            onmouseover="this.style.color='#ffce00'; this.style.borderColor='#ffce00';" 
            onmouseout="this.style.color='white'; this.style.borderColor='black';">
            Shto Produktin
        </button>
    </div>
</form>
        </div>

    
    </div>
    <!-- jQuery (required for Toastr) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</body>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
    
    document.getElementById("produkt_liga").addEventListener("change", function () {
        const ligaId = this.value;
        const ekipDropdown = document.getElementById("produkt_ekip");

        
        ekipDropdown.innerHTML = "<option value=''>Zgjidh nje ekip</option>";

        if (ligaId) {
            fetch("./controllers/fetch_teams.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ liga_id: ligaId }),
            })
                .then((response) => response.text())
                .then((data) => {
                    
                    ekipDropdown.innerHTML += data;
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        }
    });

    
document.getElementById("produkt_form").addEventListener("submit", async function (event) {
    event.preventDefault(); 

    
    const formData = new FormData(this);

    try {
        
        const response = await fetch("./controllers/ajax_add.php", {
            method: "POST",
            body: formData,
        });

        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        
        const result = await response.json();

        
        if (result.status == "success") {
            toastr.success(result.message);
            setTimeout(() => {
                window.location.href = "index.php"; 
            }, 2000);
        } else if (result.status == "error") {
            toastr.error(result.message);
        } else if (result.status == "warning") {
            toastr.warning(result.message);
        } else {
            toastr.info("Unknown response received.");
        }
    } catch (error) {
        toastr.error("An error occurred while processing the request.");
        console.error("Error:", error);
    }
});


    
    document.querySelectorAll(".stock-input").forEach((input) => {
        input.addEventListener("input", function () {
            if (this.value < 0) {
                this.value = 0; 
            }
        });
    });
});

                </script>
</html>

<?php
mysqli_close($con);
?>
