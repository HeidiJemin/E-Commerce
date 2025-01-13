<?php
include_once('../includes/connect.php');
include_one('functions/common_function.php');
// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    // If the user is not logged in, redirect to login page
    header("Location: login.php");
    exit();
} elseif ($_SESSION['role_id'] == 1) {
    
    exit();
} else {
    // If the user is logged in but does not have role_id = 1, redirect to admin page
    header("Location: admin_manage/index.php");
    exit();
}
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
  <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">


  <!-- jQuery and DataTables -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  

  <style>
   
.logo {
  width: 7%;
  height: 7%;
}
* {
  margin: 0;
  padding: 0;
  font-family: "Istok Web", sans-serif;
}
html, body {
  height: 100%; /* Ensure the page height fills the viewport */
  margin: 0; /* Remove default margin */
  display: flex;
  flex-direction: column; /* Set the layout to column direction */
}

.container-fluid {
  flex: 1; /* Allow this container to take up the remaining space */
}

footer {
  background-color: black;
  color: white;
  text-align: center;
  padding: 10px 0;
  flex-shrink: 0;
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
  </style>
</head>

<body>
  <div class="container-fluid p-0">
    <!-- Navbar -->
    <?php include("../includes/header.php"); ?>

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
              echo '<table id="ordersTable" class="table table-bordered table-striped">';
              echo '
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
                <tbody>';
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
              echo '</tbody></table>';
            } else {
              echo '
                <table id="emptyOrdersTable" class="table table-bordered">
                  <tr><td colspan="7" class="text-center">No orders found</td></tr>
                </table>
              ';
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <?php include("../includes/footer.php"); ?>
  </footer>

  <!-- Scripts -->
  <script>
    $(document).ready(function () {
      const hasData = <?php echo $result->num_rows > 0 ? 'true' : 'false'; ?>;

      if (hasData) {
        $('#ordersTable').DataTable({
          responsive: true,
          paging: true,
          ordering: true,
          info: true
        });
      }

      $(document).on('click', '.confirm-delivery', function () {
        const orderId = $(this).data('id');
        const button = $(this);

        $.ajax({
          url: 'confirm_delivery.php',
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
<?php
// Close the database connection
mysqli_close($con);
?>