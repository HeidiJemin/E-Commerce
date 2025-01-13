<?php


if (isset($_GET['edit_ekip'])) {
    $edit_ekip = $_GET['edit_ekip'];

    // Fetch existing ekip details
    $get_ekip = "SELECT * FROM `ekip` WHERE ekip_id='$edit_ekip'";
    $result = mysqli_query($con, $get_ekip);
    $row = mysqli_fetch_assoc($result);
    $ekip_name = $row['ekip_name'];
    $liga_id = $row['liga_id'];
}

// Handle form submission
if (isset($_POST['edit_ekip'])) {
    $ekip_name_new = $_POST['ekip_name'];
    $liga_id_new = $_POST['liga_id'];

    // Update ekip details
    $update_query = "UPDATE `ekip` SET ekip_name='$ekip_name_new', liga_id='$liga_id_new' WHERE ekip_id=$edit_ekip";
    $result_ekip = mysqli_query($con, $update_query);

    if ($result_ekip) {
        // Redirect to shiko_ekip after successful update
        header("Location: ./index.php?shiko_ekip");
        exit; // Stop further execution to prevent page content from being displayed before redirection
    } else {
        // In case of failure, show an error message (optional)
        echo "<script>alert('Nuk mund të përditësohet ekipi!');</script>";
    }
}
mysqli_close($con);
?>

<div class="container mt-3">
    <h1 class="text-center">Edit Ekip</h1>
    <form action="" method="post" class="text-center">
        <!-- Ekip Name -->
        <div class="form-outline mb-4 w-50 m-auto">
            <label for="ekip_name" class="form-label text-start d-block">Ekip Name</label>
            <input type="text" name="ekip_name" id="ekip_name" class="form-control" value="<?php echo isset($ekip_name) ? $ekip_name : ''; ?>" required="required">
        </div>

        <!-- Liga Dropdown -->
        <div class="form-outline mb-4 w-50 m-auto">
            <label for="liga_id" class="form-label text-start d-block">Select Liga</label>
            <select name="liga_id" id="liga_id" class="form-select" required>
                <?php
                // Show the current liga as the selected option
                $current_liga_query = "SELECT * FROM `liga` WHERE liga_id='$liga_id'";
                $current_liga_result = mysqli_query($con, $current_liga_query);
                $current_liga_row = mysqli_fetch_assoc($current_liga_result);
                echo "<option value='{$current_liga_row['liga_id']}' selected>{$current_liga_row['liga_name']}</option>";

                // Show other liga options and make sure to keep the new selected value if posted
                $other_liga_query = "SELECT * FROM `liga` WHERE liga_id != '$liga_id'";
                $other_liga_result = mysqli_query($con, $other_liga_query);
                while ($row_liga = mysqli_fetch_assoc($other_liga_result)) {
                    // If this liga is the one being updated, select it
                    $selected = ($row_liga['liga_id'] == $liga_id_new) ? 'selected' : '';
                    echo "<option value='{$row_liga['liga_id']}' $selected>{$row_liga['liga_name']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Submit Button -->
        <input type="submit" value="Update Ekip" class="btn px-3 mb-3" name="edit_ekip" style="background-color: #ffce00; border-color: #ffce00;">
    </form>
</div>
