<?php
require_once('../../includes/connect.php');

function deleteUser($user_id) {
    global $con; 

    $user_id = intval($user_id); 

    if (!$con) {
        http_response_code(500);
        return json_encode(["status" => "error", "message" => "Database connection failed."]);
    }

    
    $query = "SELECT foto FROM users WHERE user_id = $user_id";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $photo_path = '../../uploads/' . $user['foto'];

        
        if (empty($user['foto'])) {
            
            return deleteUserFromDatabase($user_id);
        }

        
        if (file_exists($photo_path) && is_file($photo_path)) {
            unlink($photo_path); // Deletes the photo
        }
    }

    
    return deleteUserFromDatabase($user_id);
}

function deleteUserFromDatabase($user_id) {
    global $con;
    $query = "DELETE FROM users WHERE user_id = $user_id";

    if (mysqli_query($con, $query)) {
        return json_encode(["status" => "success", "message" => "User deleted successfully."]);
    } else {
        http_response_code(500);
        return json_encode(["status" => "error", "message" => "Error deleting user."]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    echo deleteUser($user_id);
} else {
    http_response_code(400); 
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

mysqli_close($con);
?>
