<?php
header('Content-Type: application/json');
require_once('../../includes/connect.php');

$response = [];


if (isset($_POST['ekip_id']) && isset($_POST['ekip_name']) && isset($_POST['liga_id'])) {
    $ekip_id = intval($_POST['ekip_id']);
    $ekip_name = mysqli_real_escape_string($con, $_POST['ekip_name']);
    $liga_id = intval($_POST['liga_id']);

    // duplicate 
    $check_query = "SELECT * FROM `ekip` WHERE LOWER(ekip_name) = LOWER('$ekip_name') AND ekip_id != $ekip_id";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        
        $response = [
            'status' => 'error',
            'message' => 'Një ekip me këtë emër ekziston!'
        ];
    } else {
        
        $update_query = "UPDATE `ekip` SET ekip_name='$ekip_name', liga_id='$liga_id' WHERE ekip_id=$ekip_id";
        $result = mysqli_query($con, $update_query);

        if ($result) {
            $response = [
                'status' => 'success',
                'message' => 'Ekipi u përditësua me sukses!'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Nuk mund të përditësohet ekipi!'
            ];
        }
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Kërkesë e pavlefshme!'
    ];
}

echo json_encode($response);
mysqli_close($con);
?>
