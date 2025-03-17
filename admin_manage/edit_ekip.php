<?php
if (isset($_GET['edit_ekip'])) {
    $edit_ekip = intval($_GET['edit_ekip']);

    // existing ekip details
    $get_ekip = "SELECT * FROM `ekip` WHERE ekip_id='$edit_ekip'";
    $result = mysqli_query($con, $get_ekip);
    $row = mysqli_fetch_assoc($result);
    $ekip_name = $row['ekip_name'];
    $liga_id = $row['liga_id'];
}
?>


    <div class="container mt-3">
        <h1 class="text-center">Edit Ekip</h1>
        <form id="editEkipForm" class="text-center">
            <!-- Ekip Name -->
            <div class="form-outline mb-4 w-50 m-auto">
                <label for="ekip_name" class="form-label text-start d-block">Ekip Name</label>
                <input type="text" name="ekip_name" id="ekip_name" class="form-control"
                    value="<?php echo htmlspecialchars($ekip_name); ?>" required>
            </div>

            <!-- Liga Dropdown -->
            <div class="form-outline mb-4 w-50 m-auto">
                <label for="liga_id" class="form-label text-start d-block">Select Liga</label>
                <select name="liga_id" id="liga_id" class="form-select" required>
                    <?php
                    
                    $current_liga_query = "SELECT * FROM `liga` WHERE liga_id='$liga_id'";
                    $current_liga_result = mysqli_query($con, $current_liga_query);
                    if ($current_liga_result && mysqli_num_rows($current_liga_result) > 0) {
                        $current_liga_row = mysqli_fetch_assoc($current_liga_result);
                        echo "<option value='{$current_liga_row['liga_id']}' selected>{$current_liga_row['liga_name']}</option>";
                    } else {
                        echo "<option disabled selected>No Liga Found</option>";
                    }

                    // Show other liga options
                    $other_liga_query = "SELECT * FROM `liga` WHERE liga_id != '$liga_id'";
                    $other_liga_result = mysqli_query($con, $other_liga_query);
                    while ($row_liga = mysqli_fetch_assoc($other_liga_result)) {
                        echo "<option value='{$row_liga['liga_id']}'>{$row_liga['liga_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="button" id="updateEkip" class="btn px-3 mb-3"
                style="background-color: #ffce00; border-color: #ffce00;">
                Update Ekip
            </button>
            <input type="hidden" id="ekip_id" name="ekip_id" value="<?php echo $edit_ekip; ?>">
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $('#updateEkip').on('click', function () {
                
                const data = {
                    ekip_id: $('#ekip_id').val(),
                    ekip_name: $('#ekip_name').val(),
                    liga_id: $('#liga_id').val()
                };

                
                $.ajax({
                    url: './controllers/update_ekip.php', 
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(() => {
                                window.location.href = './index.php?shiko_ekip';
                            }, 2000); // Redirect after 2 seconds
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('Diçka shkoi keq. Provoni përsëri!');
                    }
                });
            });
        });
    </script>

<?php


    mysqli_close($con);

?>