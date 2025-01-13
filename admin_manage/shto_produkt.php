<?php

// Start the session and include necessary files
session_start();
ob_start();
include('../includes/connect.php');

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to the login page if not logged in
    header("Location: ../login.php");
    exit;
}

// Ensure the user has admin privileges
if ((int)$_SESSION['role_id'] !== 0) {
    // Redirect unauthorized users to the home page or another appropriate page
    header("Location: ../index.php");
    exit;
}

// Optional: Log the current user details for auditing or debugging (remove in production)
$username = $_SESSION['username'];
$userId = $_SESSION['id'];

// Example logging (optional, for debugging)
// error_log("Admin Access: User ID: $userId, Username: $username");

include('../includes/connect.php');

if (isset($_POST['shto_produkt'])) {
    $produkt_name = $_POST['produkt_name'];
    $produkt_description = $_POST['produkt_description'];
    $produkt_keywords = $_POST['produkt_keywords'];
    $produkt_liga = $_POST['produkt_liga'];
    $produkt_ekip = $_POST['produkt_ekip'];
    $produkt_price = $_POST['produkt_price'];
    $produkt_status = 'true';
    $produkt_image1 = $_FILES['produkt_image1']['name'];
    $produkt_image2 = $_FILES['produkt_image2']['name'];
    $produkt_image3 = $_FILES['produkt_image3']['name'];

    $temp_image1 = $_FILES['produkt_image1']['tmp_name'];
    $temp_image2 = $_FILES['produkt_image2']['tmp_name'];
    $temp_image3 = $_FILES['produkt_image3']['tmp_name'];

    // Map form inputs to size names for the database
    $sizes = [
        'S' => $_POST['stock_small'],
        'M' => $_POST['stock_medium'],
        'L' => $_POST['stock_large'],
        'XL' => $_POST['stock_xl'],
        'XXL' => $_POST['stock_xxl'],
    ];

    if (
        $produkt_name == '' || $produkt_description == '' || $produkt_keywords == '' || $produkt_liga == '' ||
        $produkt_ekip == '' || $produkt_price == '' || $produkt_image1 == '' || $produkt_image2 == '' || $produkt_image3 == ''
    ) {
        echo "<script>alert('Ju lutem jepni informacionin e plote.')</script>";
        exit();
    } else {
        move_uploaded_file($temp_image1, "./produkt_image/$produkt_image1");
        move_uploaded_file($temp_image2, "./produkt_image/$produkt_image2");
        move_uploaded_file($temp_image3, "./produkt_image/$produkt_image3");

        // Insert product details
        $insert_products = "INSERT INTO `produkt` 
            (produkt_name, produkt_description, produkt_keywords, liga_id, ekip_id, produkt_image1, produkt_image2, produkt_image3, produkt_price, date, status) 
            VALUES 
            ('$produkt_name', '$produkt_description', '$produkt_keywords', '$produkt_liga', '$produkt_ekip', '$produkt_image1', '$produkt_image2', '$produkt_image3', '$produkt_price', NOW(), '$produkt_status')";
        $result_query = mysqli_query($con, $insert_products);

        // Get the last inserted product ID
        $produkt_id = mysqli_insert_id($con);

        // Insert sizes and stock into the sizes table
        foreach ($sizes as $size => $stock) {
            $insert_sizes = "INSERT INTO `sizes` (produkt_id, size, stock) VALUES ('$produkt_id', '$size', '$stock')";
            mysqli_query($con, $insert_sizes);
        }

        if ($result_query) {
            echo "<script>
                alert('Produkti u shtua me sukses!');
                window.location.href = 'index.php';
            </script>";
            exit(); // Ensure no further code is executed
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shto Produkte - Admin</title>
    <link rel="stylesheet" href="./admin_style.css">
    <!-- bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- font awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous">
</head>

<body class="bg-light">
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
<div class="container mt-4">
    <h1 class="text-center mb-4">Shto Produkt</h1>
    <form action="" method="POST" enctype="multipart/form-data">
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
                    <label class="form-label" for="produkt_price">Cmimi i Produktit (€)</label>
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
    <input type="submit" 
           class="btn" 
           name="shto_produkt" 
           value="Shto Produktin" 
           style="background-color: black; color: white; border: 1px solid black;margin: 10px;" 
           onmouseover="this.style.color='#ffce00'; this.style.borderColor='#ffce00';" 
           onmouseout="this.style.color='white'; this.style.borderColor='black';">
</div>

</div>
    </form>
</div>
<script>
    document.getElementById('produkt_liga').addEventListener('change', function () {
        const ligaId = this.value;
        const ekipDropdown = document.getElementById('produkt_ekip');
        
        // Clear previous options
        ekipDropdown.innerHTML = "<option value=''>Zgjidh nje ekip</option>";
        
        if (ligaId) {
            fetch('fetch_teams.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `liga_id=${ligaId}`
            })
            .then(response => response.text())
            .then(data => {
                ekipDropdown.innerHTML += data;
            })
            .catch(error => console.error('Error:', error));
        }
    });
    
    
    document.querySelectorAll('.stock-input').forEach(input => {
        input.addEventListener('input', function () {
            if (this.value < 0) {
                this.value = 0; // Reset the value to 0 if negative
            }
        });
    });


</script>

</body>

</html>
<?php

mysqli_close($con);
?>