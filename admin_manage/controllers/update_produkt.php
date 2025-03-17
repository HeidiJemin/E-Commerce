<?php
require_once('../../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = mysqli_real_escape_string($con, $_POST['edit_id']);
    $product_name = mysqli_real_escape_string($con, $_POST['produkt_name']);
    $product_description = mysqli_real_escape_string($con, $_POST['produkt_description']);
    $product_keywords = mysqli_real_escape_string($con, $_POST['produkt_keywords']);
    $liga_id = mysqli_real_escape_string($con, $_POST['produkt_liga']);
    $ekip_id = mysqli_real_escape_string($con, $_POST['produkt_ekip']);
    $product_price = mysqli_real_escape_string($con, $_POST['produkt_price']);

    
    $check_product_query = "SELECT * FROM `produkt` WHERE LOWER(produkt_name) = LOWER('$product_name') AND produkt_id != $edit_id";
    $check_product_result = mysqli_query($con, $check_product_query);

    if (mysqli_num_rows($check_product_result) > 0) {
        echo json_encode(["success" => false, "message" => "Një produkt me këtë emër ekziston tashmë!"]);
        mysqli_close($con);
        exit;
    }

    
    $image_fields = ['produkt_image1', 'produkt_image2', 'produkt_image3'];
    $uploaded_images = [];

    foreach ($image_fields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES[$field]['name'];
            $uploaded_images[$field] = $file_name; 
        }
    }

    
    $update_product = "UPDATE `produkt` SET 
        produkt_name = '$product_name',
        produkt_description = '$product_description',
        produkt_keywords = '$product_keywords',
        liga_id = '$liga_id',
        ekip_id = '$ekip_id',
        produkt_price = '$product_price'";

    
    foreach ($uploaded_images as $field => $file_name) {
        $update_product .= ", $field = '$file_name'";
    }

    $update_product .= " WHERE produkt_id = $edit_id";
    $result_update = mysqli_query($con, $update_product);

    
    $sizes = ['s', 'm', 'l', 'xl', 'xxl'];
    foreach ($sizes as $size) {
        $stock_key = "stock_" . $size;
        if (isset($_POST[$stock_key])) {
            $stock_value = mysqli_real_escape_string($con, $_POST[$stock_key]);

            
            $size_query = "SELECT size_id FROM `sizes` WHERE produkt_id = $edit_id AND size = '$size'";
            $size_result = mysqli_query($con, $size_query);

            if ($size_result && mysqli_num_rows($size_result) > 0) {
                $size_row = mysqli_fetch_assoc($size_result);
                $size_id = $size_row['size_id'];

                
                $stock_query = "UPDATE `sizes` 
                                SET stock = '$stock_value' 
                                WHERE size_id = $size_id";
                mysqli_query($con, $stock_query);
            }
        }
    }

    if ($result_update) {
        echo json_encode([
            "success" => true,
            "message" => "Produkti u përditësua me sukses!",
            "redirect" => "index.php?view_products"
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Dështoi përditësimi i produktit!"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Kërkesë e pavlefshme!"]);
}

mysqli_close($con);
?>
