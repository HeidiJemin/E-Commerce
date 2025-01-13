<?php
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['liga_id'])) {
        $liga_id = intval($_POST['liga_id']);

        // Fetch teams based on liga_id
        $query = "SELECT * FROM `ekip` WHERE liga_id = $liga_id";
        $result = mysqli_query($con, $query);

        $options = "";
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $options .= "<option value='{$row['ekip_id']}'>{$row['ekip_name']}</option>";
            }
        }
        echo $options; 
    } else {
        echo ""; 
    }
} else {
    echo ""; 
}
mysqli_close($con);
?>

