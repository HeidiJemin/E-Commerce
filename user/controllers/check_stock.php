<?php

require_once('../../includes/connect.php');

//  size_ids via POST
if (!isset($_POST['size_ids']) || !is_array($_POST['size_ids'])) {
    echo json_encode(["error" => "Invalid size IDs provided."]);
    mysqli_close($con);
    exit;
    
}


$size_ids = $_POST['size_ids'];
$escaped_ids = array_map(function ($id) use ($con) {
    return "'" . mysqli_real_escape_string($con, $id) . "'";
}, $size_ids);


$escaped_ids_string = implode(',', $escaped_ids);


$query = "SELECT size_id, stock FROM sizes WHERE size_id IN ($escaped_ids_string)";
$result = mysqli_query($con, $query);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . mysqli_error($con)]);
    mysqli_close($con);
    exit;
}


$stockData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $stockData[] = [
        "size_id" => $row['size_id'],
        "stock" => (int)$row['stock']
    ];
}


echo json_encode($stockData);

mysqli_close($con);
?>

