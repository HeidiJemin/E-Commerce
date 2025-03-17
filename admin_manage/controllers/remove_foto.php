<?php
require_once('../../includes/connect.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $user_id = intval($_POST['user_id']);

    
    $query = "SELECT foto FROM users WHERE user_id = $user_id";
    $result = mysqli_query($con, $query);

    if ($result && $user = mysqli_fetch_assoc($result)) {
        if (!empty($user['foto'])) {
            $foto_path = "../../uploads/" . $user['foto'];

            
            if (file_exists($foto_path)) {
                unlink($foto_path);
            }

            
            $update_query = "UPDATE users SET foto = NULL WHERE user_id = $user_id";
            if (mysqli_query($con, $update_query)) {
                echo json_encode(['status' => 'success', 'message' => 'Photo removed successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update the database.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No photo found for this user.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    }

    mysqli_close($con);
}
?>
