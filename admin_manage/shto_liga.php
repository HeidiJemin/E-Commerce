<?php
require_once('../includes/connect.php');

if (isset($_POST['shto_liga'])) {
    
    $liga_name = mysqli_real_escape_string($con, $_POST['liga_name']);

    
    $select_query = "SELECT * FROM `liga` WHERE liga_name = '$liga_name'";
    $result_select = mysqli_query($con, $select_query);

    if (mysqli_num_rows($result_select) > 0) {
        
        echo "<script>toastr.error('Liga ekziston aktualisht');</script>";
    } else {
        
        $insert_query = "INSERT INTO `liga` (liga_name) VALUES ('$liga_name')";
        $result = mysqli_query($con, $insert_query);

        if ($result) {
            
            echo "<script>toastr.success('Liga u shtua me sukses');</script>";
        } else {
            
            echo "<script>toastr.error('Ka ndodhur një gabim, ju lutem provoni përsëri');</script>";
        }
    }
}
?>
<h2 class="text-center">Shto Liga</h2>
<form action="" method="post" class="mb-2">
    <div class="input-group mb-2 w-90">
        <span class="input-group-text" style="background-color: #ffce00;" id="basic-addon1">
            <i class="fa-solid fa-receipt"></i>
        </span>
        <input type="text" class="form-control" name="liga_name" placeholder="Shto një ligë" aria-label="Shto një ligë" aria-describedby="basic-addon1" required>
    </div>
    <div class="input-group d-flex justify-content-center align-items-center mb-2 w-10 m-auto">
        <input type="submit" class="text-white d-flex justify-content-center align-items-center p-2 my-3 border-0" name="shto_liga" value="Shto një ligë" style="background-color: #ffce00;">
    </div>
</form>
<?php

mysqli_close($con);
?>
