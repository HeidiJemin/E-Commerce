<!-- Orders Table -->
<h3 class="text-center my-4">Orders Table</h3>

<div class="container">
  <table id="ordersTable" class="table table-bordered mt-5 display">
    <thead class="table">
      <tr class="text-center">
        <th>Order ID</th>
        <th>Customer Name</th>
        <th>Email</th>
        <th>Price</th>
        <th>Items</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Delete</th> <!-- New Delete Column -->
      </tr>
    </thead>
    <tbody>
      <?php
      $query = "
        SELECT 
          orders.id AS order_id, 
          CONCAT(orders.firstname, ' ', orders.lastname) AS full_name, 
          orders.email, 
          orders.total_price, 
          GROUP_CONCAT(CONCAT(order_items.item_name, ' (', order_items.size, ')') SEPARATOR ', ') AS items,
          orders.status,
          orders.created_at
        FROM orders
        LEFT JOIN order_items ON orders.id = order_items.order_id
        GROUP BY orders.id
      ";

      $result = $con->query($query);

      // Check if the query returns any rows
      if ($result->num_rows > 0) {
        // Loop through rows and populate table
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td class='text-center'>{$row['order_id']}</td>
                  <td>{$row['full_name']}</td>
                  <td>{$row['email']}</td>
                  <td class='text-end'>\$" . number_format($row['total_price'], 2) . "</td>
                  <td>";
          $items = explode(', ', $row['items']);
          foreach ($items as $item) {
            echo $item . "<br>";
          }
          echo "</td>
                <td class='text-center'>";

          echo ($row['status'] === 'PENDING') 
            ? "<span class='badge bg-warning'>PENDING</span>" 
            : "<span class='badge bg-success'>COMPLETED</span>";
          echo "</td>
                <td class='text-center'>{$row['created_at']}</td>
                <td class='text-center'>
                  <button class='btn btn-sm btn-danger delete-order' data-id='{$row['order_id']}'>
                    Delete
                  </button>
                </td>
              </tr>";
        }
      }
      mysqli_close($con);
      ?>
    </tbody>
  </table>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete Order ID: <strong id="orderIdToDelete"></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Initialize DataTable -->
<script>
  $(document).ready(function () {
    // Initialize DataTable with proper settings
    var table = $('#ordersTable').DataTable({
      paging: true,
      searching: true,
      info: true,
      order: [[6, 'desc']], // Orders by the 'Created At' column in descending order
      responsive: true, // Makes the table responsive
      language: {
        search: "Search Orders:",
        lengthMenu: "Show _MENU_ entries",
        paginate: {
          first: "First",
          last: "Last",
          next: "Next",
          previous: "Previous"
        },
        info: "Showing _START_ to _END_ of _TOTAL_ orders",
        infoEmpty: "No orders available", // Message when no data is available
        zeroRecords: "No records match your search" // Custom message for no matching records
      }
    });

    // Check if there are any rows, if not, avoid DataTables warning
    if ($('#ordersTable tbody tr').length === 0) {
      // No need to display "No orders found" as it's handled by DataTable itself
      $('#ordersTable').DataTable().clear().draw();
    }

    // Open the modal and set the Order ID to delete
    $('.delete-order').click(function () {
      const orderId = $(this).data('id');
      $('#orderIdToDelete').text(orderId); // Show the Order ID in the modal
      $('#deleteModal').modal('show'); // Open the modal
      $('#confirmDelete').data('id', orderId); // Store Order ID on the Delete button
    });

    // Handle the confirmation button click
    $('#confirmDelete').click(function () {
      const orderId = $(this).data('id');
      $.ajax({
        url: 'delete_order.php',  // PHP file to handle deletion
        type: 'POST',
        data: { order_id: orderId },
        success: function(response) {
          const data = JSON.parse(response);
          if (data.success) {
            toastr.success('Order deleted successfully!');
            // Remove the row from the DataTable
            table.row($(`button[data-id="${orderId}"]`).closest('tr')).remove().draw();
          } else {
            toastr.error('Failed to delete the order. Please try again.');
          }
          $('#deleteModal').modal('hide'); // Close the modal
        },
        error: function() {
          toastr.error('There was an error processing your request.');
          $('#deleteModal').modal('hide'); // Close the modal in case of error
        }
      });
    });
  });
</script>
