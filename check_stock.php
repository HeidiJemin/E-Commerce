<?php
// Include the database connection
include('includes/connect.php');

// Ensure size_ids are sent via POST
if (!isset($_POST['size_ids']) || !is_array($_POST['size_ids'])) {
    echo json_encode(["error" => "Invalid size IDs provided."]);
    exit;
}

// Sanitize and prepare size IDs
$size_ids = $_POST['size_ids'];
$escaped_ids = array_map(function ($id) use ($con) {
    return "'" . mysqli_real_escape_string($con, $id) . "'";
}, $size_ids);

// Create a safe string for the query
$escaped_ids_string = implode(',', $escaped_ids);

// Query to fetch stock data
$query = "SELECT size_id, stock FROM sizes WHERE size_id IN ($escaped_ids_string)";
$result = mysqli_query($con, $query);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . mysqli_error($con)]);
    exit;
}

// Fetch results into an array
$stockData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $stockData[] = [
        "size_id" => $row['size_id'],
        "stock" => (int)$row['stock']
    ];
}

// Return stock data as JSON
echo json_encode($stockData);
?>
