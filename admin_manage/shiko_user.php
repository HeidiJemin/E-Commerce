<?php
include('../includes/connect.php');

// Fetch user data query
$query = "SELECT user_id, username, name, surname, foto, email FROM users";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        table#userTable {
            border-collapse: collapse;
            width: 100%;
        }
        .error-message {
            color: red;
            font-size: 12px;
        }
        table#userTable th, table#userTable td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;
        }
        table#userTable th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        table#userTable img {
            display: block;
            margin: 0 auto;
            width: 50px;
            height: 50px;
        }
        table#userTable tbody tr:hover {
            background-color: #f5f5f5;
        }
        </style>
</head>
<body>
    
    <h2 class="text-center">All Users</h2>
    <table id="userTable" class="display">
        <thead>
            <tr>
                <th>User No</th>
                <th>Username</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Photo</th>
                <th>Email</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if (mysqli_num_rows($result) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['surname']); ?></td>
                <td>
                    <?php 
                    // Check if the 'foto' field is empty or not
                    if (empty($row['foto'])) {
                        echo "No Photo"; // Display "No Photo" if no photo exists
                    } else {
                        echo '<img src="../user/' . htmlspecialchars($row['foto']) . '" alt="User Photo">';
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                    <button 
                        class="btn btn-primary btn-sm edit-btn" 
                        data-id="<?php echo $row['user_id']; ?>" 
                        data-username="<?php echo htmlspecialchars($row['username']); ?>" 
                        data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                        data-surname="<?php echo htmlspecialchars($row['surname']); ?>" 
                        data-email="<?php echo htmlspecialchars($row['email']); ?>">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                </td>
                <td>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['user_id']; ?>">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>No users found</td></tr>";
    }
    mysqli_close($con);
    ?>
</tbody>
    </table>

    <!-- Edit Modal -->
    <div class="modal" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editUserForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="userId" name="user_id">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control">
                            <span id="usernameError" class="error-message"></span>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control">
                            <span id="nameError" class="error-message"></span>
                        </div>
                        <div class="mb-3">
                            <label for="surname" class="form-label">Surname</label>
                            <input type="text" id="surname" name="surname" class="form-control">
                            <span id="surnameError" class="error-message"></span>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control">
                            <span id="emailError" class="error-message"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmDelete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        // Initialize DataTable with column definitions
        $('#userTable').DataTable({
            columnDefs: [
                { orderable: false, targets: [4, 6, 7] } // Disable sorting for "Photo", "Edit", and "Delete" columns
            ]
        });

        // Populate edit modal
        $(document).on("click", ".edit-btn", function () {
            const id = $(this).data("id");
            const username = $(this).data("username");
            const name = $(this).data("name");
            const surname = $(this).data("surname");
            const email = $(this).data("email");
            resetModalErrors();
            $("#userId").val(id);
            $("#username").val(username);
            $("#name").val(name);
            $("#surname").val(surname);
            $("#email").val(email);
            $("#editModal").modal("show");
        });

        // Handle delete button click
        $(document).on("click", ".delete-btn", function () {
            const userId = $(this).data("id");
            $("#confirmDelete").data("id", userId);
            $("#deleteModal").modal("show");
        });

       // Confirm deletion
$("#confirmDelete").on("click", function () {
    const userId = $(this).data("id");
    $.ajax({
        url: "delete_user.php",  // PHP file to handle deletion
        type: "POST",
        data: { user_id: userId },
        success: function (response) {
            const data = JSON.parse(response); // Parse the JSON response
            if (data.status === "success") {
                // Find the row to delete and remove it from the DataTable
                $('#userTable').DataTable().row($(`button[data-id="${userId}"]`).closest('tr')).remove().draw();
                $("#deleteModal").modal("hide"); // Close the delete modal
                toastr.success(data.message); // Show success message using Toastr
            } else {
                toastr.error(data.message); // Show error message using Toastr
            }
        },
        error: function () {
            toastr.error("An error occurred while processing the request."); // Show error notification if AJAX fails
        }
    });
});

        // Clear errors when closing modals
        $(document).on("click", "[data-bs-dismiss=modal]", function () {
            resetModalErrors();
        });

        // Validate and submit edit form
        $("#editUserForm").on("submit", function (e) {
            e.preventDefault();
            const isValid = validateEditForm();
            if (isValid) {
                const formData = new FormData(this); // Use FormData for flexibility
                $.ajax({
                    url: "update_user.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert("User updated successfully!");
                        $("#editModal").modal("hide");
                        location.reload();
                    },
                    error: function () {
                        alert("An error occurred while updating the user.");
                    }
                });
            }
        });

        function validateEditForm() {
            let isValid = true;
            $(".error-message").text("");
            if ($("#username").val().trim() === "") {
                $("#usernameError").text("Username is required.");
                isValid = false;
            }
            if ($("#name").val().trim() === "") {
                $("#nameError").text("Name is required.");
                isValid = false;
            }
            if ($("#surname").val().trim() === "") {
                $("#surnameError").text("Surname is required.");
                isValid = false;
            }
            if ($("#email").val().trim() === "") {
                $("#emailError").text("Email is required.");
                isValid = false;
            }
            return isValid;
        }

        function resetModalErrors() {
            $(".error-message").text("");
        }
    });
</script>

</body>
</html>

