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
  GROUP_CONCAT(CONCAT(order_items.item_name, ' (', sizes.size, ')') SEPARATOR ', ') AS items,
  orders.status,
  orders.created_at
FROM orders
LEFT JOIN order_items ON orders.id = order_items.order_id
LEFT JOIN sizes ON order_items.size_id = sizes.size_id
GROUP BY orders.id;
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

          echo ($row['status'] === 'PENDING') 
            ? "<span class='badge bg-warning'>PENDING</span>" 
            : "<span class='badge bg-success'>COMPLETED</span>";
          echo "</td>
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
    
    var table = $('#ordersTable').DataTable({
      paging: true,
      searching: true,
      info: true,
      order: [[6, 'desc']], 
      responsive: true, 
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
        infoEmpty: "No orders available", 
        zeroRecords: "No records match your search" 
      }
    });

    
    if ($('#ordersTable tbody tr').length === 0) {
      $('#ordersTable').DataTable().clear().draw();
    }

    

    
    $('#confirmDelete').click(function () {
      const orderId = $(this).data('id');
      $.ajax({
        url: './controllers/delete_order.php',  
        type: 'POST',
        data: { order_id: orderId },
        success: function(response) {
          const data = JSON.parse(response);
          if (data.success) {
            toastr.success('Order deleted successfully!');
            
            table.row($(`button[data-id="${orderId}"]`).closest('tr')).remove().draw();
          } else {
            toastr.error('Failed to delete the order. Please try again.');
          }
          $('#deleteModal').modal('hide'); 
        },
        error: function() {
          toastr.error('There was an error processing your request.');
          $('#deleteModal').modal('hide'); 
        }
      });
    });
  });
</script>
