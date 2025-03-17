<?php

session_start();
require_once('../includes/connect.php');
require_once('functions/common_function.php');
require_once('../includes/rememberme.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Commerce Website</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="./css/orders.css">
  
  <style>
    html, body { height: 100%; margin: 0; padding: 0; }
    .wrapper { display: flex; flex-direction: column; min-height: 100vh; }
    .content { flex: 1; }
    footer { padding: 0; text-align: center; }
  
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="content">
      <div class="container-fluid p-0">
        <!-- Navbar -->
        <?php include("../includes/header.php"); ?>

        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffce00;">
          <ul class="navbar-nav me-auto">
            <?php if (!isset($_SESSION['id'])): ?>
              <li class="nav-item ms-3"><a class="nav-link" href="login.php" style="color: black !important;">Login</a></li>
            <?php else: ?>
              <li class="nav-item ms-3"><a class="nav-link" href="profile.php" style="color: black !important;">Profile</a></li>
              <li class="nav-item ms-3"><a class="nav-link" href="logout.php" style="color: black !important;">Logout</a></li>
            <?php endif; ?>
          </ul>
        </nav>

        <div class="bg-light text-center">
          <h3>Hidden Store</h3>
          <p>Welcome to the world of football jerseys</p>
        </div>

        <div class="row px-1">
          <div class="col-md-12">
            <div class="row justify-content-center">
              <div class="col-md-10">
                <?php
                $userId = $_SESSION['id'] ?? null;
                if ($userId) {
                  $query = "
                    SELECT 
                      orders.id AS order_id, 
                      CONCAT(orders.firstname, ' ', orders.lastname) AS full_name, 
                      orders.email, 
                      orders.country, 
                      orders.city, 
                      orders.address, 
                      orders.zipcode, 
                      orders.phone,
                      orders.total_price, 
                      GROUP_CONCAT(CONCAT(order_items.item_name, ' (', sizes.size, ') x', order_items.quantity) SEPARATOR '<br>') AS items,
                      orders.status,
                      orders.created_at
                    FROM orders
                    LEFT JOIN order_items ON orders.id = order_items.order_id
                    LEFT JOIN sizes ON order_items.size_id = sizes.size_id
                    WHERE orders.user_id = $userId
                    GROUP BY orders.id";

                    $result = mysqli_query($con, $query);

                  if ($result && $result->num_rows > 0) {
                    echo '<table id="ordersTable" class="table table-bordered table-striped">';
                    echo '<thead>
                            <tr>
                              <th>Order ID</th>
                              <th>Shipping & Contact Details</th>
                              <th>Price</th>
                              <th>Items</th>
                              <th>Status</th>
                              <th>Created At</th>
                            </tr>
                          </thead>
                          <tbody>';
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>
                              <td>{$row['order_id']}</td>
                              <td>
                                <strong>Name:</strong> {$row['full_name']}<br>
                                <strong>Email:</strong> {$row['email']}<br>
                                <strong>Country:</strong> {$row['country']}<br>
                                <strong>City:</strong> {$row['city']}<br>
                                <strong>Address:</strong> {$row['address']}<br>
                                <strong>Zip Code:</strong> {$row['zipcode']}<br>
                                <strong>Phone:</strong> {$row['phone']}
                              </td>
                              <td>{$row['total_price']}$</td>
                              <td>{$row['items']}</td>
                              <td class='text-center'>";
                      if ($row['status'] === 'PENDING') {
                        echo "<button class='btn btn-warning confirm-delivery' data-id='{$row['order_id']}' data-bs-toggle='modal' data-bs-target='#confirmDeliveryModal'>Confirm Delivery</button>";
                      } else {
                        echo "<span class='text-success'>COMPLETED</span>";
                      }
                      echo "</td>
                              <td>{$row['created_at']}</td>
                            </tr>";
                    }
                    echo '</tbody></table>';
                  } else {
                    echo '<p class="text-center">No orders found</p>';
                  }
                 
                } else {
                  echo '<p class="text-center">Please log in to view your orders.</p>';
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <?php include("../includes/footer.php"); ?>
    </footer>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="confirmDeliveryModal" tabindex="-1" aria-labelledby="confirmDeliveryLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeliveryLabel">Confirm Delivery</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to mark this order as delivered?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirmDeliveryButton">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function () {
      const hasData = <?php echo $result && $result->num_rows > 0 ? 'true' : 'false'; ?>;

      if (hasData) {
        $('#ordersTable').DataTable({
          responsive: true,
          paging: true,
          ordering: true,
          info: true
        });
      }

      let selectedOrderId = null;

      $(document).on('click', '.confirm-delivery', function () {
        selectedOrderId = $(this).data('id');
      });

      $('#confirmDeliveryButton').click(function () {
        if (selectedOrderId) {
          const button = $(`button[data-id='${selectedOrderId}']`);

          $.ajax({
            url: './controllers/confirm_delivery.php',
            type: 'POST',
            dataType: 'json',
            data: { order_id: selectedOrderId },
            success: function (response) {
              if (response.status === 'success') {
                button.replaceWith('<span class="text-success">COMPLETED</span>');
                $('#confirmDeliveryModal').modal('hide');
              } else {
                alert('Failed to confirm delivery. Message: ' + response.message);
              }
            },
            error: function () {
              alert('An error occurred. Please try again.');
            },
          });
        }
      });
    });
  </script>
</body>

</html>
<?php
mysqli_close($con);
?>