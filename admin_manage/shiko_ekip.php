<h3 class="text-center text-success">Ekipet</h3>

<table id="ekipTable" class="table table-bordered mt-5 display">
  <thead class="bg-info">
    <tr>
      <th class="text-center">Ekip.NO</th>
      <th class="text-center">Ekip Name</th>
      <th class="text-center">Ekip Liga</th>
      <th class="text-center">Edit</th>
      <th class="text-center">Delete</th>
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
      <tr id="ekip-<?php echo $ekip_id; ?>">
        <td class="text-center"><?php echo $number; ?></td>
        <td class="text-center"><?php echo $ekip_name; ?></td>
        <td class="text-center"><?php echo $liga_name; ?></td> <!-- Display liga name -->
        <td class="text-center">
          <a href="index.php?edit_ekip=<?php echo $ekip_id ?>" class="btn btn-warning btn-sm">
            <i class="fa-solid fa-pen-to-square"></i> Edit
          </a>
        </td>
        <td class="text-center">
          <button 
            class="btn btn-danger btn-sm delete-btn" 
            data-id="<?php echo $ekip_id; ?>" 
            data-name="<?php echo $ekip_name; ?>">
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


<script>
  $(document).ready(function () {
    
    var table = $('#ekipTable').DataTable({
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
        },
        infoEmpty: "No Ekip available", 
        zeroRecords: "No records match your search", 
      }
    });

    
    $(document).on("click", ".delete-btn", function () {
      const ekipId = $(this).data("id");
      const ekipName = $(this).data("name");
      $("#ekipName").text(ekipName); 
      $("#confirmDelete").data("id", ekipId); 
      $("#deleteModal").modal("show");
    });

    // Confirm deletion
    $("#confirmDelete").on("click", function () {
      const ekipId = $(this).data("id");
      $.ajax({
        url: "./controllers/delete_ekip.php",  
        type: "POST",
        data: { ekip_id: ekipId },
        success: function (response) {
          try {
            const data = JSON.parse(response); 

            if (data.success) {
              
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

              
              setTimeout(() => toast.remove(), 3000);

              
              table.row('#ekip-' + ekipId).remove().draw();
            } else {
              
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
          
          $('#deleteModal').modal('hide');
        }
      });
    });
  });
</script>
