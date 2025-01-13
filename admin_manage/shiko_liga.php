<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- Include jQuery, Bootstrap, and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<h3 class="text-center text-success">Ligat</h3>

<table id="ligaTable" class="table table-bordered mt-5 display">
  <thead class="bg-info">
    <tr>
      <th class="text-center">Liga.NO</th>
      <th class="text-center">Liga Name</th>
      <th class="text-center">Edit</th>
      <th class="text-center">Delete</th>
    </tr>
  </thead>
  <tbody class="bg-secondary text-light">
    <?php
    $number = 0;
    $select_liga = "SELECT * FROM `liga`";
    $result = mysqli_query($con, $select_liga);
    while ($row = mysqli_fetch_assoc($result)) {
        $liga_id = $row['liga_id'];
        $liga_name = $row['liga_name'];
        $number++;
    ?>
      <tr id="liga-<?php echo $liga_id; ?>">
        <td class="text-center"><?php echo $number; ?></td>
        <td class="text-center"><?php echo $liga_name; ?></td>
        <td class="text-center">
          <!-- Edit button with yellow style -->
          <a href="index.php?edit_liga=<?php echo $liga_id ?>" class="btn btn-warning btn-sm">
            <i class="fa-solid fa-pen-to-square"></i> Edit
          </a>
        </td>
        <td class="text-center">
          <!-- Delete button with red style -->
          <button class="btn btn-danger btn-sm delete-liga" data-id="<?php echo $liga_id; ?>">
            <i class="fa-solid fa-trash"></i> Delete
          </button>
        </td>
      </tr>
    <?php
    }
    mysqli_close($con);
    ?>
  </tbody>
</table>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this Liga? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    var table = $('#ligaTable').DataTable({
      paging: true,
      searching: true,
      info: true,
      responsive: true, // Makes table responsive
      language: {
        search: "Search Liga:",
        paginate: {
          first: "First",
          last: "Last",
          next: "Next",
          previous: "Previous"
        }
      }
    });

    let ligaIdToDelete = null; // Variable to store the ID of the Liga to be deleted

    // Handle delete button click (show modal)
    $(document).on('click', '.delete-liga', function () {
      ligaIdToDelete = $(this).data('id');
      $('#deleteConfirmationModal').modal('show'); // Show the confirmation modal
    });

    // Handle confirmation button click (AJAX delete)
    $('#confirmDeleteButton').click(function () {
      if (ligaIdToDelete) {
        $.ajax({
          url: 'delete_liga.php',  // PHP file to handle deletion
          type: 'POST',
          data: { liga_id: ligaIdToDelete },
          success: function (response) {
            try {
              const data = JSON.parse(response); // Parse the response to JSON

              if (data.success) {
                // Show success toast
                const toast = document.createElement('div');
                toast.style.position = 'fixed';
                toast.style.bottom = '20px';
                toast.style.right = '20px';
                toast.style.backgroundColor = '#28a745';
                toast.style.color = '#fff';
                toast.style.padding = '10px 20px';
                toast.style.borderRadius = '5px';
                toast.style.boxShadow = '0px 0px 10px rgba(0,0,0,0.2)';
                toast.textContent = data.message;
                document.body.appendChild(toast);

                // Remove the toast after 3 seconds
                setTimeout(() => toast.remove(), 3000);

                // Remove the deleted row from the DataTable
                table.row('#liga-' + ligaIdToDelete).remove().draw();
              } else {
                // Show error toast
                const toast = document.createElement('div');
                toast.style.position = 'fixed';
                toast.style.bottom = '20px';
                toast.style.right = '20px';
                toast.style.backgroundColor = '#dc3545';
                toast.style.color = '#fff';
                toast.style.padding = '10px 20px';
                toast.style.borderRadius = '5px';
                toast.style.boxShadow = '0px 0px 10px rgba(0,0,0,0.2)';
                toast.textContent = data.message;
                document.body.appendChild(toast);

                // Remove the toast after 3 seconds
                setTimeout(() => toast.remove(), 3000);
              }
            } catch (e) {
              alert('Error: Invalid response from the server');
            }
          },
          error: function () {
            alert('There was an error processing your request.');
          },
          complete: function () {
            // Close the modal after the deletion
            $('#deleteConfirmationModal').modal('hide');
          }
        });
      }
    });
  });
</script>
