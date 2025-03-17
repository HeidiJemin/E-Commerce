<!-- Payments Table -->
<h3 class="text-center my-4">Payments Table</h3>

<div class="container">
  <table id="paymentsTable" class="table table-bordered mt-5 display">
    <thead class="table">
      <tr class="text-center">
        <th>Payment ID</th>
        <th>User ID</th>
        <th>Payment Method</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $query = "
        SELECT 
          payments.payment_id, 
          payments.user_id,
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
                  <td class='text-center'> {$row['payment_method']}</td>
                  <td class='text-end'>\$" . number_format($row['total_price'], 2) . "</td>
                  <td class='text-center'>" . 
                    ($row['payment_status'] === 'PENDING' ? 
                      "<span class='badge bg-warning'>PENDING</span>" : 
                      "<span class='badge bg-success'>COMPLETED</span>") . 
                  "</td>
                  <td class='text-center'>{$row['created_at']}</td>
                </tr>";
        }
      } 
      mysqli_close($con);
      ?>
    </tbody>
  </table>
</div>


<script>
  $(document).ready(function () {
    
    var table = $('#paymentsTable').DataTable({
      paging: true,
      searching: true,
      info: true,
      order: [[5, 'desc']], 
      responsive: true,
      language: {
        emptyTable: "No payments available",
        search: "Search Payments:",
        lengthMenu: "Show _MENU_ entries",
        paginate: {
          first: "First",
          last: "Last",
          next: "Next",
          previous: "Previous"
        },
        info: "Showing _START_ to _END_ of _TOTAL_ payments",
        infoEmpty: "No payments available", 
        zeroRecords: "No records match your search"
      }
    });

  });
</script>
