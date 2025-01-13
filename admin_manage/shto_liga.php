<?php
include('../includes/connect.php');

if(isset($_POST['shto_liga'])){
    $liga_name = $_POST['liga_name'];

    // Check if Liga already exists
    $select_query = "SELECT * FROM `liga` WHERE liga_name='$liga_name'";
    $result_select = mysqli_query($con, $select_query);
    $number = mysqli_num_rows($result_select);

    if($number > 0){
        // Show error notification with Toastr
        echo "<script>toastr.error('Liga ekziston aktualisht');</script>";
    } else {
        // Insert new Liga
        $insert_query = "INSERT INTO `liga` (liga_name) VALUES ('$liga_name')";
        $result = mysqli_query($con, $insert_query);

        if($result){
            // Show success notification with Toastr
            echo "<script>toastr.success('Liga u shtua me sukses');</script>";
        } else {
            // Show error notification if there is an issue with the query
            echo "<script>toastr.error('Ka ndodhur nje gabim, ju lutem provoni perseri');</script>";
        }
    }
}
?>
<h2 class="text-center">Shto liga</h2>
<form action="" method="post" class="mb-2">
    <div class="input-group mb-2 w-90">
        <span class="input-group-text" style="background-color: #ffce00;" id="basic-addon1"><i class="fa-solid fa-receipt"></i></span>
        <input type="text" class="form-control" name="liga_name" placeholder="Shto nje lige" aria-label="Shto nje lige" aria-describedby="basic-addon1" required>
    </div>
    <div class="input-group d-flex justify-content-center align-items-center mb-2 w-10 m-auto">
        <input type="submit" class="text-white d-flex justify-content-center align-items-center p-2 my-3 border-0" name="shto_liga" value="Shto nje lige" style="background-color: #ffce00;">
    </div>
</form>
<?php
// Close the database connection
mysqli_close($con);
?>