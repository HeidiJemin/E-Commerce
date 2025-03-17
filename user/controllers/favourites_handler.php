<?php
session_start();
require_once('../../includes/connect.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
    mysqli_close($con);
    exit;
}

$user_id = $_SESSION['id'];
$data = json_decode(file_get_contents('php://input'), true);


$action = $_POST['action'] ?? $data['action'] ?? null;
$produkt_id = $_POST['produkt_id'] ?? $data['produkt_id'] ?? null;


if (!$produkt_id || !$action) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    mysqli_close($con);
    exit;
}


$produkt_id = (int) $produkt_id; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        // Add to favourites
        $query = "INSERT INTO favourites (produkt_id, user_id) VALUES ($produkt_id, $user_id)";
        $result = mysqli_query($con, $query);
        echo json_encode(['success' => $result, 'message' => $result ? 'Added to favourites.' : 'Failed to add.']);
        mysqli_close($con);
        
    } elseif ($action === 'remove') {
        // Remove from favourites
        $query = "DELETE FROM favourites WHERE produkt_id = $produkt_id AND user_id = $user_id";
        $result = mysqli_query($con, $query);
        echo json_encode(['success' => $result, 'message' => $result ? 'Removed from favourites.' : 'Failed to remove.']);
        mysqli_close($con);
    } else {
        
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        mysqli_close($con);
    }
} else {

    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    mysqli_close($con);
}


?>

