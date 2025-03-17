<?php
require_once('../../includes/connect.php');

if (isset($_POST['ekip_id'])) {
    $ekip_id = $_POST['ekip_id'];

    

    
    $delete_query = "DELETE FROM `ekip` WHERE `ekip_id` = $ekip_id";
    $result = mysqli_query($con, $delete_query);

    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Ekip deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting the ekip.']);
    }
}
mysqli_close($con);
?>
