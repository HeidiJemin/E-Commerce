<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- Include jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<h3 class="text-center text-success">Ligat</h3>

<table id="ligaTable" class="table table-bordered mt-5 display">
  <thead class="bg-info">
    <tr>
      <th>Liga.NO</th>
      <th>Liga Name</th>
      <th>Edit</th>
      <th>Delete</th>
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
      <tr>
        <td><?php echo $number; ?></td>
        <td><?php echo $liga_name; ?></td>
        <td><a href="index.php?edit_liga=<?php echo $liga_id ?>"><i class="fa-solid fa-pen-to-square"></i></a></td>
        <td><a href="index.php?delete_liga=<?php echo $liga_id ?>"><i class="fa-solid fa-trash"></i></a></td>
      </tr>
    <?php
    }
    ?>
  </tbody>
</table>

<!-- Initialize DataTable -->
<script>
  $(document).ready(function () {
    $('#ligaTable').DataTable({
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
  });
</script>

