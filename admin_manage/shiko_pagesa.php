<!-- Payments Table -->
<h3 class="text-center my-4">Payments Table</h3>

<div class="container">
  <table id="paymentsTable" class="table table-bordered mt-5 display">
    <thead class="table">
      <tr class="text-center">
        <th>Payment ID</th>
        <th>User ID</th>
        <th>Stripe User</th>
        <th>Payment Method</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $query = "
        SELECT 
          payments.payment_id, 
          payments.user_id, 
          payments.stripe_user, 
          payments.payment_method, 
          payments.total_price, 
          payments.payment_status, 
          payments.created_at
        FROM payments
      ";

      $result = $con->query($query);

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td class='text-center'>{$row['payment_id']}</td>
                  <td class='text-center'>{$row['user_id']}</td>
                  <td class='text-center'>" . ($row['stripe_user'] ?: 'N/A') . "</td>
                  <td class='text-center'>" . ucfirst($row['payment_method'] ?: 'Unknown') . "</td>
                  <td class='text-end'>\$" . number_format($row['total_price'], 2) . "</td>
                  <td class='text-center'>" . 
                    ($row['payment_status'] === 'PENDING' ? 
                      "<span class='badge bg-warning'>PENDING</span>" : 
                      "<span class='badge bg-success'>COMPLETED</span>") . 
                  "</td>
                  <td class='text-center'>{$row['created_at']}</td>
                  <td class='text-center'>
                    <button class='btn btn-sm btn-danger delete-payment' data-id='{$row['payment_id']}'>
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
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete Payment ID: <strong id="paymentIdToDelete"></strong>?
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
    // Initialize DataTable with custom settings
    var table = $('#paymentsTable').DataTable({
      paging: true,
      searching: true,
      info: true,
      order: [[6, 'desc']], // Orders by the 'Created At' column in descending order
      responsive: true,
      language: {
        search: "Search Payments:",
        lengthMenu: "Show _MENU_ entries",
        paginate: {
          first: "First",
          last: "Last",
          next: "Next",
          previous: "Previous"
        },
        info: "Showing _START_ to _END_ of _TOTAL_ payments",
        infoEmpty: "No payments available", // Message when no data is available
        zeroRecords: "No records match your search" // Custom message for no matching records
      }
    });

    // Open delete modal when delete button is clicked
    $('.delete-payment').click(function () {
      const paymentId = $(this).data('id');
      $('#paymentIdToDelete').text(paymentId);
      $('#deleteModal').modal('show');
      $('#confirmDelete').data('id', paymentId);
    });

    // Handle delete request when the confirm delete button is clicked
    $('#confirmDelete').click(function () {
      const paymentId = $(this).data('id');
      $.ajax({
        url: 'delete_payment.php',
        type: 'POST',
        data: { payment_id: paymentId },
        success: function(response) {
          const data = JSON.parse(response);

          if (data.success) {
            // Show success message (toast or alert)
            alert('Payment deleted successfully!');
            
            // Remove the row from DataTable
            table.row($(`button[data-id="${paymentId}"]`).closest('tr')).remove().draw();
          } else {
            // Show error message if deletion failed
            alert('Failed to delete the payment. Please try again.');
          }
          $('#deleteModal').modal('hide');
        },
        error: function() {
          alert('Error processing your request.');
          $('#deleteModal').modal('hide');
        }
      });
    });
  });
</script>
