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

      if ($result->num_rows > 0) {
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

          if ($row['status'] === 'PENDING') {
            echo "<button class='btn btn-sm btn-danger delete-order' data-id='{$row['order_id']}'>
                    Delete
                  </button>";
          } else {
            echo "<span class='badge bg-success'>COMPLETED</span>";
          }

          echo "</td>
                <td class='text-center'>{$row['created_at']}</td>
              </tr>";
        }
      } else {
        echo "<tr><td colspan='7' class='text-center'>No orders found</td></tr>";
      }
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
    $('#ordersTable').DataTable({
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
        infoEmpty: "No orders available"
      }
    });

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
            alert('Order deleted successfully!');
            location.reload(); // Reload the page to update the table
          } else {
            alert('Failed to delete the order. Please try again.');
          }
          $('#deleteModal').modal('hide'); // Close the modal
        },
        error: function() {
          alert('There was an error processing your request.');
          $('#deleteModal').modal('hide'); // Close the modal in case of error
        }
      });
    });
  });
</script>
