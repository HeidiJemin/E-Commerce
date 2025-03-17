<?php
//
if (isset($_GET['edit_produkt'])) {
    $edit_id = $_GET['edit_produkt'];
    $get_data = "SELECT * FROM `produkt` WHERE produkt_id = $edit_id";
    $result = mysqli_query($con, $get_data);
    $row = mysqli_fetch_assoc($result);

    
    $product_name = $row['produkt_name'];
    $product_description = $row['produkt_description'];
    $product_keywords = $row['produkt_keywords'];
    $liga_id = $row['liga_id'];
    $ekip_id = $row['ekip_id'];
    $product_image1 = $row['produkt_image1'];
    $product_image2 = $row['produkt_image2'];
    $product_image3 = $row['produkt_image3'];
    $product_price = $row['produkt_price'];

    
    $select_liga = "SELECT * FROM `liga` WHERE liga_id = $liga_id";
    $result_liga = mysqli_query($con, $select_liga);
    $row_liga = mysqli_fetch_assoc($result_liga);
    $liga_name = $row_liga['liga_name'];

    
    $select_ekip = "SELECT * FROM `ekip` WHERE ekip_id = $ekip_id";
    $result_ekip = mysqli_query($con, $select_ekip);
    $row_ekip = mysqli_fetch_assoc($result_ekip);
    $ekip_name = $row_ekip['ekip_name'];
}
?>


    <h1 class="text-center mb-4">Edit Produkt</h1>
    <form id="editProductForm" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

        <div class="row">
            
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
                    <label for="produkt_price" class="form-label">Cmimi i Produktit ($)</label>
                    <input type="number" step="0.01" required class="form-control" name="produkt_price" id="produkt_price" value="<?php echo htmlspecialchars($product_price); ?>" placeholder="Shkruaj cmimin">
                </div>

                <!-- Liga Dropdown -->
                <div class="form-outline mb-3">
                    <label for="produkt_liga" class="form-label">Liga</label>
                    
            <select name="produkt_liga" id="produkt_liga" class="form-select" onchange="filterTeamsByLiga()">
                <option value="<?php echo htmlspecialchars($liga_id); ?>"><?php echo htmlspecialchars($liga_name); ?></option>
                <?php
                $select_liga_all = "SELECT * FROM `liga`";
                $result_liga_all = mysqli_query($con, $select_liga_all);

                while ($row_liga_all = mysqli_fetch_assoc($result_liga_all)) {
                    $other_liga_id = $row_liga_all['liga_id'];
                    $other_liga_name = $row_liga_all['liga_name'];
                    if ($other_liga_id != $liga_id) {
                        echo "<option value='$other_liga_id'>" . htmlspecialchars($other_liga_name) . "</option>";
                    }
                }
                ?>
            </select>
                </div>
            </div>

            <!-- Right column -->
            <div class="col-md-6">
                <!-- Ekip Dropdown -->
                <div class="form-outline mb-3">
                <label for="produkt_ekip" class="form-label">Product Ekip</label>
            <select name="produkt_ekip" id="produkt_ekip" class="form-select">
                <option value="<?php echo htmlspecialchars($ekip_id); ?>"><?php echo htmlspecialchars($ekip_name); ?></option>
                <?php
                $select_ekip_all = "SELECT * FROM `ekip` WHERE liga_id = $liga_id";
                $result_ekip_all = mysqli_query($con, $select_ekip_all);

                while ($row_ekip_all = mysqli_fetch_assoc($result_ekip_all)) {
                    $other_ekip_id = $row_ekip_all['ekip_id'];
                    $other_ekip_name = $row_ekip_all['ekip_name'];
                    if ($other_ekip_id != $ekip_id) {
                        echo "<option value='$other_ekip_id'>" . htmlspecialchars($other_ekip_name) . "</option>";
                    }
                }
                ?>
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
                <input type='number' class='form-control' name='stock_" . strtolower($size) . "' id='stock_" . strtolower($size) . "' value='$stock' min='0'>
                            </div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-4">
        <button type="button" id="submitBtn" class="btn" style="background-color: #ffce00; border-color: #ffce00;">Përditëso Produktin</button>
    </div>
    </form>

    <script>
document.getElementById('submitBtn').addEventListener('click', function () {
    const formData = new FormData();

    
    const productDetails = {
        edit_id: <?php echo $edit_id; ?>,
        produkt_name: document.getElementById('produkt_name').value,
        produkt_description: document.getElementById('produkt_description').value,
        produkt_keywords: document.getElementById('produkt_keywords').value,
        produkt_liga: document.getElementById('produkt_liga').value,
        produkt_ekip: document.getElementById('produkt_ekip').value,
        produkt_price: document.getElementById('produkt_price').value,
    };

    
    for (const key in productDetails) {
        formData.append(key, productDetails[key]);
    }

    
    const sizes = ['s', 'm', 'l', 'xl', 'xxl'];
    sizes.forEach(size => {
        const stockInput = document.getElementById(`stock_${size}`);
        if (stockInput) {
            formData.append(`stock_${size}`, stockInput.value || 0);
        }
    });
    
    const imageFields = ['produkt_image1', 'produkt_image2', 'produkt_image3'];
    imageFields.forEach(field => {
        const fileInput = document.getElementById(field);
        if (fileInput && fileInput.files.length > 0) {
            formData.append(field, fileInput.files[0]);
        }
    });

    
    fetch('./controllers/update_produkt.php', {
    method: 'POST',
    body: formData
})
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message, "Sukses");
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000); // Delay for 2 seconds to show success notification
        } else {
            toastr.error(data.message, "Gabim");
        }
    })
    .catch(error => {
        toastr.error("Dështoi kërkesa AJAX!", "Gabim");
        console.error(error);
    });
});

</script>


    <?php
mysqli_close($con);
?>