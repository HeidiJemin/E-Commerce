<?php
require_once('../includes/connect.php');

// user data
$query = "SELECT user_id, username, name, surname, foto, email FROM users WHERE role_id = 1";
$result = mysqli_query($con, $query);
?>

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


    
    <h2 class="text-center">Perdoruesit</h2>
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
                        echo '<img src="../uploads/' . htmlspecialchars($row['foto']) . '" alt="User Photo" class="img-thumbnail">';
                        echo '<br><button class="btn btn-warning btn-sm remove-foto-btn" data-id="' . $row['user_id'] . '" data-bs-toggle="modal" data-bs-target="#confirmRemoveModal">Remove Foto</button>';
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
                        data-email="<?php echo htmlspecialchars($row['email']); ?>"
                        data-foto="<?php echo htmlspecialchars($row['foto']); ?>">
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
    }
    
    ?>
</tbody>
    </table>
    <div class="text-center mt-4">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal">Add New User</button>
</div>

<!-- Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addUserForm" onsubmit="add_user(event);" novalidate>
          <div class="mb-3">
            <label for="addName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="addName" name="add_name" placeholder="Enter your first name">
            <span id="addNameError" class="text-danger small"></span>
          </div>
          <div class="mb-3">
            <label for="addSurname" class="form-label">Surname</label>
            <input type="text" class="form-control" id="addSurname" name="add_surname" placeholder="Enter your surname">
            <span id="addSurnameError" class="text-danger small"></span>
          </div>
          <div class="mb-3">
            <label for="addUsername" class="form-label">Username</label>
            <input type="text" class="form-control" id="addUsername" name="add_username" placeholder="Enter your username">
            <span id="addUsernameError" class="text-danger small"></span>
          </div>
          <div class="mb-3">
            <label for="addEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="addEmail" name="add_email" placeholder="Enter your email">
            <span id="addEmailError" class="text-danger small"></span>
          </div>
          <div class="mb-3">
            <label for="addPassword" class="form-label">Password</label>
            <input type="password" class="form-control" id="addPassword" name="add_password" placeholder="Create password">
            <span id="addPasswordError" class="text-danger small"></span>
          </div>
          <div class="mb-3">
            <label for="addConfPassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="addConfPassword" name="add_conf_password" placeholder="Confirm password">
            <span id="addConfirmPasswordError" class="text-danger small"></span>
          </div>
          <button type="submit" class="btn btn-primary w-100">Add User</button>
        </form>
      </div>
    </div>
  </div>
</div>





    <!-- Edit Modal -->
<div class="modal" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="userId" name="user_id">
                    <input type="hidden" id="current_email" name="current_email">
                    <!-- Hidden field to store the current photo -->
                    <input type="hidden" id="current_foto" name="current_foto">
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
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto(Opsionale)</label>
                        <input type="file" id="foto" name="foto" class="form-control">
                        <span id="fotoError" class="error-message"></span>
                        <br><br>
                        
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

<div class="modal fade" id="confirmRemoveModal" tabindex="-1" aria-labelledby="confirmRemoveLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmRemoveLabel">Confirm Photo Removal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this photo? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmRemoveFoto" class="btn btn-danger">Remove</button>
            </div>
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
        document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('name');
    const surnameInput = document.getElementById('surname');
    const editUserForm = document.getElementById('editUserForm');

    // Function to capitalize the first letter of each word
    function capitalizeFirstLetter(input) {
        return input
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    
    nameInput.addEventListener('input', () => {
        nameInput.value = capitalizeFirstLetter(nameInput.value);
    });

    
    surnameInput.addEventListener('input', () => {
        surnameInput.value = capitalizeFirstLetter(surnameInput.value);
    });

    
    editUserForm.addEventListener('submit', () => {
        nameInput.value = capitalizeFirstLetter(nameInput.value);
        surnameInput.value = capitalizeFirstLetter(surnameInput.value);
    });
});

    $(document).ready(function () {
        
        var table = $('#userTable').DataTable({
        paging: true,
        searching: true,
        info: true,
        order: [[6, 'desc']], 
        responsive: true,
        language: {
            search: "Search Users:",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            },
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            infoEmpty: "No users available",
            zeroRecords: "No records match your search"
        },
        columnDefs: [
            { orderable: false, targets: [4, 6, 7] } 
        ]
    });

    
    if ($('#userTable tbody tr').length === 0) {
        $('#userTable').DataTable().clear().draw();
    }

        
        $(document).on("click", ".edit-btn", function () {
            const id = $(this).data("id");
    const username = $(this).data("username");
    const name = $(this).data("name");
    const surname = $(this).data("surname");
    const email = $(this).data("email"); // current email
    const foto = $(this).data("foto");
    resetModalErrors();
    $("#userId").val(id);
    $("#username").val(username);
    $("#name").val(name);
    $("#surname").val(surname);
    $("#email").val(email);
    $("#current_email").val(email);  // Set the old email to the hidden field
    $("#editModal").modal("show");
            

            
        });
 
        
        $(document).on("click", ".delete-btn", function () {
            const userId = $(this).data("id");
            $("#confirmDelete").data("id", userId);
            $("#deleteModal").modal("show");
        });

       // Confirm deletion
$("#confirmDelete").on("click", function () {
    const userId = $(this).data("id");
    $.ajax({
        url: "./controllers/delete_user.php",  
        type: "POST",
        data: { user_id: userId },
        success: function (response) {
            const data = JSON.parse(response); 
            if (data.status === "success") {
                
                $('#userTable').DataTable().row($(`button[data-id="${userId}"]`).closest('tr')).remove().draw();
                $("#deleteModal").modal("hide"); 
                toastr.success(data.message); 
            } else {
                toastr.error(data.message); 
            }
        },
        error: function () {
            toastr.error("An error occurred while processing the request."); 
        }
    });
});

        
        $(document).on("click", "[data-bs-dismiss=modal]", function () {
            resetModalErrors();
        });

        
$("#editUserForm").on("submit", function (e) {
    e.preventDefault();
    const isValid = validateEditForm();
    if (isValid) {
        const formData = new FormData(this);
        $.ajax({
            url: "./controllers/update_user.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                const data = JSON.parse(response);
                
                // Show success or error message
                if (data.success === true) {
                    toastr.success(data.message);  // Success toastr
                    // Delay page reload to ensure toastr shows up
                    setTimeout(function() {
                        $("#editModal").modal("hide");
                        location.reload();  // Reload the page after a short delay
                    }, 2000);  // delay (2000 ms = 2 seconds)
                } else {
                    toastr.error(data.message);  // Error toastr
                }
            },
            error: function () {
                toastr.error("An error occurred while updating the user.");
            }
        });
    }
});

let userIdToRemove = null; // Store the user ID for removal

// Open the confirmation modal and store the user ID
$(document).on("click", ".remove-foto-btn", function () {
    userIdToRemove = $(this).data("id");
});

// Handle the confirmation of photo removal
$("#confirmRemoveFoto").on("click", function () {
    if (userIdToRemove) {
        $.ajax({
            url: "./controllers/remove_foto.php", // PHP file to handle photo removal
            type: "POST",
            data: { user_id: userIdToRemove },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status === "success") {
                    toastr.success(data.message); // Show success message
                    
                    // Update the table row dynamically
                    const button = $(`.remove-foto-btn[data-id="${userIdToRemove}"]`);
                    const cell = button.closest("td"); // Get the parent cell
                    cell.html("No Photo"); // Replace content with "No Photo"
                    
                    // Reset stored user ID
                    userIdToRemove = null;
                } else {
                    toastr.error(data.message); // Show error message
                }

                // Close the modal
                $("#confirmRemoveModal").modal("hide");
            },
            error: function () {
                toastr.error("An error occurred while trying to remove the photo.");
                $("#confirmRemoveModal").modal("hide");
            }
        });
    }
});


        function validateEditForm() {
            let isValid = true;
    $(".error-message").text("");

    // Validation Regex
    var nameRegex = /^[A-Z][a-zA-Z ]{2,19}$/; // First letter capital, 3-20 chars
    var usernameRegex = /^[a-zA-Z0-9-_]{3,20}$/; // Username rules
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; // Email regex

    // Validate username
    let username = $("#username").val().trim();
    if (username === "" || !usernameRegex.test(username)) {
        $("#usernameError").text(username === "" ? "Username is required." : "Username must be 3-20 characters and can only include letters, numbers, '-' or '_'.");
        isValid = false;
    }

    // Validate name
    let name = $("#name").val().trim();
    if (name === "" || !nameRegex.test(name)) {
        $("#nameError").text(name === "" ? "Name is required." : "Name must start with a capital letter and have at least 3 characters.");
        isValid = false;
    }

    // Validate surname
    let surname = $("#surname").val().trim();
    if (surname === "" || !nameRegex.test(surname)) {
        $("#surnameError").text(surname === "" ? "Surname is required." : "Surname must start with a capital letter and have at least 3 characters.");
        isValid = false;
    }

    // Validate email
    let email = $("#email").val().trim();
    if (email === "" || !emailRegex.test(email)) {
        $("#emailError").text(email === "" ? "Email is required." : "Please enter a valid email address.");
        isValid = false;
    }

    return isValid;
        }

        function resetModalErrors() {
    $(".error-message").text("");
    $(".is-invalid").removeClass("is-invalid");
    $(".is-valid").removeClass("is-valid");
}
    });

    function add_user(event) {
    event.preventDefault(); // Prevent default form submission

    // Get form values from the modal
    var name = $("#addName").val().trim();
    var surname = $("#addSurname").val().trim();
    var username = $("#addUsername").val().trim();
    var email = $("#addEmail").val().trim();
    var password = $("#addPassword").val().trim();
    var confirmPassword = $("#addConfPassword").val().trim();

    // Validation Regex
    var nameRegex = /^[A-Z][a-zA-Z ]{2,19}$/; // First letter capital, 3-20 chars
    var usernameRegex = /^[a-zA-Z0-9-_]{3,20}$/; // Username rules
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; //email regex
    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/; // Strong password

    var error = 0; // Error count

    // Validate name
    if (!nameRegex.test(name)) {
        $("#addName").addClass("is-invalid");
        $("#addNameError").text("Name must start with a capital letter and have at least 3 characters.");
        error++;
    } else {
        $("#addName").removeClass("is-invalid").addClass("is-valid");
        $("#addNameError").text("");
    }

    // Validate surname
    if (!nameRegex.test(surname)) {
        $("#addSurname").addClass("is-invalid");
        $("#addSurnameError").text("Surname must start with a capital letter and have at least 3 characters.");
        error++;
    } else {
        $("#addSurname").removeClass("is-invalid").addClass("is-valid");
        $("#addSurnameError").text("");
    }

    // Validate username
    if (!usernameRegex.test(username)) {
        $("#addUsername").addClass("is-invalid");
        $("#addUsernameError").text("Username must be 3-20 characters and can only include letters, numbers, '-' or '_'.");
        error++;
    } else {
        $("#addUsername").removeClass("is-invalid").addClass("is-valid");
        $("#addUsernameError").text("");
    }

    // Validate email
    if (!emailRegex.test(email)) {
        $("#addEmail").addClass("is-invalid");
        $("#addEmailError").text("Please enter a valid email address.");
        error++;
    } else {
        $("#addEmail").removeClass("is-invalid").addClass("is-valid");
        $("#addEmailError").text("");
    }

    // Validate password
    if (!passwordRegex.test(password)) {
        $("#addPassword").addClass("is-invalid");
        $("#addPasswordError").text("Password must be at least 8 characters, include uppercase, lowercase, number, and special character.");
        error++;
    } else if (password !== confirmPassword) {
        $("#addPassword").addClass("is-invalid");
        $("#addConfPassword").addClass("is-invalid");
        $("#addPasswordError").text("Passwords do not match.");
        $("#addConfirmPasswordError").text("Passwords do not match.");
        error++;
    } else {
        $("#addPassword").removeClass("is-invalid").addClass("is-valid");
        $("#addConfPassword").removeClass("is-invalid").addClass("is-valid");
        $("#addPasswordError").text("");
        $("#addConfirmPasswordError").text("");
    }

    // If no errors, send AJAX request
    if (error === 0) {
        var data = new FormData();
        data.append("name", name);
        data.append("surname", surname);
        data.append("username", username);
        data.append("email", email);
        data.append("password", password);
        data.append("conf_password", confirmPassword);

        // AJAX call to backend
        $.ajax({
            type: "POST",
            url: "./controllers/add_user.php",
            processData: false,
            contentType: false,
            data: data,
            success: function (response) {
                response = JSON.parse(response);
                if (response.success) {
                    toastr.success(response.message); // Show success notification
                    $("#createUserModal").modal("hide"); // Hide the modal

                    setTimeout(function() {
                window.location.reload();
            }, 1000);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }
}

    toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

</script>

