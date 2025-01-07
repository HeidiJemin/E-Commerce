<?php
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ekip_id'])) {
    $ekip_id = intval($_POST['ekip_id']);
    $query = "DELETE FROM `ekip` WHERE `ekip_id` = $ekip_id";
    if (mysqli_query($con, $query)) {
        echo "Ekip deleted successfully.";
    } else {
        http_response_code(500);
        echo "Failed to delete ekip.";
    }
}
mysqli_close($con);
?>
