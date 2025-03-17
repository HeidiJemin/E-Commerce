<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('../../includes/connect.php');

if (!isset($_SESSION['id']) || (int)$_SESSION['role_id'] !== 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    mysqli_close($con);
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $produkt_name = mysqli_real_escape_string($con, $_POST['produkt_name']);
    $produkt_description = mysqli_real_escape_string($con, $_POST['produkt_description']);
    $produkt_keywords = mysqli_real_escape_string($con, $_POST['produkt_keywords']);
    $produkt_liga = mysqli_real_escape_string($con, $_POST['produkt_liga']);
    $produkt_ekip = mysqli_real_escape_string($con, $_POST['produkt_ekip']);
    $produkt_price = mysqli_real_escape_string($con, $_POST['produkt_price']);

    $sizes = [
        'S' => $_POST['stock_small'],
        'M' => $_POST['stock_medium'],
        'L' => $_POST['stock_large'],
        'XL' => $_POST['stock_xl'],
        'XXL' => $_POST['stock_xxl'],
    ];

    // Nese produkti ekziston
    $check_product_query = "SELECT * FROM `produkt` WHERE produkt_name = '$produkt_name'";
    $result = mysqli_query($con, $check_product_query);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Product already exists!']);
        mysqli_close($con);
        exit;
    }

    // image uploads
    $upload_dir = "../produkt_image/";
    $produkt_image1 = $_FILES['produkt_image1']['name'];
    $produkt_image2 = $_FILES['produkt_image2']['name'];
    $produkt_image3 = $_FILES['produkt_image3']['name'];

    move_uploaded_file($_FILES['produkt_image1']['tmp_name'], $upload_dir . $produkt_image1);
    move_uploaded_file($_FILES['produkt_image2']['tmp_name'], $upload_dir . $produkt_image2);
    move_uploaded_file($_FILES['produkt_image3']['tmp_name'], $upload_dir . $produkt_image3);

    
    $insert_query = "INSERT INTO `produkt` 
        (produkt_name, produkt_description, produkt_keywords, liga_id, ekip_id, produkt_image1, produkt_image2, produkt_image3, produkt_price, date) 
        VALUES ('$produkt_name', '$produkt_description', '$produkt_keywords', '$produkt_liga', '$produkt_ekip', '$produkt_image1', '$produkt_image2', '$produkt_image3', '$produkt_price', NOW())";

    if (mysqli_query($con, $insert_query)) {
        $produkt_id = mysqli_insert_id($con);

        foreach ($sizes as $size => $stock) {
            $insert_sizes = "INSERT INTO `sizes` (produkt_id, size, stock) VALUES ('$produkt_id', '$size', '$stock')";
            mysqli_query($con, $insert_sizes);
        }

        echo json_encode(['status' => 'success', 'message' => 'Product added successfully!']);
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error!']);
        
    }
}
mysqli_close($con);
?>
