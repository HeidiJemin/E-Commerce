<h3 class="text-center text-success">All Products</h3>
<table id="productTable" class="table table-bordered mt-5 display">
    <thead class="bg-white text-dark">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Product Image</th>
            <th>Product Price</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $get_products = "Select * from `produkt`";
            $result = mysqli_query($con, $get_products);
            $number = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $product_id = $row['produkt_id'];
                $product_name = $row['produkt_name'];
                $product_image1 = $row['produkt_image1'];
                $product_price = $row['produkt_price'];
                
                $number++;
        ?>
            <tr class="text-center">
                <td><?php echo $number; ?></td>
                <td><?php echo $product_name; ?></td>
                <td><img src='./produkt_image/<?php echo $product_image1; ?>' class='produkt_img' /></td>
                <td>$<?php echo $product_price; ?></td>
                
                <td>
                    <a href='index.php?edit_produkt=<?php echo $product_id ?>' class="btn btn-warning btn-sm">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                </td>
                <td>
                    <button class="btn btn-danger btn-sm delete-product" data-id="<?php echo $product_id; ?>">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete Product ID: <strong id="productIdToDelete"></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function () {
    var table = $('#productTable').DataTable(); 

    
    $('.delete-product').click(function () {
        const productId = $(this).data('id');
        $('#productIdToDelete').text(productId); 
        $('#deleteModal').modal('show'); 
        $('#confirmDelete').data('id', productId); 
    });

    $('#confirmDelete').click(function () {
        const productId = $(this).data('id');
        $.ajax({
            url: './controllers/delete_produkt.php',  
            type: 'POST',
            data: { product_id: productId },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.success) {
                    
                    table.row($(`button[data-id="${productId}"]`).closest('tr')).remove().draw();
                    toastr.success('Product deleted successfully!'); 
                } else {
                    toastr.error('Failed to delete the product. Please try again.'); 
                }
                $('#deleteModal').modal('hide'); 
            },
            error: function () {
                toastr.error('There was an error processing your request.'); 
                $('#deleteModal').modal('hide'); 
            }
        });
    });
});

</script>
<script>
        $(document).ready(function () {
            $('#productTable').DataTable();
        });

        function filterTeamsByLiga() {
            const ligaId = document.getElementById('produkt_liga').value;
            const data = new FormData();
            data.append("liga_id", ligaId);

            $.ajax({
                type: "POST",
                url: "./controllers/fetch_teams.php",
                async: false,
                cache: false,
                processData: false,
                data: data,
                contentType: false,
                success: function (response) {
                    const ekipSelect = document.getElementById('produkt_ekip');
                    ekipSelect.innerHTML = `<option value="" disabled selected>Zgjidh nje ekip</option>` + response;
                },
                error: function () {
                    console.error("An error occurred while fetching teams.");
                }
            });
        }
       
    </script>
<?php
mysqli_close($con);
?>