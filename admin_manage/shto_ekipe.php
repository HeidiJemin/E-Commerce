<?php
include('../includes/connect.php');

if (isset($_POST['shto_ekip'])) {
    $ekip_name = $_POST['ekip_name'];
    $liga_id = $_POST['liga_id'];

    // Check if the Ekip already exists
    $select_query = "SELECT * FROM `ekip` WHERE ekip_name='$ekip_name'";
    $result_select = mysqli_query($con, $select_query);
    $number = mysqli_num_rows($result_select);

    if ($number > 0) {
        echo "<script>alert('Ekipi ekziston aktualisht');</script>";
    } else {
        // Insert new Ekip with Liga ID
        $insert_query = "INSERT INTO `ekip` (ekip_name, liga_id) VALUES ('$ekip_name', '$liga_id')";
        $result = mysqli_query($con, $insert_query);
        if ($result) {
            echo "<script>alert('Ekipi u shtua me sukses');</script>";
        }
    }
}

// Fetch all Liga for the dropdown
$liga_query = "SELECT * FROM `liga`";
$liga_result = mysqli_query($con, $liga_query);
?>
<h2 class="text-center">Shto ekipe</h2>
<form action="" method="post" class="mb-2">
    <!-- Input field for Ekip -->
    <div class="input-group mb-2 w-90">
        <span class="input-group-text bg-info" id="basic-addon1"><i class="fa-solid fa-receipt"></i></span>
        <input type="text" class="form-control" name="ekip_name" placeholder="Shto nje ekip" aria-label="ekip" aria-describedby="basic-addon1" required>
    </div>

    <!-- Select dropdown for Liga -->
    <div class="input-group mb-2 w-90">
        <span class="input-group-text bg-info" id="basic-addon2"><i class="fa-solid fa-list"></i></span>
        <select name="liga_id" class="form-select" aria-label="Zgjidhni Ligën" required>
            <option value="" disabled selected>Zgjidhni Ligën</option>
            <?php
            // Populate Liga dropdown dynamically
            $liga_query = "SELECT * FROM `liga`";
            $liga_result = mysqli_query($con, $liga_query);
            while ($liga_row = mysqli_fetch_assoc($liga_result)) {
                echo "<option value='{$liga_row['liga_id']}'>{$liga_row['liga_name']}</option>";
            }
            ?>
        </select>
    </div>

    <!-- Submit Button -->
    <div class="d-flex justify-content-center align-items-center input-group mb-2 w-10 m-auto">
        <input type="submit" class="bg-info text-white p-2 my-3 border-0" name="shto_ekip" value="Shto nje ekip">
    </div>
</form>
