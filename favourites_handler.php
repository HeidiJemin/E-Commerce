<?php
session_start();
include('includes/connect.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
    exit;
}

$user_id = $_SESSION['id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['produkt_id']) || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$produkt_id = $data['produkt_id'];
$action = $data['action'];

if ($action === 'add') {
    // Add to favourites
    $query = "INSERT INTO favourites (produkt_id, user_id) VALUES ($produkt_id, $user_id)";
    $result = mysqli_query($con, $query);
    echo json_encode(['success' => $result, 'message' => $result ? 'Added to favourites.' : 'Failed to add.']);
} elseif ($action === 'remove') {
    // Remove from favourites
    $query = "DELETE FROM favourites WHERE produkt_id = $produkt_id AND user_id = $user_id";
    $result = mysqli_query($con, $query);
    echo json_encode(['success' => $result, 'message' => $result ? 'Removed from favourites.' : 'Failed to remove.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
?>
