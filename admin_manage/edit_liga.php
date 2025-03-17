<?php

if (isset($_GET['edit_liga'])) {
    $edit_liga = $_GET['edit_liga'];
    
    $get_liga="Select * from `liga` where liga_id='$edit_liga'";
    $result=mysqli_query($con,$get_liga);
    $row = mysqli_fetch_assoc($result);
    $liga_name=$row['liga_name'];
    
}


mysqli_close($con);

?>

<div class="container mt-3">
        <h1 class="text-center">Edit Liga</h1>
        <form id="editLigaForm" class="text-center">
            <div class="form-outline mb-4 w-50 m-auto">
                <label for="liga_name" class="form-label">Liga Name</label>
                <input type="text" name="liga_name" id="liga_name" class="form-control"
                    value="<?php echo htmlspecialchars($liga_name); ?>" required>
            </div>
            <input type="hidden" name="liga_id" id="liga_id" value="<?php echo $edit_liga; ?>">
            <button type="button" id="updateLiga" class="btn px-3 mb-3"
                style="background-color: #ffce00; border-color: #ffce00;">
                Update Liga
            </button>
        </form>
    </div>
<script>
        $(document).ready(function () {
            $('#updateLiga').on('click', function () {
                
                const data = {
                    liga_id: $('#liga_id').val(),
                    liga_name: $('#liga_name').val()
                };

                
                $.ajax({
                    url: './controllers/update_liga.php', 
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(() => {
                                window.location.href = './index.php?shiko_liga';
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