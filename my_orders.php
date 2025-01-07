<?php
session_start();
include('includes/connect.php');
include('functions/common_function.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Commerce Website</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  


  <!-- DataTable CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

  <!-- jQuery and DataTables -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <style>
    @import url("https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap");
.logo {
  width: 7%;
  height: 7%;
}
* {
  margin: 0;
  padding: 0;
  font-family: "Istok Web", sans-serif;
}

body {
  margin: 0;
  padding: 0;
  min-height: 100vh; /* Ensure the body takes at least the full viewport height */
  display: flex;
  flex-direction: column; /* Align children vertically */
  
}

.row {
  min-height: 50px; /* Adjust height as needed */
}

.card-img-top {
  width: 100%;
  height: 200px;
  object-fit: contain;
}

.admin_image {
  width: 100px;
  object-fit: contain;
}

.cart-image {
  width: 80px;
  height: 80px;
  object-fit: contain;
}
/* Custom styling for the black navbar */
.navbar.bg-black {
  background-color: black !important;
  border: none;
}

/* Navbar link styling */
.navbar .nav-link {
  color: white !important; /* White text for navbar links */
}

.navbar .nav-link:hover {
  color: #ffce00 !important; /* Hover color for navbar links */
}

.navbar .nav-link.active {
  color: #ffce00 !important; /* Active link color */
}

/* Toggler icon color */
.navbar-toggler-icon {
  background-color: white; /* White icon color */
}

/* Search form button */
form .btn-outline-light {
  border-color: #fff !important;
  color: white !important;
}

form .btn-outline-light:hover {
  background-color: #ffce00;
  color: white !important;
}
    table.dataTable td, 
    table.dataTable th {
      padding: 5px !important;
      white-space: nowrap;
      text-align: center;
      vertical-align: middle;
    }

    table.dataTable tbody tr td:nth-child(5) {
      white-space: normal;
      text-align: left;
    }

    .confirm-delivery {
      padding: 5px 10px;
      font-size: 12px;
    }

    table {
      width: 80% !important;
      margin: auto;
    }
    footer {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  background-color: black;
  color: white;
  text-align: center;
  padding: 10px 0;
}

html {
  position: relative;
  min-height: 100%;
}

  </style>
</head>

<body>
<div class="container-fluid p-0">
  <!-- Navbar -->
  
    <?php include("./includes/header.php"); ?>
    
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffce00;">
      <ul class="navbar-nav me-auto">
        <?php
          if (!isset($_SESSION['id'])) {
            echo '
              <li class="nav-item ms-3">
                <a class="nav-link" href="#" style="color: black !important;">Guest</a>
              </li>
              <li class="nav-item ms-3">
                <a class="nav-link" href="login.php" style="color: black !important;">Login</a>
              </li>
            ';
          } else {
            echo '
              <li class="nav-item ms-3">
                <a class="nav-link" href="logout.php" style="color: black !important;">Logout</a>
              </li>
              <li class="nav-item ms-3">
                <a class="nav-link" href="profile.php" style="color: black !important;">Profile</a>
              </li>
            ';
          }
        ?>
      </ul>
    </nav>

    <div class="bg-light">
      <h3 class="text-center">Hidden Store</h3>
      <p class="text-center">Welcome to the world of football jerseys</p>
    </div>

    <div class="row px-1">
      <div class="col-md-12">
        <div class="row justify-content-center">
          <div class="col-md-10">
            <!-- Orders Table -->
            <table id="ordersTable" class="table table-bordered table-striped">
              <thead>
                <tr>
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
                            <td>{$row['order_id']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['total_price']}</td>
                            <td>";
                    $items = explode(', ', $row['items']);
                    foreach ($items as $item) {
                      echo $item . "<br>";
                    }
                    echo "</td>
                          <td>";
                    if ($row['status'] === 'PENDING') {
                      echo "<button class='btn btn-warning confirm-delivery' data-id='{$row['order_id']}'>Confirm Delivery</button>";
                    } else {
                      echo "<span class='text-success'>COMPLETED</span>";
                    }
                    echo "</td>
                          <td>{$row['created_at']}</td>
                        </tr>";
                  }
                } else {
                  echo "<tr><td colspan='7'>No orders found</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    </div>

    <!-- Footer -->
     <footer>
    <?php include("./includes/footer.php"); ?>
    </footer>
  </div>

  <!-- Scripts -->
  <script>
    $(document).ready(function () {
      // Initialize DataTable
      $('#ordersTable').DataTable({
        responsive: true,
        paging: true,
        ordering: true,
        info: true
      });

      // Handle Confirm Delivery button click
      $(document).on('click', '.confirm-delivery', function () {
        const orderId = $(this).data('id');
        const button = $(this);

        // Send AJAX request to update the status
        $.ajax({
          url: 'confirm_delivery.php', // Backend endpoint
          type: 'POST',
          data: { order_id: orderId },
          success: function (response) {
            if (response === 'success') {
              button.replaceWith('<span class="text-success">COMPLETED</span>');
            } else {
              alert('Failed to confirm delivery. Please try again.');
            }
          },
          error: function () {
            alert('An error occurred. Please try again.');
          }
        });
      });
    });
  </script>
</body>

</html>
