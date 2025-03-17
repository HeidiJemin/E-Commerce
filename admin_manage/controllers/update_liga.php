<?php
header('Content-Type: application/json');
require_once('../../includes/connect.php');

$response = [];


if (isset($_POST['liga_id']) && isset($_POST['liga_name'])) {
    
    $liga_id = intval($_POST['liga_id']);
    $liga_name = mysqli_real_escape_string($con, $_POST['liga_name']);

    
    $check_query = "SELECT * FROM `liga` WHERE LOWER(liga_name) = LOWER('$liga_name') AND liga_id != $liga_id";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        
        $response = [
            'status' => 'error',
            'message' => 'Një ligë me këtë emër ekziston tashmë!'
        ];
    } else {
        
        $update_query = "UPDATE `liga` SET liga_name='$liga_name' WHERE liga_id=$liga_id";
        $result = mysqli_query($con, $update_query);

        if ($result) {
            $response = [
                'status' => 'success',
                'message' => 'Liga u përditësua me sukses!'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Nuk mund të përditësohet liga!'
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
