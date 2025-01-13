<?php
// Fetch product details for editing
if (isset($_GET['edit_produkt'])) {
    $edit_id = $_GET['edit_produkt'];
    $get_data = "SELECT * FROM `produkt` WHERE produkt_id = $edit_id";
    $result = mysqli_query($con, $get_data);
    $row = mysqli_fetch_assoc($result);

    // Assign values to variables
    $product_name = $row['produkt_name'];
    $product_description = $row['produkt_description'];
    $product_keywords = $row['produkt_keywords'];
    $liga_id = $row['liga_id'];
    $ekip_id = $row['ekip_id'];
    $product_image1 = $row['produkt_image1'];
    $product_image2 = $row['produkt_image2'];
    $product_image3 = $row['produkt_image3'];
    $product_price = $row['produkt_price'];

    // Fetch Liga name
    $select_liga = "SELECT * FROM `liga` WHERE liga_id = $liga_id";
    $result_liga = mysqli_query($con, $select_liga);
    $row_liga = mysqli_fetch_assoc($result_liga);
    $liga_name = $row_liga['liga_name'];

    // Fetch Ekip name
    $select_ekip = "SELECT * FROM `ekip` WHERE ekip_id = $ekip_id";
    $result_ekip = mysqli_query($con, $select_ekip);
    $row_ekip = mysqli_fetch_assoc($result_ekip);
    $ekip_name = $row_ekip['ekip_name'];
}
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Edit Produkt</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

        <div class="row">
            <!-- Left column -->
            <div class="col-md-6">
                <!-- Product Name -->
                <div class="form-outline mb-3">
                    <label for="produkt_name" class="form-label">Emri i Produktit</label>
                    <input type="text" required class="form-control" name="produkt_name" id="produkt_name" value="<?php echo htmlspecialchars($product_name); ?>" placeholder="Shkruaj emrin e produktit">
                </div>

                <!-- Product Description -->
                <div class="form-outline mb-3">
                    <label for="produkt_description" class="form-label">Pershkrimi i Produktit</label>
                    <textarea required class="form-control" name="produkt_description" id="produkt_description" rows="3" placeholder="Shkruaj pershkrimin"><?php echo htmlspecialchars($product_description); ?></textarea>
                </div>

                <!-- Product Keywords -->
                <div class="form-outline mb-3">
                    <label for="produkt_keywords" class="form-label">Keywords per Produktin</label>
                    <input type="text" required class="form-control" name="produkt_keywords" id="produkt_keywords" value="<?php echo htmlspecialchars($product_keywords); ?>" placeholder="Shkruaj keywords">
                </div>

                <!-- Product Price -->
                <div class="form-outline mb-3">
                    <label for="produkt_price" class="form-label">Cmimi i Produktit (€)</label>
                    <input type="number" step="0.01" required class="form-control" name="produkt_price" id="produkt_price" value="<?php echo htmlspecialchars($product_price); ?>" placeholder="Shkruaj cmimin">
                </div>

                <!-- Liga Dropdown -->
                <div class="form-outline mb-3">
                    <label for="produkt_liga" class="form-label">Liga</label>
                    <select class="form-select" name="produkt_liga" id="produkt_liga">
                        <option value="<?php echo $liga_id; ?>"><?php echo $liga_name; ?></option>
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
                <!-- Ekip Dropdown -->
                <div class="form-outline mb-3">
                    <label for="produkt_ekip" class="form-label">Ekip</label>
                    <select class="form-select" name="produkt_ekip" id="produkt_ekip">
                        <option value="<?php echo $ekip_id; ?>"><?php echo $ekip_name; ?></option>
                    </select>
                </div>

                <!-- Product Image 1 -->
                <div class="form-outline mb-3">
                    <label for="produkt_image1" class="form-label">Foto e Produktit 1</label>
                    <div class="d-flex">
                        <input type="file" class="form-control" name="produkt_image1" id="produkt_image1" onchange="previewImage(1)">
                        <img id="preview_image1" src="./produkt_image/<?php echo $product_image1; ?>" width="100" alt="Image 1">
                    </div>
                </div>

                <!-- Product Image 2 -->
                <div class="form-outline mb-3">
                    <label for="produkt_image2" class="form-label">Foto e Produktit 2</label>
                    <div class="d-flex">
                        <input type="file" class="form-control" name="produkt_image2" id="produkt_image2" onchange="previewImage(2)">
                        <img id="preview_image2" src="./produkt_image/<?php echo $product_image2; ?>" width="100" alt="Image 2">
                    </div>
                </div>

                <!-- Product Image 3 -->
                <div class="form-outline mb-3">
                    <label for="produkt_image3" class="form-label">Foto e Produktit 3</label>
                    <div class="d-flex">
                        <input type="file" class="form-control" name="produkt_image3" id="produkt_image3" onchange="previewImage(3)">
                        <img id="preview_image3" src="./produkt_image/<?php echo $product_image3; ?>" width="100" alt="Image 3">
                    </div>
                </div>

                <!-- Stock for Sizes -->
                <h5 class="mt-4">Stoku per Masat</h5>
                <div class="row">
                    <?php
                    // Fetch stock for each size
                    $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                    foreach ($sizes as $size) {
                        // Fetch stock from the sizes table
                        $stock_query = "SELECT stock FROM `sizes` WHERE produkt_id = $edit_id AND size = '$size'";
                        $stock_result = mysqli_query($con, $stock_query);
                        $stock_row = mysqli_fetch_assoc($stock_result);
                        $stock = $stock_row['stock'] ?? 0;
                        echo "
                            <div class='col-md-4'>
                                <label for='stock_" . strtolower($size) . "' class='form-label'>$size</label>
                                <input type='number' class='form-control' name='stock_" . strtolower($size) . "' value='$stock' min='0'>
                            </div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-4">
        <input type="submit" class="btn" style="background-color: #ffce00; border-color: #ffce00;" name="edit_produkt" value="Përditëso Produktin">
        </div>
    </form>
</div>

<?php
// Update product data
if (isset($_POST['edit_produkt'])) {
    $edit_id = $_POST['edit_id'];
    $product_name = $_POST['produkt_name'];
    $product_description = $_POST['produkt_description'];
    $product_keywords = $_POST['produkt_keywords'];
    $liga_id = $_POST['produkt_liga'];
    $ekip_id = $_POST['produkt_ekip'];
    $product_price = $_POST['produkt_price'];

   // Handle images (check if files were uploaded)
   $product_image1 = isset($_FILES['produkt_image1']['name']) && $_FILES['produkt_image1']['name'] != '' ? $_FILES['produkt_image1']['name'] : $product_image1;
   $product_image2 = isset($_FILES['produkt_image2']['name']) && $_FILES['produkt_image2']['name'] != '' ? $_FILES['produkt_image2']['name'] : $product_image2;
   $product_image3 = isset($_FILES['produkt_image3']['name']) && $_FILES['produkt_image3']['name'] != '' ? $_FILES['produkt_image3']['name'] : $product_image3;
   
   // Get temporary file paths for each image upload
   $temp_image1 = $_FILES['produkt_image1']['tmp_name'] ?? '';
   $temp_image2 = $_FILES['produkt_image2']['tmp_name'] ?? '';
   $temp_image3 = $_FILES['produkt_image3']['tmp_name'] ?? '';
   
   // Move uploaded files if new images are selected
   if ($temp_image1) {
       move_uploaded_file($temp_image1, "./produkt_image/$product_image1");
   }
   if ($temp_image2) {
       move_uploaded_file($temp_image2, "./produkt_image/$product_image2");
   }
   if ($temp_image3) {
       move_uploaded_file($temp_image3, "./produkt_image/$product_image3");
   }

    // Update product details in database
    $update_product = "UPDATE `produkt` SET 
        produkt_name = '$product_name',
        produkt_description = '$product_description',
        produkt_keywords = '$product_keywords',
        liga_id = '$liga_id',
        ekip_id = '$ekip_id',
        produkt_image1 = '$product_image1',
        produkt_image2 = '$product_image2',
        produkt_image3 = '$product_image3',
        produkt_price = '$product_price'
        WHERE produkt_id = $edit_id";
    $result_update = mysqli_query($con, $update_product);

    // Handle stock for sizes
    foreach ($sizes as $size) {
        $stock = $_POST['stock_' . strtolower($size)] ?? 0;
        $update_stock = "UPDATE `sizes` SET stock = '$stock' WHERE produkt_id = $edit_id AND size = '$size'";
        mysqli_query($con, $update_stock);
    }

    if ($result_update) {
        echo "<script>alert('Produkti u përditësua me sukses!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Dështoi përditësimi i produktit!');</script>";
    }
}

mysqli_close($con);
?>
