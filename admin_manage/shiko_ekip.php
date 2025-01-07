<h3 class="text-center text-success">Ekipet</h3>

<table id="ekipTable" class="table table-bordered mt-5 display">
  <thead class="bg-info">
    <tr>
      <th>Ekip.NO</th>
      <th>Ekip Name</th>
      <th>Ekip Liga</th>
      <th>Edit</th>
      <th>Delete</th>
    </tr>
  </thead>
  <tbody class="bg-secondary text-light">
    <?php
    $number = 0;

    // Modify the query to join 'ekip' and 'liga' tables
    $select_ekip = "
      SELECT e.ekip_id, e.ekip_name, l.liga_name 
      FROM `ekip` e 
      JOIN `liga` l ON e.liga_id = l.liga_id
    ";
    $result = mysqli_query($con, $select_ekip);

    while ($row = mysqli_fetch_assoc($result)) {
        $ekip_id = $row['ekip_id'];
        $ekip_name = $row['ekip_name'];
        $liga_name = $row['liga_name']; // Fetch the liga name
        $number++;
    ?>
      <tr>
        <td><?php echo $number; ?></td>
        <td><?php echo $ekip_name; ?></td>
        <td><?php echo $liga_name; ?></td> <!-- Display liga name -->
        <td><a href="index.php?edit_ekip=<?php echo $ekip_id ?>"><i class="fa-solid fa-pen-to-square"></i></a></td>
        <td>
          <button 
            class="btn btn-danger btn-sm delete-btn" 
            data-id="<?php echo $ekip_id; ?>" 
            data-name="<?php echo $ekip_name; ?>">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>
      </tr>
    <?php
    }
    ?>
  </tbody>
</table>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete <strong id="ekipName"></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="confirmDelete" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Initialize DataTable and Modal Logic -->
<script>
  $(document).ready(function () {
    // Initialize DataTable
    $('#ekipTable').DataTable({
      paging: true,
      searching: true,
      info: true,
      responsive: true,
      language: {
        search: "Search Ekip:",
        paginate: {
          first: "First",
          last: "Last",
          next: "Next",
          previous: "Previous"
        }
      }
    });

    // Handle delete button click
    $(document).on("click", ".delete-btn", function () {
      const ekipId = $(this).data("id");
      const ekipName = $(this).data("name");
      $("#ekipName").text(ekipName); // Display ekip name in modal
      $("#confirmDelete").data("id", ekipId); // Store ekip ID in confirm button
      $("#deleteModal").modal("show");
    });

    // Confirm deletion
    $("#confirmDelete").on("click", function () {
      const ekipId = $(this).data("id");
      $.ajax({
        url: "delete_ekip.php",
        type: "POST",
        data: { ekip_id: ekipId },
        success: function (response) {
          alert("Ekip deleted successfully!");
          $("#deleteModal").modal("hide");
          location.reload();
        },
        error: function () {
          alert("An error occurred while deleting the ekip.");
        }
      });
    });
  });
</script>
