<h3 class="text-center text-success">All Products</h3>
<table id="productTable" class="table table-bordered mt-5 display">
    <thead class="bg-white text-dark">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Product Image</th>
            <th>Product Price</th>
            <th>Status</th>
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
                $product_status = $row['status'];
                $number++;
        ?>
            <tr class="text-center">
                <td><?php echo $number; ?></td>
                <td><?php echo $product_name; ?></td>
                <td><img src='./produkt_image/<?php echo $product_image1; ?>' class='produkt_img' /></td>
                <td><?php echo $product_price; ?></td>
                <td><?php echo $product_status; ?></td>
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
        $('#productTable').DataTable();

        // Open the modal and set the Product ID to delete
        $('.delete-product').click(function () {
            const productId = $(this).data('id');
            $('#productIdToDelete').text(productId); // Show the Product ID in the modal
            $('#deleteModal').modal('show'); // Open the modal
            $('#confirmDelete').data('id', productId); // Store Product ID on the Delete button
        });

        // Handle the confirmation button click
        $('#confirmDelete').click(function () {
            const productId = $(this).data('id');
            $.ajax({
                url: 'delete_produkt.php',  // PHP file to handle deletion
                type: 'POST',
                data: { product_id: productId },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alert('Product deleted successfully!');
                        location.reload(); // Reload the page to update the table
                    } else {
                        alert('Failed to delete the product. Please try again.');
                    }
                    $('#deleteModal').modal('hide'); // Close the modal
                },
                error: function () {
                    alert('There was an error processing your request.');
                    $('#deleteModal').modal('hide'); // Close the modal in case of error
                }
            });
        });
    });
</script>
<?php

mysqli_close($con);
?>