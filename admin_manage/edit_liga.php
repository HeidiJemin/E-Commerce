<?php
if (isset($_GET['edit_liga'])) {
    $edit_liga = $_GET['edit_liga'];
    
    $get_liga="Select * from `liga` where liga_id='$edit_liga'";
    $result=mysqli_query($con,$get_liga);
    $row = mysqli_fetch_assoc($result);
    $liga_name=$row['liga_name'];
    
}

if(isset($_POST['edit_liga'])){
    $liga_name_new=$_POST['liga_name'];
    $update_query="update `liga` set liga_name='$liga_name_new' where liga_id=$edit_liga";
    $result_liga=mysqli_query($con,$update_query);

    if($result_liga){
        echo "<script> alert('Liga u perditesua me sukses)</script>";
        echo "<script>window.open('./index.php?shiko_liga','_self')</script>";
    }
} 
mysqli_close($con);  
?>

<div class="container mt-3">
    <h1 class="text-center">Edit Liga</h1>
    <form action="" method="post" class="text-center">
        <div class="form-outline mb-4 w-50 m-auto">
            <label for="liga_name" class="form-label">Liga Name</label>
            <input type="text" name="liga_name" id="liga_name" class="form-control" value="<?php echo $liga_name;?>" required="required">
        </div>
        <input type="submit" value="Update Liga" class="btn px-3 mb-3" style="background-color: #ffce00; border-color: #ffce00;" name="edit_liga">
    </form>
</div>
