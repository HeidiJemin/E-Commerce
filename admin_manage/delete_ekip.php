<?php
include_once('../user/includes/connect.php');

if (isset($_POST['ekip_id'])) {
    $ekip_id = $_POST['ekip_id'];

    

    // SQL query to delete the 'ekip'
    $delete_query = "DELETE FROM `ekip` WHERE `ekip_id` = $ekip_id";
    $result = mysqli_query($con, $delete_query);

    // Return JSON response
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Ekip deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting the ekip.']);
    }
}
mysqli_close($con);
?>
